<?php
/**
 * User: Dred
 * Date: 11.10.13
 * Time: 12:43
 */

namespace Domain\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Company
 *
 * @ORM\Table(name="industries")
 * @ORM\Entity(repositoryClass="Domain\CoreBundle\Repository\IndustryRepository")
 */
class Industry
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
     * @ORM\ManyToMany(targetEntity="Company", mappedBy="industries")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $compamies;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Expert", mappedBy="industry")
     */
    protected $experts;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->compamies = new \Doctrine\Common\Collections\ArrayCollection();
        $this->experts = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Industry
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
     * Add compamies
     *
     * @param \Domain\CoreBundle\Entity\Company $compamies
     * @return Industry
     */
    public function addCompamie(\Domain\CoreBundle\Entity\Company $compamies)
    {
        $this->compamies[] = $compamies;
    
        return $this;
    }

    /**
     * Remove compamies
     *
     * @param \Domain\CoreBundle\Entity\Company $compamies
     */
    public function removeCompamie(\Domain\CoreBundle\Entity\Company $compamies)
    {
        $this->compamies->removeElement($compamies);
    }

    /**
     * Get compamies
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCompamies()
    {
        return $this->compamies;
    }

    /**
     * Add experts
     *
     * @param \Domain\CoreBundle\Entity\Expert $experts
     * @return Industry
     */
    public function addExpert(\Domain\CoreBundle\Entity\Expert $experts)
    {
        $this->experts[] = $experts;
    
        return $this;
    }

    /**
     * Remove experts
     *
     * @param \Domain\CoreBundle\Entity\Expert $experts
     */
    public function removeExpert(\Domain\CoreBundle\Entity\Expert $experts)
    {
        $this->experts->removeElement($experts);
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