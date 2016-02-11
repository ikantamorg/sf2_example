<?php

namespace Domain\CoreBundle\Service\Payment;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;
use Domain\CoreBundle\Entity\Appointment;
use Domain\CoreBundle\Entity\Transaction;
use iKantam\UtilsBundle\Utils\ArrayUtils;

/**
 * Class PaymentService
 * @package Domain\CoreBundle\Service\Payment
 */
class PaymentService
{
    const SUCCESS = 'SUCCESS';
    const MATCH_CRITERIA = 'NAME';
    const ACTION_TYPE_PAY = 'PAY';
    const ACTION_TYPE_PAY_PRIMARY = 'PAY_PRIMARY';
    const REFUNDED = 'REFUNDED';

    const LIVE_REDIRECT_URL = 'https://ipnpb.paypal.com/cgi-bin/webscr&cmd=_ap-payment&paykey=';
    const SANDBOX_REDIRECT_URL = 'https://www.sandbox.paypal.com/cgi/webscr&cmd=_ap-payment&paykey=';

    protected $container;
    protected $manager;
    protected $admin = [];
    protected $config;
    protected $paypalConfig = [];

    // Fields, that must be set in admin
    protected $required = [
        'paypal_email',
        'fee_percent',
        'currency_code',
        'memo',
        'language',
        'mode',
        'acct1.UserName',
        'acct1.Password',
        'acct1.Signature',
        'acct1.AppId',
    ];

    protected $lastError = '';

    /**
     * Inject container and entity manager into service
     * Load config for service
     * Load and prepare for usage admin settings from database
     * If there is no any required admin settings - service should not continue working
     */
    public function __construct(ContainerInterface $container, EntityManager $manager)
    {
        $this->container = $container;
        $this->manager = $manager;
        $this->config = $container->getParameter('payment');

        $settings = $this->manager->getRepository('CoreBundle:AdminSetting')->findPaymentSettings();
        foreach ($settings as $setting) {
            $this->admin[$setting->getLabel()] = $setting->getValue();
        }
        $this->admin = ArrayUtils::unprefix($this->admin, 'payment.');

        $empty = array_diff_key(array_flip($this->required), $this->admin);
        if (count($empty)) {
            throw new \Exception(
                'Site admin must set the following payment settings: "'
                . implode('" ,"', array_flip($empty)) . '"'
            );
        }

        $this->paypalConfig = ArrayUtils::extract($this->admin, [
            'mode', 'acct1.UserName', 'acct1.Password', 'acct1.Signature', 'acct1.AppId',
        ]);
    }

    /**
     * Set error occured during any request
     * 
     * @param string $message
     */
    public function setLastError($message)
    {
        $this->lastError = $message;
        return false;
    }

    /**
     * Return the last error
     * 
     * @return string
     */
    public function getLastError()
    {
        return $this->lastError;
    }


    /**
     * Clear last error
     */
    public function clearLastError()
    {
        $this->lastError = '';
    }

    /**
     * If there were any errors during last request
     * 
     * @return bool
     */
    public function success()
    {
        return strlen($this->lastError) <= 0;
    }

    /**
     * Set last error updating the transaction status
     * Transaction should be already persisted by entity manager
     * 
     * @param \Domain\CoreBundle\Entity\Transaction $transaction
     * @param string $status
     * @param string $error
     * @return \Domain\CoreBundle\Entity\Transaction $transaction
     */
    public function setTransactionLastError($transaction, $error = '')
    {
        $transaction->setMessage($error);
        $this->manager->flush();
        return $this->setLastError($error);
    }

    /**
     * Create payment
     * 
     * @param \Domain\CoreBundle\Entity\Appoinment $transaction $appointment
     * @param array $options - pass additional options
     *      [
     *      'successUrl' => ''
     *      'cancelUrl' => ''
     *      ]
     * @return \Domain\CoreBundle\Entity\Transaction
     */
    public function initiateTransaction(Appointment $appointment, $options = [])
    {

        // TEMPORARY CODE
        $request = $this->container->get('request');
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBaseURL();
        if (empty($options['successUrl'])) {
            $options['successUrl'] = $baseUrl;
        }

        if (empty($options['cancelUrl'])) {
            $options['cancelUrl'] = $baseUrl;
        }

        // TEMPORARY CODE

        $this->clearLastError();
        $error = '';

        $expert = $appointment->getExpert();
        if (! $expert) {
            return $this->setLastError('Expert for appointment not found');
        }
        $candidate = $appointment->getCandidate();
        if (! $candidate) {
            return $this->setLastError('Candidate for appointment not found');
        }

        if ($transaction = $appointment->getTransaction()) {
            $transaction->setAttempt($transaction->getAttempt() + 1);
        } else {
            $transaction = new Transaction;
            $transaction->setExpert($expert);
            $transaction->setCandidate($candidate);
            $appointment->setTransaction($transaction);
            $this->manager->persist($appointment);
        }

        $amount = $expert->getPrice();
        if (! $amount) {
            return $this->getLastError('Expert price not set or is invalid');
        }

        $amountToAdmin = round($amount / 100 * floatval($this->admin['fee_percent']), 2);
        $amountToExpert = round($amount - $amountToAdmin, 2);

        $transaction->setAmountToAdmin($amountToAdmin);
        $transaction->setAmountToExpert($amountToExpert);
        $transaction->setCurrency($this->admin['currency_code']);

        $this->manager->persist($transaction);

        try {

            // get first billing paypal email address for expert
            $expertBilling = $this->manager->getRepository('CoreBundle:ExpertBilling')
                ->findOneActiveByExpert($expert);

            if (! $expertBilling || ! $expertBilling->getEmail()) {
                throw new \Exception('Expert has no active billing');
            }

            // configure receivers: one is site admin and another is expert

            $receivers = [];

            // primary receiver is site admin, he will get money by the end of this operation
            $primaryReciever = new \Receiver();
            $primaryReciever->email = $this->admin['paypal_email'];
            $primaryReciever->amount = $amount;
            $primaryReciever->primary = true;
            $receivers[] = $primaryReciever;

            // expert is a secondary receiver, he will get money when particular event occures
            $secondaryReceiver = new \Receiver();
            $secondaryReceiver->email = $expertBilling->getEmail();
            $secondaryReceiver->amount = $amountToExpert;
            $secondaryReceiver->primary = false;
            $receivers[] = $secondaryReceiver;

            $cancelUrl = $this->getAbsoluteRedirectUrl($this->config['route_cancel']);
            $cancelUrl = ArrayUtils::get($options, 'cancelUrl', $cancelUrl);

            $successUrl = $this->getAbsoluteRedirectUrl($this->config['route_success']);
            $successUrl = ArrayUtils::get($options, 'successUrl', $successUrl);

            // create request object
            /*$payRequest = new \PayRequest(
                new \RequestEnvelope($this->admin['language']),
                static::ACTION_TYPE_PAY_PRIMARY,
                $cancelUrl,
                $this->admin['currency_code'],
                new \ReceiverList($receivers),
                $successUrl
            );

            $payRequest->ipnNotificationUrl = $this->getAbsoluteRedirectUrl($this->config['route_ipn']);
            $payRequest->memo = $this->admin['memo'];*/

            // create service for adaptive payment (chained delayed will be used)
            //$service = new \AdaptivePaymentsService($this->paypalConfig);

            //$response = $service->Pay($payRequest);

        } catch (\PPConnectionException $e) {
            $error = 'Error connecting to ' . $e->getUrl();
        } catch (\PPConfigurationException $e) {
            $error = 'Error at ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage();
        } catch (\PPInvalidCredentialException $e) {
            $error = $e->errorMessage();
        } catch (\PPMissingCredentialException $e) {
            $error = $e->errorMessage();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        /*if (strlen($error)) {
            return $this->setTransactionLastError($transaction, $error);
        }

        $ack = strtoupper($response->responseEnvelope->ack);
        if ($ack !== static::SUCCESS) {
            return $this->setTransactionLastError($transaction, $response->error[0]->message);
        }*/


        $this->payKey = 'test_' . rand(0, 100000);
        // $payKey = $response->payKey;

        $transaction->setPayKey($this->payKey);
        $transaction->updateStatus(Transaction::STATUS_WAITING);
        $this->manager->flush();

        return true;
    }

    /**
     * Refund payment
     * 
     * @param \Domain\CoreBundle\Entity\Appointment $appointment
     * @return bool
     */
    public function refundTransaction(Appointment $appointment)
    {
        $this->clearLastError();
        $error = '';

        $transaction = $appointment->getTransaction();
        if (! $transaction) {
            return $this->setLastError('Transaction for appointment not found');
        }

        try {

            $this->manager->persist($transaction);

            if ($transaction->getStatus() !== Transaction::STATUS_PENDING) {
                throw new \Exception('Can not refund unverified transaction.');
            }

            if (! $transaction->getPayKey()) {
                throw new \Exception('Transaction has no pay key. Failed to refund');
            }

            $refundRequest = new \RefundRequest(new \RequestEnvelope($this->admin['language']));
            $refundRequest->currencyCode = $this->admin['currency_code'];
            $refundRequest->payKey = $transaction->getPayKey();

            $service = new \AdaptivePaymentsService($this->paypalConfig);

            if (! $transaction) {
                throw new \Exception('Transaction for appointment not found');
                
            }

            $response = $service->Refund($refundRequest);

        } catch (\PPConnectionException $e) {
            $error = 'Error connecting to ' . $e->getUrl();
        } catch (\PPConfigurationException $e) {
            $error = 'Error at ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage();
        } catch (\PPInvalidCredentialException $e) {
            $error = $e->errorMessage();
        } catch (\PPMissingCredentialException $e) {
            $error = $e->errorMessage();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (strlen($error)) {
            return $this->setTransactionLastError($transaction, $error);
        }

        $ack = strtoupper($response->responseEnvelope->ack);
        if ($ack !== static::SUCCESS) {
            return $this->setTransactionLastError($transaction, $response->error[0]->message);
        }
        if (empty($response->refundInfoList->refundInfo[0]->refundStatus)) {
            return $this->setTransactionLastError($transaction, 'Invalud responce');
        }

        $refundStatus = strtoupper($response->refundInfoList->refundInfo[0]->refundStatus);
        if ($refundStatus !== self::REFUNDED) {
            return $this->setTransactionLastError($transaction, 'Invalud refund status - '.$refundStatus);
        }

        $transaction->updateStatus(Transaction::STATUS_REFUNDED);
        $this->manager->flush();

        return true;
    }

    /**
     * Execute payment
     * 
     * @param \Domain\CoreBundle\Entity\Appointment $appointment
     * @return bool
     */
    public function completeTransaction(Appointment $appointment)
    {
        $this->clearLastError();
        $error = '';

        $transaction = $appointment->getTransaction();
        if (! $transaction) {
            return $this->setLastError('Transaction for appointment not found');
        }

        try {

            $this->manager->persist($transaction);

            if ($transaction->getStatus() !== Transaction::STATUS_PENDING) {
                throw new \Exception('Can not complete unverified transaction.');
            }

            $executePaymentRequest = new \ExecutePaymentRequest(
                new \RequestEnvelope($this->admin['language']),
                $transaction->getPayKey()
            );
            $executePaymentRequest->actionType = static::ACTION_TYPE_PAY;

            $service = new \AdaptivePaymentsService($this->paypalConfig);

            $response = $service->ExecutePayment($executePaymentRequest);

        } catch (\PPConnectionException $e) {
            $error = 'Error connecting to ' . $e->getUrl();
        } catch (\PPConfigurationException $e) {
            $error = 'Error at ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage();
        } catch (\PPInvalidCredentialException $e) {
            $error = $e->errorMessage();
        } catch (\PPMissingCredentialException $e) {
            $error = $e->errorMessage();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (strlen($error)) {
            return $this->setTransactionLastError($transaction, $error);
        }
        
        $ack = strtoupper($response->responseEnvelope->ack);
        if ($ack !== static::SUCCESS) {
            return $this->setTransactionLastError($transaction, $response->error[0]->message);
        }

        $transaction->updateStatus(Transaction::STATUS_COMPLETED);
        $this->manager->flush();

        $this->container->get('domain.core.event.listener')->dispatch('appointment_payment_completed', '', $appointment);

        return true;
    }

    /**
     * Verify transaction
     * 
     * @return bool
     */
    public function verifyTransaction()
    {
        $this->clearLastError();

        /*$ipnMessage = new \PPIPNMessage(null, $this->paypalConfig);
        
        $ipnData = $ipnMessage->getRawData();
        $ipnDataEncoded = json_encode($ipnData);*/

        if (! isset($ipnData['pay_key'])) {
            //1return $this->setLastError('No pay key found in IPN response');
        }

        #$payKey = $ipnData['pay_key'];
        $payKey = $this->payKey;

        $transaction = $this->manager->getRepository('CoreBundle:Transaction')
            ->findOneByPayKey($payKey);
        if (! $transaction) {
            return $this->setLastError('No transaction found for pay key');
        }

        $this->manager->persist($transaction);

        $status = $transaction->getStatus();

/*        if (! $ipnMessage->validate()) {
            if ($status === Transaction::STATUS_WAITING) {
                return $this->setTransactionLastError($transaction, $ipnDataEncoded);
            }
            return $this->setTransactionLastError($transaction, 'VERIFICATION NOT VALID FOR UNKNOWN STATUS');
        }*/

        if ($status !== Transaction::STATUS_WAITING) {
            return $this->setTransactionLastError($transaction, 'VERIFICATION IS VALID BUT WITH UNKNOWN STATUS');
        }

        $ipnDataEncoded = 'sdsd';
        $transaction->updateStatus(Transaction::STATUS_PENDING);
        $transaction->setDetails($ipnDataEncoded);

        $this->manager->flush();

        if (($appointment = $transaction->getAppointment())) {
            $this->container->get('domain.appointment.manager')
                ->unsetActor()
                ->setAppointment($appointment)
                ->confirmPayment()
            ;
        }

        return true;
    }

    /**
     * Verify PayPal account by email, first name and last name
     * 
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     * @return bool
     */
    public function verifyAccount($email, $firstName, $lastName)
    {
        $this->clearLastError();
        $error = '';

        if (! ($email && $firstName && $lastName)) {
            return $this->setLastError('One or more user fields is empty');
        }

        try {

            $getVerifiedStatus = new \GetVerifiedStatusRequest;

            $accountIdentifier = new \AccountIdentifierType;
            $accountIdentifier->emailAddress = $email;

            $getVerifiedStatus->accountIdentifier = $accountIdentifier;
            $getVerifiedStatus->firstName = $firstName;
            $getVerifiedStatus->lastName = $lastName;
            $getVerifiedStatus->matchCriteria = static::MATCH_CRITERIA;

            $service  = new \AdaptiveAccountsService($this->paypalConfig);

            $response = $service->GetVerifiedStatus($getVerifiedStatus);

        } catch (\PPConnectionException $e) {
            $error = 'Error connecting to ' . $e->getUrl();
        } catch (\PPConfigurationException $e) {
            $error = 'Error at ' . $e->getLine() . ' in ' . $e->getFile() . ': ' . $e->getMessage();
        } catch (\PPInvalidCredentialException $e) {
            $error = $e->errorMessage();
        } catch (\PPMissingCredentialException $e) {
            $error = $e->errorMessage();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (strlen($error)) {
            return $this->setLastError($error);
        }

        $ack = strtoupper($response->responseEnvelope->ack);
        if ($ack !== static::SUCCESS) {
            $error = $response->error[0]->message;
            return $this->setLastError($error);
        }

        return true;
    }

    /**
     * Create absolute url for route
     * 
     * @param string $route
     * @return string
     */
    protected function getAbsoluteRedirectUrl($route)
    {
        $request = $this->container->get('request');
        $domain = $request->getScheme() . '://' . $request->getHttpHost();
        $uri = $this->container->get('router')->generate($route);
        $url = $domain . $uri;
        return $url;
    }

    /**
     * Generate Paypal redirect url for transaction using it's pay key
     * Return false if transaction has no pay key
     * 
     * @param \Domain\CoreBundle\Entity\Appointment $appointment
     * @return mixed (string|bool)
     */
    public function getPaypalRedirectUrl(Appointment $appointment)
    {
        $transaction = $appointment->getTransaction();
        if (!$transaction) {
            return false;
        }
        if ($key = $transaction->getPayKey()) {
            $url = ($this->paypalConfig['mode'] === 'sandbox')
                ? static::SANDBOX_REDIRECT_URL
                : static::LIVE_REDIRECT_URL;
            $url .= $key;
            return $url;
        }
        return false;
    }


}
