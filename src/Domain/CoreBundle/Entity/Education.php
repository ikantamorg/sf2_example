<?php

namespace Domain\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Educations
 *
 * @ORM\Table(name="educations")
 * @ORM\Entity(repositoryClass="Domain\CoreBundle\Repository\EducationRepository")
 */
class Education
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
     * @ORM\Column(name="activities", type="text", nullable=true)
     */
    private $activities;
    /**
     * @var string
     *
     * @ORM\Column(name="degree", type="string", length=255, nullable=true)
     */
    private $degree;
    /**
     * @var string
     *
     * @ORM\Column(name="field_of_study", type="string", length=255, nullable=true)
     */
    private $fieldOfStudy;
    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    private $notes;
    /**
     * @var integer
     *
     * @ORM\Column(name="start_date", type="integer", nullable=true)
     */
    private $startDate;
    /**
     * @var integer
     *
     * @ORM\Column(name="end_date", type="integer", nullable=true)
     */
    private $endDate;
    /**
     * @var \Domain\CoreBundle\Entity\Expert
     *
     * @ORM\ManyToOne(targetEntity="Expert", inversedBy="educations")
     * @ORM\JoinColumn(name="expert_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $expert;
    /**
     * @var \Domain\CoreBundle\Entity\Institution
     *
     * @ORM\ManyToOne(targetEntity="Institution", inversedBy="educations")
     * @ORM\JoinColumn(name="institution_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $institution;

    /**
     * Constructor
     */
    public function __construct()
    {
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
     * Get activities
     *
     * @return string
     */
    public function getActivities()
    {
        return $this->activities;
    }

    /**
     * Set activities
     *
     * @param string $activities
     * @return Education
     */
    public function setActivities($activities)
    {
        $this->activities = $activities;

        return $this;
    }

    /**
     * Get degree
     *
     * @return string
     */
    public function getDegree()
    {
        return $this->degree;
    }

    /**
     * Set degree
     *
     * @param string $degree
     * @return Education
     */
    public function setDegree($degree)
    {
        $this->degree = $degree;

        return $this;
    }

    /**
     * Get fieldOfStudy
     *
     * @return string
     */
    public function getFieldOfStudy()
    {
        return $this->fieldOfStudy;
    }

    /**
     * Set fieldOfStudy
     *
     * @param string $fieldOfStudy
     * @return Education
     */
    public function setFieldOfStudy($fieldOfStudy)
    {
        $this->fieldOfStudy = $fieldOfStudy;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return Education
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return integer
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set startDate
     *
     * @param integer $startDate
     * @return Education
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return integer
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set endDate
     *
     * @param integer $endDate
     * @return Education
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get expert
     *
     * @return \Domain\CoreBundle\Entity\Expert
     */
    public function getExpert()
    {
        return $this->expert;
    }

    /**
     * Set expert
     *
     * @param \Domain\CoreBundle\Entity\Expert $expert
     * @return Education
     */
    public function setExpert(\Domain\CoreBundle\Entity\Expert $expert)
    {
        $this->expert = $expert;

        return $this;
    }

    /**
     * Get institution
     *
     * @return \Domain\CoreBundle\Entity\Institution
     */
    public function getInstitution()
    {
        return $this->institution;
    }

    /**
     * Set institution
     *
     * @param \Domain\CoreBundle\Entity\Institution $institution
     * @return Education
     */
    public function setInstitution(\Domain\CoreBundle\Entity\Institution $institution)
    {
        $this->institution = $institution;

        return $this;
    }
}