<?php

namespace Domain\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Domain\CoreBundle\Repository\UserRepository")
 * @UniqueEntity(fields="email",  groups={"registration"}, message="Email is already in use")
 */
class User extends BaseUser implements EquatableInterface
{

    const ROLE_CANDIDATE = 'ROLE_CANDIDATE';
    const ROLE_EXPERT = 'ROLE_EXPERT';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Assert\NotBlank(groups={"registration", "edit"}, message="Email should not be empty")
     * @Assert\Email(groups={"registration", "edit"})
     * @Assert\Length(
     * groups={"registration", "edit"}, max=250,
     * maxMessage="Email should be less than {{ limit }} characters")
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     * @Assert\NotBlank(groups={"registration", "edit"}, message="First name should not be empty")
     * @Assert\Length(groups={"registration", "edit"}, max=250,
     * maxMessage="First name should be less than {{ limit }} characters")
     */
    protected $first_name;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     * @Assert\NotBlank(groups={"registration", "edit"}, message="Last name should not be empty")
     * @Assert\Length(groups={"registration", "edit"}, max=250,
     * maxMessage="Last name should be less than {{ limit }} characters")
     */
    protected $last_name;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"registration"}, message="Password should not be empty")
     * @Assert\Length(groups={"registration"}, min=6, minMessage="Password should have at least 6 characters")
     */
    protected $plainPassword;

    /**
     * @var boolean
     *
     * @ORM\Column(name="terms", type="boolean", nullable=false)
     * @Assert\NotBlank(groups={"registration"}, message="You must accept Terms & Conditions")
     */
    private $terms;

    /**
     * @var string
     *
     * @ORM\Column(name="linkedin_id", type="string", length=255, nullable=true)
     */
    private $linkedin_id;

    /**
     * @var string
     *
     * @ORM\Column(name="linkedin_access_token", type="string", length=255, nullable=true)
     */
    private $linkedinAccessToken;

    /**
     * @var string
     *
     * @ORM\Column(name="linkedin_profile_link", type="string", length=255, nullable=true)
     */
    private $linkedinProfileLink;

    /**
     * @var \Domain\CoreBundle\Entity\Image
     *
     * @ORM\OneToOne(targetEntity="Image", inversedBy="user")
     * @ORM\JoinColumn(name="avatar_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $avatar;

    /**
     * @var \Domain\CoreBundle\Entity\Expert
     *
     * @ORM\OneToOne(targetEntity="Expert", mappedBy="user", cascade={"all"})
     */
    private $expert;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="File", mappedBy="user", cascade={"all"}, orphanRemoval=true)
     */
    private $files;

    /**
     * @var \Domain\CoreBundle\Entity\NotificationOptions
     *
     * @ORM\OneToOne(targetEntity="NotificationOptions", mappedBy="user", cascade={"all"})
     */
    private $notificationOptions;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->files = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->first_name = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->last_name = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * Set terms
     *
     * @param boolean $terms
     * @return User
     */
    public function setTerms($terms)
    {
        $this->terms = $terms;

        return $this;
    }

    /**
     * Get terms
     *
     * @return boolean
     */
    public function getTerms()
    {
        return $this->terms;
    }

    /**
     * Set linkedinId
     *
     * @param string $linkedinId
     * @return User
     */
    public function setLinkedinId($linkedinId)
    {
        $this->linkedin_id = $linkedinId;

        return $this;
    }

    /**
     * Get linkedinId
     *
     * @return string
     */
    public function getLinkedinId()
    {
        return $this->linkedin_id;
    }

    /**
     * Set linkedinAccessToken
     *
     * @param string $linkedinAccessToken
     * @return User
     */
    public function setLinkedinAccessToken($linkedinAccessToken)
    {
        $this->linkedinAccessToken = $linkedinAccessToken;

        return $this;
    }

    /**
     * Get linkedinAccessToken
     *
     * @return string
     */
    public function getLinkedinAccessToken()
    {
        return $this->linkedinAccessToken;
    }

    /**
     * Set linkedinProfileLink
     *
     * @param string $linkedinProfileLink
     * @return User
     */
    public function setLinkedinProfileLink($linkedinProfileLink)
    {
        $this->linkedinProfileLink = $linkedinProfileLink;

        return $this;
    }

    /**
     * Get linkedinProfileLink
     *
     * @return string
     */
    public function getLinkedinProfileLink()
    {
        return $this->linkedinProfileLink;
    }


    /**
     * @param \Domain\CoreBundle\Entity\Expert $expert
     */
    public function setExpert(\Domain\CoreBundle\Entity\Expert $expert)
    {
        $this->expert = $expert;
        $expert->setUser($this);
    }

    /**
     * @return \Domain\CoreBundle\Entity\Expert
     */
    public function getExpert()
    {
        return $this->expert;
    }



    
    /**
     * Add files
     *
     * @param \Domain\CoreBundle\Entity\File $files
     * @return User
     */
    public function addFile(\Domain\CoreBundle\Entity\File $files)
    {
        $this->files[] = $files;
    
        return $this;
    }

    /**
     * Remove files
     *
     * @param \Domain\CoreBundle\Entity\File $files
     */
    public function removeFile(\Domain\CoreBundle\Entity\File $files)
    {
        $this->files->removeElement($files);
    }

    /**
     * Get files
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Set avatar
     *
     * @param \Domain\CoreBundle\Entity\Image $avatar
     * @return User
     */
    public function setAvatar(\Domain\CoreBundle\Entity\Image $avatar = null)
    {
        $this->avatar = $avatar;
    
        return $this;
    }

    /**
     * Get avatar
     *
     * @return \Domain\CoreBundle\Entity\Image 
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Compares this user to another to determine if they are the same.
     *
     * @param UserInterface $user
     * @return boolean True if equal, false otherwise.
     */
    public function isEqualTo(UserInterface $user)
    {
        return (
           true
            && md5(serialize($user->getRoles())) == md5(serialize($this->getRoles()))
        )
            ;
    }

    public function serialize()
    {
        return serialize(
            [
                $this->roles,
                parent::serialize()
            ]
        );

    }

    public function unserialize($serialized)
    {
        list(
            $this->roles,
            $parent,
            ) = unserialize($serialized);

        parent::unserialize($parent);
    }

    /**
     * Get first ane last name
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->first_name.' '.$this->last_name;
    }

    /**
     * Set notificationOptions
     *
     * @param \Domain\CoreBundle\Entity\NotificationOptions $notificationOptions
     * @return User
     */
    public function setNotificationOptions(\Domain\CoreBundle\Entity\NotificationOptions $notificationOptions = null)
    {
        $this->notificationOptions = $notificationOptions;
        $notificationOptions->setUser($this);

        return $this;
    }

    /**
     * Get notificationOptions
     *
     * @return \Domain\CoreBundle\Entity\NotificationOptions 
     */
    public function getNotificationOptions()
    {
        return $this->notificationOptions;
    }
}