<?php
/**
 * User: dev
 * Date: 30.09.13
 * Time: 22:25
 */

namespace Domain\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ikantam\ImagerBundle\Image\ImageLinkGenerator;

/**
 * Image
 *
 * @ORM\Table(name="images")
 * @ORM\Entity(repositoryClass="Domain\CoreBundle\Repository\ImageRepository")
 */
class Image
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
     * @var \Domain\CoreBundle\Entity\File
     *
     * @ORM\OneToOne(targetEntity="File", inversedBy="image")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $file;

    /**
     * @var width
     *
     * @ORM\Column(name="width", type="integer", nullable=false)
     */
    private $width;

    /**
     * @var height
     *
     * @ORM\Column(name="height", type="integer", nullable=false)
     */
    private $height;

    /**
     * @var x
     *
     * @ORM\Column(name="x", type="integer", nullable=false)
     */
    private $x;

    /**
     * @var y
     *
     * @ORM\Column(name="y", type="integer", nullable=false)
     */
    private $y;

    /**
     * @var x2
     *
     * @ORM\Column(name="x2", type="integer", nullable=false)
     */
    private $x2;

    /**
     * @var y2
     *
     * @ORM\Column(name="y2", type="integer", nullable=false)
     */
    private $y2;

    /**
     * @var \Domain\CoreBundle\Entity\Image
     *
     * @ORM\OneToOne(targetEntity="User", mappedBy="avatar")
     */
    private $user;

    /**
     * @var \Datetime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime", nullable=false)
     */
    private $updated;

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
     * Set width
     *
     * @param integer $width
     * @return Image
     */
    public function setWidth($width)
    {
        $this->width = $width;
    
        return $this;
    }

    /**
     * Get width
     *
     * @return integer 
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param integer $height
     * @return Image
     */
    public function setHeight($height)
    {
        $this->height = $height;
    
        return $this;
    }

    /**
     * Get height
     *
     * @return integer 
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set x
     *
     * @param integer $x
     * @return Image
     */
    public function setX($x)
    {
        $this->x = $x;
    
        return $this;
    }

    /**
     * Get x
     *
     * @return integer 
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * Set y
     *
     * @param integer $y
     * @return Image
     */
    public function setY($y)
    {
        $this->y = $y;
    
        return $this;
    }

    /**
     * Get y
     *
     * @return integer 
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * Set x2
     *
     * @param integer $x2
     * @return Image
     */
    public function setX2($x2)
    {
        $this->x2 = $x2;
    
        return $this;
    }

    /**
     * Get x2
     *
     * @return integer 
     */
    public function getX2()
    {
        return $this->x2;
    }

    /**
     * Set y2
     *
     * @param integer $y2
     * @return Image
     */
    public function setY2($y2)
    {
        $this->y2 = $y2;
    
        return $this;
    }

    /**
     * Get y2
     *
     * @return integer 
     */
    public function getY2()
    {
        return $this->y2;
    }

    /**
     * Set file
     *
     * @param \Domain\CoreBundle\Entity\File $file
     * @return Image
     */
    public function setFile(\Domain\CoreBundle\Entity\File $file)
    {
        $this->file = $file;
        $file->setImage($this);
    
        return $this;
    }

    /**
     * Get file
     *
     * @return \Domain\CoreBundle\Entity\File 
     */
    public function getFile()
    {
        return $this->file;
    }



    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Image
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    
        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Return url for perset
     *
     * @param $perset
     *
     * @return string
     */
    public function getPresetUrl($perset)
    {
        return ImageLinkGenerator::genLink($this, $perset);
    }

    /**
     * Set user
     *
     * @param \Domain\CoreBundle\Entity\User $user
     * @return Image
     */
    public function setUser(\Domain\CoreBundle\Entity\User $user = null)
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