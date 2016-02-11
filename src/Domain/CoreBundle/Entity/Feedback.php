<?php

namespace Domain\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Feedbacks
 *
 * @ORM\Table(name="feedbacks")
 * @ORM\Entity(repositoryClass="Domain\CoreBundle\Repository\FeedbackRepository")
 */
class Feedback
{
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
     * @ORM\Column(name="video_id", type="integer", nullable=true)
     */
    private $videoId;

    /**
     * @var string
     * 
     * @Assert\NotBlank
     * @Assert\Length(max=4000)
     * @ORM\Column(name="strong_points", type="text", nullable=false)
     */
    private $strongPoints;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(max=4000)
     * @ORM\Column(name="weak_points", type="text", nullable=false)
     */
    private $weakPoints;

    /**
     * @var string
     * 
     * @Assert\NotBlank
     * @Assert\Length(max=4000)
     * @ORM\Column(name="decision", type="text", nullable=false)
     */
    private $decision;

    /**
     * @var string
     * 
     * @Assert\Length(max=4000)
     * @ORM\Column(name="additional", type="text", nullable=true)
     */
    private $additional;

    /**
     * @var \Domain\CoreBundle\Entity\Expert
     *
     * @ORM\ManyToOne(targetEntity="Domain\CoreBundle\Entity\Expert")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="expert_id", referencedColumnName="id")
     * })
     */
    private $expert;

    /**
     * @var \Domain\CoreBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Domain\CoreBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="candidate_id", referencedColumnName="id")
     * })
     */
    private $candidate;

    /**
     * @var \Domain\CoreBundle\Entity\Appointment
     * 
     * @ORM\OneToOne(targetEntity="Domain\CoreBundle\Entity\Appointment", inversedBy="feedback")
     * @ORM\JoinColumn(name="appointment_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $appointment;

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
     * Set videoId
     *
     * @param integer $videoId
     * @return Feedback
     */
    public function setVideoId($videoId)
    {
        $this->videoId = $videoId;
    
        return $this;
    }

    /**
     * Get videoId
     *
     * @return integer 
     */
    public function getVideoId()
    {
        return $this->videoId;
    }

    /**
     * Set experts
     *
     * @param \Domain\CoreBundle\Entity\Expert $expert
     * @return Feedback
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
     * @return Feedback
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
     * Set appointment
     *
     * @param \Domain\CoreBundle\Entity\Appointment $appointment
     * @return Feedback
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
     * Set strongPoints
     *
     * @param string $strongPoints
     * @return Feedback
     */
    public function setStrongPoints($strongPoints)
    {
        $this->strongPoints = $strongPoints;
    
        return $this;
    }

    /**
     * Get strongPoints
     *
     * @return string 
     */
    public function getStrongPoints()
    {
        return $this->strongPoints;
    }

    /**
     * Set weakPoints
     *
     * @param string $weakPoints
     * @return Feedback
     */
    public function setWeakPoints($weakPoints)
    {
        $this->weakPoints = $weakPoints;
    
        return $this;
    }

    /**
     * Get weakPoints
     *
     * @return string 
     */
    public function getWeakPoints()
    {
        return $this->weakPoints;
    }

    /**
     * Set decision
     *
     * @param string $decision
     * @return Feedback
     */
    public function setDecision($decision)
    {
        $this->decision = $decision;
    
        return $this;
    }

    /**
     * Get decision
     *
     * @return string 
     */
    public function getDecision()
    {
        return $this->decision;
    }

    /**
     * Set additional
     *
     * @param string $additional
     * @return Feedback
     */
    public function setAdditional($additional)
    {
        $this->additional = $additional;
    
        return $this;
    }

    /**
     * Get additional
     *
     * @return string 
     */
    public function getAdditional()
    {
        return $this->additional;
    }
}