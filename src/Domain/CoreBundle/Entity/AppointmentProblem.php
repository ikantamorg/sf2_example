<?php

namespace Domain\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * AppointmentProblems
 *
 * @ORM\Table(name="appointment_problems")
 * @ORM\Entity(repositoryClass="Domain\CoreBundle\Repository\AppointmentProblemRepository")
 */
class AppointmentProblem
{
    CONST USER_TYPE_CANDIDATE = 1;
    CONST USER_TYPE_EXPERT = 0;

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
     * @ORM\Column(name="message", type="text", nullable=false)
     * @Assert\Length(max=4000)
     * @Assert\NotBlank
     */
    private $message;

    /**
     * @var \Domain\CoreBundle\Entity\Appointment
     *
     * @ORM\ManyToOne(targetEntity="Domain\CoreBundle\Entity\Appointment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="appointment_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     * })
     */
    private $appointment;

    /**
     * @var \Domain\CoreBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Domain\CoreBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     * })
     */
    private $user;

    /**
     * @var int
     *
     * @ORM\Column(name="user_type", type="smallint", nullable=true)
     */
    private $userType;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     *
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var bool
     *
     * @ORM\Column(name="resolved", type="boolean")
     */
    private $resolved;

    /**
     * @var \Domain\CoreBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Domain\CoreBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="resolver_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     * })
     */
    private $resolver;

    /**
     * @var \DateTime
     * @ORM\Column(name="resolved_at", type="datetime", nullable=true)
     */
    private $resolvedAt;

    public function __construct()
    {
        $this->resolved = false;
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
     * Set message
     *
     * @param string $message
     * @return AppointmentProblem
     */
    public function setMessage($message)
    {
        $this->message = htmlentities($message, ENT_HTML5, "UTF-8");
    
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
     * Set appointment
     *
     * @param \Domain\CoreBundle\Entity\Appointment $appointment
     * @return AppointmentProblem
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
     * Set user
     *
     * @param \Domain\CoreBundle\Entity\User $user
     * @return AppointmentProblem
     */
    public function setUser(\Domain\CoreBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Domain\CoreBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set user type to Expert
     *
     * @return $this
     */
    public function userExpert()
    {
        $this->userType = self::USER_TYPE_EXPERT;

        return $this;
    }

    /**
     * Set user type to Candidate
     *
     * @return $this
     */
    public function userCandidate()
    {
        $this->userType = self::USER_TYPE_CANDIDATE;

        return $this;
    }

    /**
     * Is problem resolved.
     *
     * @return bool
     */
    public function isResolved()
    {
        return $this->resolved;
    }

    /**
     * @param bool $resolved
     */
    public function setResolved($resolved)
    {
        $this->resolved = (bool)$resolved;
    }



    /**
     * Set user which resolve this problem
     *
     * @param \Domain\CoreBundle\Entity\User $resolver
     */
    public function setResolver(\Domain\CoreBundle\Entity\User $resolver = null)
    {
        $this->resolver = $resolver;
    }

    /**
     * Get user which resolve this problem
     *
     * @return \Domain\CoreBundle\Entity\User
     */
    public function getResolver()
    {
        return $this->resolver;
    }

    /**
     * @param \DateTime $resolvedAt
     */
    public function setResolvedAt(\DateTime $resolvedAt = null)
    {
        $this->resolvedAt = $resolvedAt;
    }

    /**
     * @return \DateTime
     */
    public function getResolvedAt()
    {
        return $this->resolvedAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get Hyman readable type of user
     *
     * @return string
     */
    public function getReadableTypeOfUser()
    {
        switch ($this->userType) {
            case self::USER_TYPE_CANDIDATE:
                return 'Candidate';
            case self::USER_TYPE_EXPERT;
                return 'Expert';
        }
    }

}
