<?php

namespace Ikantam\FilesBundle\Model;

/**
 * File Model
 */
interface FileInterface
{
    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Set name
     *
     * @param string $name
     * @return self
     */
    public function setName($name);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set size
     *
     * @param integer $size
     * @return self
     */
    public function setSize($size);

    /**
     * Get size
     *
     * @return integer
     */
    public function getSize();

    /**
     * Set type
     *
     * @param string $type
     * @return self
     */
    public function setType($type);

    /**
     * Get type
     *
     * @return string
     */
    public function getType();


    /**
     * Set path
     *
     * @param string $path
     * @return self
     */
    public function setPath($path);

    /**
     * Get path
     *
     * @return string
     */
    public function getPath();


}
