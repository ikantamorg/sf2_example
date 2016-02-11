<?php

namespace Domain\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdditionalExpertInfo
 *
 * @ORM\Table(name="additional_expert_info")
 * @ORM\Entity
 */
class AdditionalExpertInfo
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
     * @var \DateTime
     *
     * @ORM\Column(name="birthday", type="datetime", nullable=true)
     */
    private $birthday;

    /**
     * @var string
     *
     * @ORM\Column(name="summary", type="text", nullable=true)
     */
    private $summary;

    /**
     * @var string
     *
     * @ORM\Column(name="interests", type="text", nullable=true)
     */
    private $interests;

    /**
     * @var \Domain\CoreBundle\Entity\Expert
     *
     * @ORM\OneToOne(targetEntity="Expert", inversedBy="additional_info")
     * @ORM\JoinColumn(name="expert_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $expert;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar_url", type="text", nullable=true)
     */
    private $avatar_url;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="text", nullable=true)
     */
     private $first_name;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="text", nullable=true)
     */
    private $last_name;

    /**
     * @var string
     *
     * @ORM\Column(name="headline", type="text", nullable=true)
     */
    private $headline;

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
     * Set birthday
     *
     * @param \DateTime $birthday
     * @return AdditionalExpertInfo
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    
        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime 
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set summary
     *
     * @param string $summary
     * @return AdditionalExpertInfo
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    
        return $this;
    }

    /**
     * Get summary
     *
     * @return string 
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set interests
     *
     * @param string $interests
     * @return AdditionalExpertInfo
     */
    public function setInterests($interests)
    {
        $this->interests = $interests;
    
        return $this;
    }

    /**
     * Get interests
     *
     * @return string 
     */
    public function getInterests()
    {
        return $this->interests;
    }

    /**
     * Set experts
     *
     * @param \Domain\CoreBundle\Entity\Expert $expert
     * @return AdditionalExpertInfo
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
     * Set avatar_url
     *
     * @param string $avatarUrl
     * @return AdditionalExpertInfo
     */
    public function setAvatarUrl($avatarUrl)
    {
        $this->avatar_url = $avatarUrl;
    
        return $this;
    }

    /**
     * Get avatar_url
     *
     * @return string 
     */
    public function getAvatarUrl()
    {
        return $this->avatar_url;
    }

    /**
     * Set first_name
     *
     * @param string $firstName
     * @return AdditionalExpertInfo
     */
    public function setFirstName($firstName)
    {
        $this->first_name = $firstName;
    
        return $this;
    }

    /**
     * Get first_name
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Set last_name
     *
     * @param string $lastName
     * @return AdditionalExpertInfo
     */
    public function setLastName($lastName)
    {
        $this->last_name = $lastName;
    
        return $this;
    }

    /**
     * Get last_name
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * Set headline
     *
     * @param string $headline
     * @return AdditionalExpertInfo
     */
    public function setHeadline($headline)
    {
        $this->headline = $headline;
    
        return $this;
    }

    /**
     * Get headline
     *
     * @return string 
     */
    public function getHeadline()
    {
        return $this->headline;
    }
}