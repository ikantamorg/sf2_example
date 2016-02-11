<?php

namespace Domain\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ikantam\FilesBundle\Entity\File as BaseFile;

/**
 * Files
 *
 * @ORM\Table(name="files")
 * @ORM\Entity
 */
class File extends BaseFile
{

    /**
     * @var \Domain\CoreBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="files")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $user;

    /**
     * @var \Domain\CoreBundle\Entity\Image
     *
     * @ORM\OneToOne(targetEntity="Image", mappedBy="file", cascade={"all"})
     */
    private $image;

    /**
     * Set user
     *
     * @param \Domain\CoreBundle\Entity\User $user
     * @return File
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

    /**
     * Set image
     *
     * @param \Domain\CoreBundle\Entity\Image $image
     * @return File
     */
    public function setImage(\Domain\CoreBundle\Entity\Image $image = null)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Get image
     *
     * @return \Domain\CoreBundle\Entity\Image 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Return file path relative to upload folder
     * 
     * @return string
     */
    public function getRelativePath()
    {
        return $this->getId() . DIRECTORY_SEPARATOR . $this->getName();
    }

    /**
     * Get file extension
     * 
     * @return string
     */
    public function getExtension()
    {
        return pathinfo($this->getName(), PATHINFO_EXTENSION);
    }
}