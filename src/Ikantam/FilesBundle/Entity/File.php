<?php

namespace Ikantam\FilesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ikantam\FilesBundle\Model\FileInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * File
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="files")
 */
class File implements FileInterface
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank(message="Name should not be empty")
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="size", type="integer", length=11)
     * @Assert\NotBlank(message="Size should not be empty")
     */
    private $size;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     * @Assert\NotBlank(message="Type should not be empty")
     */
    private $type;


    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255)
     * @Assert\NotBlank(message="Path should not be empty")
     */
    private $path;


    public function __construct()
    {
        //for feature changes
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
     * @return File
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
     * Set size
     *
     * @param integer $size
     * @return File
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return File
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }



    /**
     * Set path
     *
     * @param string $path
     * @return File
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

}
