<?php

namespace Domain\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Experts
 *
 * @ORM\Table(name="experts")
 * @ORM\Entity(repositoryClass="Domain\CoreBundle\Repository\ExpertRepository")
 */
class Expert
{

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var string
     *
     * @ORM\Column(name="welcome_message", type="text", nullable=true)
     */
    private $welcomeMessage;

    /**
     * @var integer
     * @Assert\Type(type="numeric", groups={"edit"})
     * @Assert\NotBlank(groups={"edit"})
     * @Assert\Range(min = 10, groups={"edit"})
     * @ORM\Column(name="price", type="integer", nullable=false)
     */
    private $price;

    /**
     * @var decimal
     * 
     * @ORM\Column(name="average_rating", type="decimal", scale=2, nullable=true)
     */
    private $averageRating;

    /**
     * @var \Domain\CoreBundle\Entity\User
     *
     * @ORM\OneToOne(targetEntity="User", inversedBy="expert")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $user;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Education", mappedBy="expert", cascade={"all"}, orphanRemoval=true)
     */
    private $educations;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Skill", inversedBy="experts", cascade={"all"})
     * @ORM\JoinTable(name="experts_skills",
     *      joinColumns={@ORM\JoinColumn(name="expert_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="skill_id", referencedColumnName="id", onDelete="CASCADE")})
     */
    private $skills;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Experience", mappedBy="expert", cascade={"all"}, orphanRemoval=true)
     */
    private $experiences;

    /**
     * @var \Domain\CoreBundle\Entity\AdditionalExpertInfo
     *
     * @ORM\OneToOne(targetEntity="AdditionalExpertInfo", mappedBy="expert", cascade={"all"})
     */
    private $additional_info;

    /**
     * @var \Domain\CoreBundle\Entity\ExpertBilling
     * 
     * @ORM\OneToMany(targetEntity="ExpertBilling", mappedBy="expert", cascade={"all"})
     */
    private $billing;

    /**
     * @ORM\OneToMany(targetEntity="ExpertSchedule", mappedBy="expert", cascade={"all"}, orphanRemoval=true)
     */
    private $expert_schedules;

    /**
     * @var \Domain\CoreBundle\Entity\Industry
     *
     * @ORM\ManyToOne(targetEntity="Industry", inversedBy="experts")
     * @ORM\JoinColumn(name="industry_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $industry;

    /**
     * @var \Domain\CoreBundle\Entity\Location
     *
     * @ORM\ManyToOne(targetEntity="Location", inversedBy="experts")
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $location;

    /**
     * @var \Domain\CoreBundle\Entity\Expert
     *
     * @ORM\OneToMany(targetEntity="Domain\CoreBundle\Entity\Review", mappedBy="expert")
     */
    private $reviews;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->educations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->experiences = new \Doctrine\Common\Collections\ArrayCollection();
        $this->skills = new \Doctrine\Common\Collections\ArrayCollection();
        $this->price = 0;
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
     * Set welcomeMessage
     *
     * @param string $welcomeMessage
     * @return Expert
     */
    public function setWelcomeMessage($welcomeMessage)
    {
        $this->welcomeMessage = $welcomeMessage;
    
        return $this;
    }

    /**
     * Get welcomeMessage
     *
     * @return string 
     */
    public function getWelcomeMessage()
    {
        return $this->welcomeMessage;
    }

    /**
     * Set price
     *
     * @param integer $price
     * @return Expert
     */
    public function setPrice($price)
    {
        $this->price = $price;
    
        return $this;
    }

    /**
     * Get price
     *
     * @return integer 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set user
     *
     * @param \Domain\CoreBundle\Entity\User $user
     * @return Expert
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
     * Add skills
     *
     * @param \Domain\CoreBundle\Entity\Skill $skill
     * @return Expert
     */
    public function addSkill(\Domain\CoreBundle\Entity\Skill $skill)
    {
        $this->skills[] = $skill;
        $skill->addExpert($this);
        return $this;
    }

    /**
     * Remove skills
     *
     * @param \Domain\CoreBundle\Entity\Skill $skill
     */
    public function removeSkill(\Domain\CoreBundle\Entity\Skill $skill)
    {
        $this->skills->removeElement($skill);
    }

    /**
     * Get skills
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSkills()
    {
        return $this->skills;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }




    /**
     * Add educations
     *
     * @param \Domain\CoreBundle\Entity\Education $educations
     * @return Expert
     */
    public function addEducation(\Domain\CoreBundle\Entity\Education $educations)
    {
        $this->educations[] = $educations;

        $educations->setExpert($this);

        return $this;
    }

    /**
     * Remove educations
     *
     * @param \Domain\CoreBundle\Entity\Education $educations
     */
    public function removeEducation(\Domain\CoreBundle\Entity\Education $educations)
    {
        $this->educations->removeElement($educations);
    }

    /**
     * Get educations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEducations()
    {
        return $this->educations;
    }

    /**
     * Add experiences
     *
     * @param \Domain\CoreBundle\Entity\Experience $experiences
     * @return Expert
     */
    public function addExperience(\Domain\CoreBundle\Entity\Experience $experiences)
    {
        $this->experiences[] = $experiences;
        $experiences->setExpert($this);
        return $this;
    }

    /**
     * Remove experiences
     *
     * @param \Domain\CoreBundle\Entity\Experience $experiences
     */
    public function removeExperience(\Domain\CoreBundle\Entity\Experience $experiences)
    {
        $this->experiences->removeElement($experiences);
    }

    /**
     * Get experiences
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getExperiences()
    {
        return $this->experiences;
    }

    /**
     * Set additional_info
     *
     * @param \Domain\CoreBundle\Entity\AdditionalExpertInfo $additionalInfo
     * @return Expert
     */
    public function setAdditionalInfo(\Domain\CoreBundle\Entity\AdditionalExpertInfo $additionalInfo = null)
    {
        $this->additional_info = $additionalInfo;
        $additionalInfo->setExpert($this);
        return $this;
    }

    /**
     * Get additional_info
     *
     * @return \Domain\CoreBundle\Entity\AdditionalExpertInfo 
     */
    public function getAdditionalInfo()
    {
        return $this->additional_info;
    }

    /**
     * Add expert_schedules
     *
     * @param \Domain\CoreBundle\Entity\ExpertSchedule $expertSchedules
     * @return Expert
     */
    public function addExpertSchedule(\Domain\CoreBundle\Entity\ExpertSchedule $expertSchedules)
    {
        $this->expert_schedules[] = $expertSchedules;
    
        return $this;
    }

    /**
     * Remove expert_schedules
     *
     * @param \Domain\CoreBundle\Entity\ExpertSchedule $expertSchedules
     */
    public function removeExpertSchedule(\Domain\CoreBundle\Entity\ExpertSchedule $expertSchedules)
    {
        $this->expert_schedules->removeElement($expertSchedules);
    }

    /**
     * Get expert_schedules
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getExpertSchedules()
    {
        return $this->expert_schedules;
    }

    /**
     * Set industry
     *
     * @param \Domain\CoreBundle\Entity\Industry $industry
     * @return Expert
     */
    public function setIndustry(\Domain\CoreBundle\Entity\Industry $industry = null)
    {
        $this->industry = $industry;
    
        return $this;
    }

    /**
     * Get industry
     *
     * @return \Domain\CoreBundle\Entity\Industry 
     */
    public function getIndustry()
    {
        return $this->industry;
    }

    /**
     * Set location
     *
     * @param \Domain\CoreBundle\Entity\Location $location
     * @return Expert
     */
    public function setLocation(\Domain\CoreBundle\Entity\Location $location = null)
    {
        $this->location = $location;
    
        return $this;
    }

    /**
     * Get location
     *
     * @return \Domain\CoreBundle\Entity\Location 
     */
    public function getLocation()
    {
        return $this->location;
    }


    /**
     * Get billing
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBilling()
    {
        return $this->billing;
    }

    /**
     * Set averageRating
     *
     * @param float $averageRating
     * @return Expert
     */
    public function setAverageRating($averageRating)
    {
        $this->averageRating = $averageRating;
    
        return $this;
    }

    /**
     * Get averageRating
     *
     * @return float 
     */
    public function getAverageRating()
    {
        return $this->averageRating;
    }

    /**
     * Update expert's average rating, when the new review is posted
     * Save method must be called to update rating
     * 
     * @param float $newValue
     * @return float
     */
    public function updateAverageRating($newValue)
    {
        $currentRating = $this->getAverageRating();
        $newValue = floatval($newValue);
        // if new value is zero don't do anything to prevent bad result
        if (! $newValue) {
            return $currentRating;
        }
        // if current rating is null - new value is the first rating value
        if (is_null($currentRating)) {
            $newRating = $newValue;
        } else {
            $newRating = round(floatval(($currentRating + $newValue) / 2), 1);
        }
        $this->setAverageRating($newRating);
        return $newRating;
    }

    /**
     * Add reviews
     *
     * @param \Domain\CoreBundle\Entity\Review $reviews
     * @return Expert
     */
    public function addReview(\Domain\CoreBundle\Entity\Review $reviews)
    {
        $this->reviews[] = $reviews;
    
        return $this;
    }

    /**
     * Remove reviews
     *
     * @param \Domain\CoreBundle\Entity\Review $reviews
     */
    public function removeReview(\Domain\CoreBundle\Entity\Review $reviews)
    {
        $this->reviews->removeElement($reviews);
    }

    /**
     * Get reviews
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * Add billing
     *
     * @param \Domain\CoreBundle\Entity\ExpertBilling $billing
     * @return Expert
     */
    public function addBilling(\Domain\CoreBundle\Entity\ExpertBilling $billing)
    {
        $this->billing[] = $billing;
    
        return $this;
    }

    /**
     * Remove billing
     *
     * @param \Domain\CoreBundle\Entity\ExpertBilling $billing
     */
    public function removeBilling(\Domain\CoreBundle\Entity\ExpertBilling $billing)
    {
        $this->billing->removeElement($billing);
    }

    /**
     * Checking existence of active billing
     *
     * @return bool
     */
    public function isHasActiveBilling()
    {

        foreach ($this->getBilling() as $billing) {
            $email = $billing->getEmail();
            if ($billing->getActive() && !empty($email)) {
                return true;
            }
        }

        return false;
    }
}