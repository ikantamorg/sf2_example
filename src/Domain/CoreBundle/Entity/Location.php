<?php
/**
 * User: Dred
 * Date: 11.10.13
 * Time: 13:14
 */

namespace Domain\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Company
 *
 * @ORM\Table(name="locations")
 * @ORM\Entity(repositoryClass="Domain\CoreBundle\Repository\LocationRepository")
 */
class Location
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
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false, unique=true)
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Expert", mappedBy="location")
     */
    private $experts;

    /**
     * Constructor
     */
    public function __construct()
    {
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
     * Set code
     *
     * @param string $code
     * @return Location
     */
    public function setCode($code)
    {
        $this->code = $code;
    
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Location
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
     * @param \Domain\CoreBundle\Entity\Expert $experts
     * @return Location
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