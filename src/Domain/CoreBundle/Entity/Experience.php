<?php

namespace Domain\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Experiences
 *
 * @ORM\Table(name="experiences")
 * @ORM\Entity
 */
class Experience
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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="summary", type="text", nullable=true)
     */
    private $summary;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_current", type="text", nullable=true)
     */
    private $isCurrent;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @var \Domain\CoreBundle\Entity\Expert
     *
     * @ORM\ManyToOne(targetEntity="Domain\CoreBundle\Entity\Expert", inversedBy="experiences")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="expert_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $expert;

    /**
     * @var \Domain\CoreBundle\Entity\Company
     *
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="experiences")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $company;

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
     * Set title
     *
     * @param string $title
     * @return Experience
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }


    /**
     * Set experts
     *
     * @param \Domain\CoreBundle\Entity\Expert $expert
     * @return Experience
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
     * Set company
     *
     * @param \Domain\CoreBundle\Entity\Company $company
     * @return Experience
     */
    public function setCompany(\Domain\CoreBundle\Entity\Company $company)
    {
        $this->company = $company;
    
        return $this;
    }

    /**
     * Get company
     *
     * @return \Domain\CoreBundle\Entity\Company 
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Experience
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set summary
     *
     * @param string $summary
     * @return Experience
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
     * Set isCurrent
     *
     * @param string $isCurrent
     * @return Experience
     */
    public function setIsCurrent($isCurrent)
    {
        $this->isCurrent = $isCurrent;
    
        return $this;
    }

    /**
     * Get isCurrent
     *
     * @return string 
     */
    public function getIsCurrent()
    {
        return $this->isCurrent;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return Experience
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
     * @return Experience
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
}