<?php

namespace Domain\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotificationOptions
 *
 * @ORM\Table(name="notification_options")
 * @ORM\Entity(repositoryClass="Domain\CoreBundle\Repository\NotificationOptionsRepository")
 */
class NotificationOptions
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="remind_me_email", type="boolean")
     */
    private $remindMeEmail;

    /**
     * @var integer
     *
     * @ORM\Column(name="remind_me_interval", type="integer")
     */
    private $remindMeInterval;


    /**
     * @var \Domain\CoreBundle\Entity\User
     *
     * @ORM\OneToOne(targetEntity="User", mappedBy="user", cascade={"all"})
     */
    /**
     * @var \Domain\CoreBundle\Entity\User
     *
     * @ORM\OneToOne(targetEntity="User", inversedBy="notificationOptions")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $user;

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
     * Set remindMeEmail
     *
     * @param boolean $remindMeEmail
     * @return NotificationOptions
     */
    public function setRemindMeEmail($remindMeEmail)
    {
        $this->remindMeEmail = $remindMeEmail;
    
        return $this;
    }

    /**
     * Get remindMeEmail
     *
     * @return boolean
     */
    public function getRemindMeEmail()
    {
        return $this->remindMeEmail;
    }

    /**
     * Set remindMeInterval
     *
     * @param int $remindMeInterval
     * @return NotificationOptions
     */
    public function setRemindMeInterval($remindMeInterval)
    {
        $this->remindMeInterval = $remindMeInterval;
    
        return $this;
    }

    /**
     * Get remindMeInterval
     *
     * @return int
     */
    public function getRemindMeInterval()
    {
        return $this->remindMeInterval;
    }

    /**
     * Set user
     *
     * @param \Domain\CoreBundle\Entity\User $user
     * @return NotificationOptions
     */
    public function setUser(\Domain\CoreBundle\Entity\User $user)
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
}