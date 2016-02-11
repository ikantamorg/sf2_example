<?php
/**
 * User: Dred
 * Date: 20.09.13
 * Time: 15:58
 */

namespace Domain\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Institution
 *
 * @ORM\Table(name="institutions")
 * @ORM\Entity(repositoryClass="Domain\CoreBundle\Repository\InstitutionRepository")
 */
class Institution
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
     * @ORM\OneToMany(targetEntity="Education", mappedBy="institution", cascade={"all"}, orphanRemoval=true)
     */
    private $educations;


    public function __construct()
    {
        $this->educations = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Institution
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
     * Add educations
     *
     * @param \Domain\CoreBundle\Entity\Education $educations
     * @return Institution
     */
    public function addEducation(\Domain\CoreBundle\Entity\Education $educations)
    {
        $this->educations[] = $educations;

        $educations->setInstitution($this);

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
}