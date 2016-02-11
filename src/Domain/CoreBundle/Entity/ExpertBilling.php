<?php

namespace Domain\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ExpertsBilling
 *
 * @ORM\Table(name="experts_billing")
 * @ORM\Entity(repositoryClass="Domain\CoreBundle\Repository\ExpertBillingRepository")
 */
class ExpertBilling
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
     * @var \Domain\CoreBundle\Entity\Expert
     *
     * @ORM\ManyToOne(targetEntity="Domain\CoreBundle\Entity\Expert", inversedBy="billing")
     * @ORM\JoinColumn(name="expert_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $expert;

    /**
     * @var string
     * @Assert\NotBlank(groups={"billing"})
     * @Assert\Email(groups={"billing"})
     * @ORM\Column(name="email", type="string", nullable=true)
     */
    private $email;

    /**
     * @var string
     * @Assert\NotBlank(groups={"billing"})
     * @ORM\Column(name="first_name", type="string", nullable=true)
     */
    private $firstName;

    /**
     * @var string
     * @Assert\NotBlank(groups={"billing"})
     * @ORM\Column(name="last_name", type="string", nullable=true)
     */
    private $lastName;

    /**
     * @var string
     * 
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = false;



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
     * Set experts
     *
     * @param \Domain\CoreBundle\Entity\Expert $expert
     * @return ExpertBilling
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
     * Set active
     *
     * @param boolean $active
     * @return ExpertBilling
     */
    public function setActive($active)
    {
        $this->active = $active;
    
        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return ExpertBilling
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return ExpertBilling
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    
        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return ExpertBilling
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    
        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }
}