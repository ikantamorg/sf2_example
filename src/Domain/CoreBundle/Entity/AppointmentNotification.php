<?php
/**
 * User: Dred
 * Date: 24.12.13
 * Time: 12:58
 */

namespace Domain\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class AppointmentNotification
 *
 * @ORM\Table(name="appointment_notifications")
 * @ORM\Entity
 */
class AppointmentNotification
{
    /**
     * @var \Domain\CoreBundle\Entity\Appointment
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Domain\CoreBundle\Entity\Appointment", inversedBy="notification")
     * @ORM\JoinColumn(name="appointment_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $appointment;

    /**
     * @var boolean
     *
     * @ORM\Column(name="candidate_notified", type="boolean")
     */
    private $candidateNotified;

    /**
     * @var boolean
     *
     * @ORM\Column(name="expert_notified", type="boolean")
     */
    private $expertNotified;

    /**
     * @var boolean
     *
     * @ORM\Column(name="before_start_candidate", type="boolean")
     */
    private $beforeStartCandidate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="before_start_expert", type="boolean")
     */
    private $beforeStartExpert;


    /**
     *
     */
    public function __construct()
    {
        $this->candidateNotified = false;
        $this->expertNotified = false;
        $this->beforeStartCandidate = false;
        $this->beforeStartExpert = false;

    }

    /**
     * Set candidateNotified
     *
     * @param boolean $candidateNotified
     * @return AppointmentNotification
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
     * @return AppointmentNotification
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
     * Set beforeStartCandidate
     *
     * @param boolean $beforeStartCandidate
     * @return AppointmentNotification
     */
    public function setBeforeStartCandidate($beforeStartCandidate)
    {
        $this->beforeStartCandidate = $beforeStartCandidate;
    
        return $this;
    }

    /**
     * Get beforeStartCandidate
     *
     * @return boolean 
     */
    public function getBeforeStartCandidate()
    {
        return $this->beforeStartCandidate;
    }

    /**
     * Set beforeStartExpert
     *
     * @param boolean $beforeStartExpert
     * @return AppointmentNotification
     */
    public function setBeforeStartExpert($beforeStartExpert)
    {
        $this->beforeStartExpert = $beforeStartExpert;
    
        return $this;
    }

    /**
     * Get beforeStartExpert
     *
     * @return boolean 
     */
    public function getBeforeStartExpert()
    {
        return $this->beforeStartExpert;
    }

    /**
     * Set appointment
     *
     * @param \Domain\CoreBundle\Entity\Appointment $appointment
     * @return AppointmentNotification
     */
    public function setAppointment(\Domain\CoreBundle\Entity\Appointment $appointment)
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
}