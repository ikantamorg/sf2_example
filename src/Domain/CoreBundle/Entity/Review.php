<?php

namespace Domain\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Reviews
 *
 * @ORM\Table(name="reviews")
 * @ORM\Entity(repositoryClass="Domain\CoreBundle\Repository\ReviewRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Review
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
     * @var float
     *
     * @ORM\Column(name="rating", type="decimal", nullable=false)
     * @Assert\NotBlank
     * @Assert\Type(type="digit", message="Rating should be a positive digit")
     * @Assert\GreaterThan(value=0, message="Rating should be positive")
     */
    private $rating = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="review", type="text", nullable=true)
     * @Assert\NotBlank
     * @Assert\Length(max=4000)
     */
    private $review;

    /**
     * @var DateTime
     * 
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var DateTime
     * 
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

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
     * @var \Domain\CoreBundle\Entity\Expert
     *
     * @ORM\ManyToOne(targetEntity="Domain\CoreBundle\Entity\Expert", inversedBy="reviews")
     * @ORM\JoinColumn(name="expert_id", referencedColumnName="id", onDelete="CASCADE")
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
     * Set rating
     *
     * @param float $rating
     * @return Review
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    
        return $this;
    }

    /**
     * Get rating
     *
     * @return float 
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set review
     *
     * @param string $review
     * @return Review
     */
    public function setReview($review)
    {
        $this->review = $review;
    
        return $this;
    }

    /**
     * Get review
     *
     * @return string 
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * Set appointment
     *
     * @param \Domain\CoreBundle\Entity\Appointment $appointment
     * @return Review
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
     * Set experts
     *
     * @param \Domain\CoreBundle\Entity\Expert $expert
     * @return Review
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
     * @return Review
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
    * @ORM\PreUpdate
    */
    public function updateUpdatedAt()
    {
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Review
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Review
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}