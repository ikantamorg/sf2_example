<?php

namespace Domain\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;
use DateTime;

/**
 * Appointments
 *
 * @Assert\Callback(methods={"hasResume"}, groups={"booking_dryrun"})
 * @ORM\Table(name="appointments")
 * @ORM\Entity(repositoryClass="Domain\CoreBundle\Repository\AppointmentRepository")
 */
class Appointment
{
    /**
     * Status definition
     */
    const STATUS_WAIT_FOR_PAYMENT = 0;
    const STATUS_PAID = 1;
    const STATUS_DECLINED = 2;
    const STATUS_APPROVED = 3;
    const STATUS_COMPLETED = 4;
    const STATUS_FEEDBACKED = 5;

    /**
     * Type definition
     */
    const TYPE_INTERVIEW = 0;
    const TYPE_DRYRUN = 1;

    /**
     * Video statuses definition
     */
    const VIDEO_STATUS_PENDING = 0;
    const VIDEO_STATUS_PROCESSED = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type;

    /**
     * @var \DateTime
     * @Assert\NotBlank(groups={"booking"})
     * @Assert\DateTime(groups={"booking"})
     * @ORM\Column(name="start_date", type="datetime", nullable=false)
     */
    private $startDate;

    /**
     * @var \DateTime
     * @Assert\NotBlank(groups={"booking"})
     * @Assert\DateTime(groups={"booking"})
     * @ORM\Column(name="end_date", type="datetime", nullable=false)
     */
    private $endDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="total_price", type="integer", nullable=false)
     */
    private $totalPrice;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="resume", type="text", nullable=true)
     */
    private $resume;

    /**
     * @var string
     * @Assert\NotBlank(groups={"booking_dryrun"}, message="Job Description should not be blank.")
     * @ORM\Column(name="job_description", type="text", nullable=true)
     */
    private $jobDescription;

    /**
     * @var string
     * @Assert\NotBlank(groups={"booking_interview"}, message="Short Description should not be blank.")
     * @ORM\Column(name="other_info", type="text", nullable=true)
     */
    private $otherInfo;

    /**
     * @var string
     *
     * @ORM\Column(name="session_id", type="string", length=60, nullable=true, unique=true)
     */
    private $sessionId;

    /**
     * @var integer
     *
     * @ORM\Column(name="video_status", type="integer", nullable=false)
     */
    private $videoStatus;

    /**
     * @var boolean
     *
     * @ORM\Column(name="manual_end", type="boolean")
     */
    private $manualEnd;

    /**
     * @var \DateTime
     * @ORM\Column(name="manual_end_date", type="datetime", nullable=true)
     */
    private $manualEndDate;

    /**
     * @var \Domain\CoreBundle\Entity\Expert
     *
     * @ORM\ManyToOne(targetEntity="Domain\CoreBundle\Entity\Expert")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="expert_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $expert;

    /**
     * @var \Domain\CoreBundle\Entity\File
     *
     * @ORM\ManyToOne(targetEntity="Domain\CoreBundle\Entity\File")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="resume_file_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     * })
     */
    private $resumeFile;

    /**
     * @var \Domain\CoreBundle\Entity\Transaction
     *
     * @ORM\OneToOne(targetEntity="Domain\CoreBundle\Entity\Transaction", inversedBy="appointment")
     * @ORM\JoinColumn(name="transaction_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $transaction;

    /**
     * @var \Domain\CoreBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Domain\CoreBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="candidate_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $candidate;

    /**
     * @var \Domain\CoreBundle\Entity\Feedback
     *
     * @ORM\OneToOne(targetEntity="Domain\CoreBundle\Entity\Feedback", mappedBy="appointment")
     */
    private $feedback;

    /**
     * @var \Domain\CoreBundle\Entity\AppointmentNotification
     *
     * @ORM\OneToOne(targetEntity="Domain\CoreBundle\Entity\AppointmentNotification", mappedBy="appointment")
     */
    private $notification;

    /**
     * @var boolean
     *
     * @ORM\Column(name="require_candidate", type="boolean")
     */
    private $requireCandidate;

        /**
         * Constructor
         */
    public function __construct()
    {
        $this->videoStatus = self::VIDEO_STATUS_PENDING;
        $this->manualEnd = false;
        $this->requireCandidate = false;
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
     * Set type
     *
     * @param integer $type
     *
     * @return Appointment
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return Appointment
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return Appointment
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set totalPrice
     *
     * @param integer $totalPrice
     *
     * @return Appointment
     */
    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    /**
     * Get totalPrice
     *
     * @return integer
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Appointment
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
     * Set resume
     *
     * @param string $resume
     *
     * @return Appointment
     */
    public function setResume($resume)
    {
        $this->resume = $resume;

        return $this;
    }

    /**
     * Get resume
     *
     * @return string
     */
    public function getResume()
    {
        return $this->resume;
    }

    /**
     * Set jobDescription
     *
     * @param string $jobDescription
     *
     * @return Appointment
     */
    public function setJobDescription($jobDescription)
    {
        $this->jobDescription = $jobDescription;

        return $this;
    }

    /**
     * Get jobDescription
     *
     * @return string
     */
    public function getJobDescription()
    {
        return $this->jobDescription;
    }

    /**
     * Set otherInfo
     *
     * @param string $otherInfo
     *
     * @return Appointment
     */
    public function setOtherInfo($otherInfo)
    {
        $this->otherInfo = $otherInfo;

        return $this;
    }

    /**
     * Get otherInfo
     *
     * @return string
     */
    public function getOtherInfo()
    {
        return $this->otherInfo;
    }

    /**
     * Set experts
     *
     * @param \Domain\CoreBundle\Entity\Expert $expert
     *
     * @return Appointment
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
     * Set resumeFile
     *
     * @param \Domain\CoreBundle\Entity\File $resumeFile
     *
     * @return Appointment
     */
    public function setResumeFile(\Domain\CoreBundle\Entity\File $resumeFile = null)
    {
        $this->resumeFile = $resumeFile;

        return $this;
    }

    /**
     * Get resumeFile
     *
     * @return \Domain\CoreBundle\Entity\File
     */
    public function getResumeFile()
    {
        return $this->resumeFile;
    }

    /**
     * Set transaction
     *
     * @param \Domain\CoreBundle\Entity\Transaction $transaction
     *
     * @return Appointment
     */
    public function setTransaction(\Domain\CoreBundle\Entity\Transaction $transaction = null)
    {
        $this->transaction = $transaction;

        return $this;
    }

    /**
     * Get transaction
     *
     * @return \Domain\CoreBundle\Entity\Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * Set candidate
     *
     * @param \Domain\CoreBundle\Entity\User $candidate
     *
     * @return Appointment
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

    public function hasResume(ExecutionContextInterface $context)
    {
        if (empty($this->resume) && empty($this->resumeFile)) {
            $context->addViolationAt('resume', 'You should write or upload Resume', array(), null);
        }
    }

    /**
     * Set feedback
     *
     * @param \Domain\CoreBundle\Entity\Feedback $feedback
     *
     * @return Appointment
     */
    public function setFeedback(\Domain\CoreBundle\Entity\Feedback $feedback = null)
    {
        $this->feedback = $feedback;

        return $this;
    }

    /**
     * Get feedback
     *
     * @return \Domain\CoreBundle\Entity\Feedback
     */
    public function getFeedback()
    {
        return $this->feedback;
    }

    /**
     * If appointment has not feedback
     *
     * @return bool
     */
    public function hasNoFeedback()
    {
        $isDryrun = ($this->type === static::TYPE_DRYRUN);
        $isCompleted = ($this->status === static::STATUS_COMPLETED);
        $hasFeedback = !!$this->getFeedback();

        return $isDryrun && $isCompleted && !$hasFeedback;
    }

    /**
     * If appointment started and ended on the same day
     *
     * @return bool
     */
    public function endedTheSameDay()
    {
        $startDate = $this->startDate->format('Y-m-d');
        $endDate = $this->endDate->format('Y-m-d');

        return $startDate === $endDate;
    }

    /**
     * Return difference between the start and the end appointemnt in seconds
     *
     * @return int
     */
    public function timeDiffInSeconds()
    {
        return abs($this->startDate->getTimestamp() - $this->endDate->getTimestamp());
    }

    /**
     * Return difference between the start and the end appointemnt in minutes
     *
     * @return int
     */
    public function timeDiffInMinutes()
    {
        return ceil($this->timeDiffInSeconds() / 60);
    }

    /**
     * Nice filename for resume
     *
     * @return string
     */
    public function getPrettyResumeFilename()
    {
        return 'Sharpen-It-Resume-' . $this->getCandidate()->getFirstName() . '-' . $this->getCandidate()->getLastName() . '-' . $this->getId() . '.' . $this->getResumeFile()->getExtension();
    }

    /**
     * If appointment is requested
     *
     * @return boolean
     */
    public function isRequested()
    {
        return ($this->getStatus() === static::STATUS_PAID);
    }

    /**
     * If appointment is confirmed
     *
     * @return boolean
     */
    public function isConfirmed()
    {
        return ($this->getStatus() === static::STATUS_APPROVED);
    }

    /**
     * If appointment is completed
     *
     * @return boolean
     */
    public function isCompleted()
    {
        return (($this->getType() === static::TYPE_INTERVIEW && $this->getStatus() === static::STATUS_COMPLETED) || ($this->getType() === static::TYPE_DRYRUN && $this->getStatus() === static::STATUS_FEEDBACKED));
    }

    /**
     * If appointment is ended
     *
     * @return bool
     */
    public function isEnded()
    {
        $nowDate = new DateTime();

        return ($this->getStatus() >= Appointment::STATUS_COMPLETED || $this->getEndDate() <= $nowDate);
    }

    public function isRunning()
    {
        $nowDate = new DateTime();

        return (!$this->isEnded() && $this->getStartDate() <= $nowDate);
    }

    /**
     * If appointment is rejected
     *
     * @return boolean
     */
    public function isRejected()
    {
        return ($this->getStatus() === static::STATUS_DECLINED);
    }

    /**
     * Get status pretified
     * If not passing value, current status will be returned
     *
     * @param string $status
     *
     * @return string
     */
    public function getFriendlyStatus()
    {
        switch (true) {
            case $this->isRejected():
                return 'Rejected';
            case $this->isRequested():
                return 'Requested';
            case $this->isConfirmed():
                return 'Confirmed';
            case $this->hasNoFeedback():
                return 'Need Feedback';
            case $this->isCompleted():
                return 'Completed';
        }
        return 'No status';
    }

    /**
     * Get class name for status
     *
     * @return string
     */
    public function getStatusClassName()
    {
        switch (true) {
            case $this->isRejected():
                return 'rejected';
            case $this->isRequested():
                return 'requested';
            case $this->isConfirmed():
                return 'confirmed';
            case $this->hasNoFeedback():
                return 'need';
            case $this->isCompleted():
                return 'completed';
        }
        return '';
    }

    /**
     * Get type prettified
     *
     * @return string
     */
    public function getFriendlyType()
    {
        switch ($this->getType()) {
            case self::TYPE_INTERVIEW:
                return 'General Q&A Session';
            case self::TYPE_DRYRUN:
                return 'Sharpen Practice';
        }
    }

    /**
     * check is type TYPE_DRYRUN
     *
     * @return bool
     */
    public function isTypeDryrun()
    {
        return $this->getType() === self::TYPE_DRYRUN;
    }

    /**
     * check is type TYPE_INTERVIEW
     *
     * @return bool
     */
    public function isTypeInterview()
    {
        return $this->getType() === self::TYPE_INTERVIEW;
    }

    /**
     * Set candidateNotified
     *
     * @param boolean $candidateNotified
     *
     * @return Appointment
     */
    public function setCandidateNotified($candidateNotified)
    {
        $this->candidateNotified = $candidateNotified;

        return $this;
    }

    /**
     * Get candidateNotified
     *
     * @return boolean
     */
    public function getCandidateNotified()
    {
        return $this->candidateNotified;
    }

    /**
     * Set expertNotified
     *
     * @param boolean $expertNotified
     *
     * @return Appointment
     */
    public function setExpertNotified($expertNotified)
    {
        $this->expertNotified = $expertNotified;

        return $this;
    }

    /**
     * Get expertNotified
     *
     * @return boolean
     */
    public function getExpertNotified()
    {
        return $this->expertNotified;
    }

    /**
     * Set notification
     *
     * @param \Domain\CoreBundle\Entity\AppointmentNotification $notification
     *
     * @return Appointment
     */
    public function setNotification(\Domain\CoreBundle\Entity\AppointmentNotification $notification = null)
    {
        $this->notification = $notification;
        $notification->setAppointment($this);

        return $this;
    }

    /**
     * Get notification
     *
     * @return \Domain\CoreBundle\Entity\AppointmentNotification
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * Set sessionId
     *
     * @param string $sessionId
     *
     * @return Appointment
     */
    /*    public function setSessionId($sessionId)
        {
            $this->sessionId = $sessionId;

            return $this;
        }*/

    /**
     * Generate unique session id
     */
    public function generateSessionId()
    {
        if (isset($this->sessionId)) {
            return;
        }

        $this->sessionId = $this->getId() . sha1($this->getId() . $this->getType() . time());
    }

    /**
     * Get sessionId
     *
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Set videoStatus
     *
     * @param integer $videoStatus
     *
     * @return Appointment
     */
    public function setVideoStatus($videoStatus)
    {
        $this->videoStatus = $videoStatus;

        return $this;
    }

    /**
     * Get videoStatus
     *
     * @return integer
     */
    public function getVideoStatus()
    {
        return $this->videoStatus;
    }

    /**
     * @param boolean $manualEnd
     */
    public function setManualEnd($manualEnd)
    {
        $this->manualEnd = $manualEnd;
    }

    /**
     * @return boolean
     */
    public function isManualEnd()
    {
        return $this->manualEnd;
    }

    /**
     * @param \DateTime $manualEndDate
     */
    public function setManualEndDate($manualEndDate)
    {
        $this->manualEndDate = $manualEndDate;
    }

    /**
     * @return \DateTime
     */
    public function getManualEndDate()
    {
        return $this->manualEndDate;
    }

    /**
     * Check is this appointment has processed video
     *
     * @return bool
     */
    public function hasProcessedVideo()
    {
        return ($this->getVideoStatus() == self::VIDEO_STATUS_PROCESSED);
    }

    /**
     * @param bool $requireCandidate
     */
    public function setRequireCandidate($requireCandidate)
    {
        $this->requireCandidate = $requireCandidate;
    }

    /**
     * @return boolean
     */
    public function getRequireCandidate()
    {
        return $this->requireCandidate;
    }

}