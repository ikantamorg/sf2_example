<?php

namespace Domain\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transactions
 *
 * @ORM\Table(name="transactions")
 * @ORM\Entity(repositoryClass="Domain\CoreBundle\Repository\TransactionRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Transaction
{
    // when model is created for the first time
    const STATUS_CREATED = 'CREATED';
    // when primary payment was successful, waiting for IPN payment verification
    const STATUS_WAITING = 'WAITING';
    // when primary payment completed
    const STATUS_PENDING = 'PENDING';
    // when transaction is totally completed
    const STATUS_COMPLETED = 'COMPLETED';
    // when user gets his money back
    const STATUS_REFUNDED = 'REFUNDED';

    /**
     * Get all statuses
     * Method moved to top not to forget change all values
     * 
     * @param bool $friendly
     * @return array
     */
    public static function getStatuses($friendly = false)
    {
        // some shit to call this method statically
        $self = new self;

        $statuses = [
            // static::STATUS_CREATED, // no need to display this status to user
            // static::STATUS_WAITING, // no need to display this status to user
            static::STATUS_PENDING,
            static::STATUS_COMPLETED,
            static::STATUS_REFUNDED,
        ];
        $statuses = array_combine($statuses, $statuses);
        if ($friendly) {
            foreach ($statuses as $status) {
                $statuses[$status] = $self->getFriendlyStatus($status);
            }
        }
        return $statuses;
    }

    /**
     * Get status pretified
     * If not passing value, current status will be returned
     * Method moved to top not to forget change all values
     * 
     * @param string $status
     * @return string
     */
    public function getFriendlyStatus($status = '')
    {
        if (! strlen($status)) {
            $status = $this->getStatus();
        }
        switch ($status) {
            case static::STATUS_CREATED:
                return 'Created';
            case static::STATUS_WAITING:
                return 'Waiting for verification';
            case static::STATUS_PENDING:
                return 'Pending';
            case static::STATUS_REFUNDED:
                return 'Refund';
            case static::STATUS_COMPLETED:
                return 'Completed';
        }
        return 'No status';
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * 
     * @ORM\Column(name="pay_key", type="string", nullable=true)
     */
    private $payKey;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     * 
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var float
     * 
     * @ORM\Column(name="amount_to_admin", type="decimal", scale=2, nullable=false)
     */
    private $amountToAdmin;

    /**
     * @var float
     *
     * @ORM\Column(name="amount_to_expert", type="decimal", scale=2, nullable=false)
     */
    private $amountToExpert;

    /**
     * @var string
     * 
     * @ORM\Column(name="currency", type="string", nullable=true)
     */
    private $currency;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status = self::STATUS_CREATED;

    /**
     * @var int
     * 
     * @ORM\Column(name="attempt", type="integer", nullable=false)
     */
    private $attempt = 1;

    /**
     * @var string
     * 
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;

    /**
     * @var string
     * 
     * @ORM\Column(name="details", type="text", nullable=true)
     */
    private $details;

    /**
     * @var \Domain\CoreBundle\Entity\Expert
     *
     * @ORM\ManyToOne(targetEntity="Domain\CoreBundle\Entity\Expert")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="expert_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     * })
     */
    private $expert;

    /**
     * @var \Domain\CoreBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Domain\CoreBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="candidate_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     * })
     */
    private $candidate;

    /**
     * @var \Domain\CoreBundle\Entity\Appointment
     * 
     * @ORM\OneToOne(targetEntity="Domain\CoreBundle\Entity\Appointment", mappedBy="transaction")
     */
    private $appointment;

    /**
     * Define any default values
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Transaction
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set experts
     *
     * @param \Domain\CoreBundle\Entity\Expert $expert
     * @return Transaction
     */
    public function setExpert(\Domain\CoreBundle\Entity\Expert $expert = null)
    {
        $this->expert = $expert;
    
        return $this;
    }

    /**
     * Get experts
     *
     * @return \Domain\CoreBundle\Entity\Expert
     */
    public function getExpert()
    {
        return $this->expert;
    }

    /**
     * Set candidate
     *
     * @param \Domain\CoreBundle\Entity\User $candidate
     * @return Transaction
     */
    public function setCandidate(\Domain\CoreBundle\Entity\User $candidate = null)
    {
        $this->candidate = $candidate;
    
        return $this;
    }

    /**
     * Get candidate
     *
     * @return \Domain\CoreBundle\Entity\User
     */
    public function getCandidate()
    {
        return $this->candidate;
    }

    /**
     * Set amountToAdmin
     *
     * @param float $amountToAdmin
     * @return Transaction
     */
    public function setAmountToAdmin($amountToAdmin)
    {
        $this->amountToAdmin = $amountToAdmin;
    
        return $this;
    }

    /**
     * Get amountToAdmin
     *
     * @return float 
     */
    public function getAmountToAdmin()
    {
        return $this->amountToAdmin;
    }

    /**
     * Set amountToExpert
     *
     * @param integer $amountToExpert
     * @return Transaction
     */
    public function setAmountToExpert($amountToExpert)
    {
        $this->amountToExpert = $amountToExpert;
    
        return $this;
    }

    /**
     * Get amountToExpert
     *
     * @return integer 
     */
    public function getAmountToExpert()
    {
        return $this->amountToExpert;
    }

    /**
     * Set currency
     *
     * @param string $currency
     * @return Transaction
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    
        return $this;
    }

    /**
     * Get currency
     *
     * @return string 
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Transaction
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return Transaction
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    
        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set payKey
     *
     * @param string $payKey
     * @return Transaction
     */
    public function setPayKey($payKey)
    {
        $this->payKey = $payKey;
    
        return $this;
    }

    /**
     * Get payKey
     *
     * @return string 
     */
    public function getPayKey()
    {
        return $this->payKey;
    }

    /**
     * Set attempt
     *
     * @param integer $attempt
     * @return Transaction
     */
    public function setAttempt($attempt)
    {
        $this->attempt = $attempt;
    
        return $this;
    }

    /**
     * Get attempt
     *
     * @return integer 
     */
    public function getAttempt()
    {
        return $this->attempt;
    }

    /**
     * Update status
     * 
     * @param string $status
     * @param string $message
     * @return Transaction
     */
    public function updateStatus($status, $message = '')
    {
        $this->setStatus($status);
        $this->setMessage($message);
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Transaction
     */
    public function setMessage($message)
    {
        $this->message = $message;
    
        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set details
     *
     * @param string $details
     * @return Transaction
     */
    public function setDetails($details)
    {
        $this->details = $details;
    
        return $this;
    }

    /**
     * Get details
     *
     * @return string 
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
    * @ORM\PreUpdate
    */
    public function updateUpdatedAt()
    {
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * Set appointment
     *
     * @param \Domain\CoreBundle\Entity\Appointment $appointment
     * @return Transaction
     */
    public function setAppointment(\Domain\CoreBundle\Entity\Appointment $appointment = null)
    {
        $this->appointment = $appointment;
    
        return $this;
    }

    /**
     * Get appointment
     *
     * @return \Domain\CoreBundle\Entity\Appointment 
     */
    public function getAppointment()
    {
        return $this->appointment;
    }

    /**
     * Add appointment
     *
     * @param \Domain\CoreBundle\Entity\Appointment $appointment
     * @return Transaction
     */
    public function addAppointment(\Domain\CoreBundle\Entity\Appointment $appointment)
    {
        $this->appointment[] = $appointment;
    
        return $this;
    }

    /**
     * Remove appointment
     *
     * @param \Domain\CoreBundle\Entity\Appointment $appointment
     */
    public function removeAppointment(\Domain\CoreBundle\Entity\Appointment $appointment)
    {
        $this->appointment->removeElement($appointment);
    }

    /**
     * If current status is "pending"
     * 
     * @return bool
     */
    public function isPending()
    {
        return $this->status === static::STATUS_PENDING;
    }

    /**
     * If current status is "refunded"
     * 
     * @return bool
     */
    public function isRefunded()
    {
        return $this->status === static::STATUS_REFUNDED;
    }

    /**
     * If current status is "completed"
     * 
     * @return bool
     */
    public function isCompleted()
    {
        return $this->status === static::STATUS_COMPLETED;
    }
}