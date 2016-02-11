<?php
/**
 * User: Dred
 * Date: 23.09.13
 * Time: 12:09
 */

namespace Domain\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Company
 *
 * @ORM\Table(name="companies")
 * @ORM\Entity(repositoryClass="Domain\CoreBundle\Repository\CompanyRepository")
 */
class Company
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
     * @ORM\OneToMany(targetEntity="Experience", mappedBy="company", cascade={"all"}, orphanRemoval=true)
     */
    private $experiences;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Industry", inversedBy="compamies", cascade={"all"})
     * @ORM\JoinTable(name="companies_industries",
     *      joinColumns={@ORM\JoinColumn(name="company_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="industry_id", referencedColumnName="id", onDelete="CASCADE")})
     */
    private $industries;

    public function __construct()
    {
        $this->experiences = new \Doctrine\Common\Collections\ArrayCollection();
        $this->industries = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Company
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
     * Add experiences
     *
     * @param \Domain\CoreBundle\Entity\Experience $experiences
     * @return Company
     */
    public function addExperience(\Domain\CoreBundle\Entity\Experience $experiences)
    {
        $this->experiences[] = $experiences;
        $experiences->setCompany($this);
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
     * Add industries
     *
     * @param \Domain\CoreBundle\Entity\Industry $industries
     * @return Company
     */
    public function addIndustrie(\Domain\CoreBundle\Entity\Industry $industries)
    {
        $this->industries[] = $industries;

        $industries->addCompamie($this);

        return $this;
    }

    /**
     * Remove industries
     *
     * @param \Domain\CoreBundle\Entity\Industry $industries
     */
    public function removeIndustrie(\Domain\CoreBundle\Entity\Industry $industries)
    {
        $this->industries->removeElement($industries);
    }

    /**
     * Get industries
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getIndustries()
    {
        return $this->industries;
    }
}