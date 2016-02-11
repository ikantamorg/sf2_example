<?php

namespace Domain\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use \Doctrine\Common\Collections\ArrayCollection;

/**
 * Skill
 *
 * @ORM\Table(name="skills")
 * @ORM\Entity(repositoryClass="Domain\CoreBundle\Repository\SkillRepository")
 */
class Skill
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
     * @ORM\Column(name="name", type="string", length=255, unique=true, nullable=false)
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Expert", mappedBy="skills")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $experts;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->experts = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Skill
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add experts
     *
     * @param \Domain\CoreBundle\Entity\Expert $expert
     * @return Skill
     */
    public function addExpert(\Domain\CoreBundle\Entity\Expert $expert)
    {
        $this->experts[] = $expert;
    
        return $this;
    }

    /**
     * Remove experts
     *
     * @param \Domain\CoreBundle\Entity\Expert $expert
     */
    public function removeExpert(\Domain\CoreBundle\Entity\Expert $expert)
    {
        $this->experts->removeElement($expert);
    }

    /**
     * Get experts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getExpert()
    {
        return $this->experts;
    }

    /**
     * Get experts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getExperts()
    {
        return $this->experts;
    }
}