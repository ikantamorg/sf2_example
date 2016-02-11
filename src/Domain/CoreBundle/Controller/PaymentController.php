<?php

namespace Domain\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class PaymentController extends CoreController
{
    /**
     * REQUIRED Public function to catch IPN
     */
    public function ipnAction(Request $request)
    {
        $logger = $this->get('logger');
        $logger->info('Ipaypal -'.$request->getSchemeAndHttpHost().' ---real port: '.$request->getPort().' --is secure - '.($request->isSecure() ? 'true': 'false'."\r\n"));
        $logger->info(implode("\r\n", apache_request_headers()));

        $paymentService = $this->get('payment');
        $paymentService->verifyTransaction();
        exit;
    }

    /**
     * Set proper success route in payment service config
     */
    public function successAction()
    {
        throw $this->createNotFoundException('Please, set "success" route in Payment service config');
    }

    /**
     * Set proper cancel rount in payment service config
     */
    public function cancelAction()
    {
        throw $this->createNotFoundException('Please, set "cancel" route in Payment service config');
    }

    /**
     * Example of payment initiation action
     * Set access property to "public" for test
     */
    protected function initiateAction()
    {
        $manager = $this->getDoctrine()->getManager();

        $appointment = $manager->find('CoreBundle:Appointment', 2);
        if (! $appointment) {
            throw new \Exception('No appointment');
        }

        $paymentService = $this->get('payment');

        $paymentService->initiateTransaction($appointment);

        if ($paymentService->success()) {
            $url = $paymentService->getPaypalRedirectUrl($appointment);
            echo 'Transaction initiated. Url: ' . $url;
        } else {
            throw new \Exception($paymentService->getLastError());
        }
    }

    /**
     * Example of refund action
     * Set access property to "public" for test
     */
    protected function refundAction()
    {
        $manager = $this->getDoctrine()->getManager();
        
        $appointment = $manager->find('CoreBundle:Appointment', 2);
        if (! $appointment) {
            throw new \Exception('No appointment');
        }

        $paymentService = $this->get('payment');

        $paymentService->refundTransaction($appointment);

        if ($paymentService->success()) {
            echo 'Refund completed';
            // refund succesfully completed, wait for IPN verification
        } else {
            throw new \Exception($paymentService->getLastError());
        }
    }

    /**
     * Example of complete action
     * Set access property to "public" for test
     */
    protected function completeAction()
    {
        $manager = $this->getDoctrine()->getManager();

        $appointment = $manager->find('CoreBundle:Appointment', 2);
        if (! $appointment) {
            throw new \Exception('No appointment');
        }

        $paymentService = $this->get('payment');

        $paymentService->completeTransaction($appointment);
        if ($paymentService->success()) {
            echo 'Transaction completed';
            // transaction succesfully completed, wait for IPN verification
        } else {
            throw new \Exception($paymentService->getLastError());
        }
    }

    /**
     * Example of account action
     * Set access property to "public" for test
     */
    protected function accountAction()
    {
        $paymentService = $this->get('payment');

        $paymentService->verifyAccount('rd.drgn@yahoo.com', 'Micha', 'Business');
        if ($paymentService->success()) {
            echo 'Account is verified';
        } else {
            throw new \Exception($paymentService->getLastError());
        }
    }
}
