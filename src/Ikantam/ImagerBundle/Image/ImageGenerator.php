<?php

namespace Ikantam\ImagerBundle\Image;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\ImageInterface;

class ImageGenerator
{

    /**
     * Directory for uploaded images
     */
    const IMAGES_UPLOAD_DIR = 'upload/persets';

    /**
     * New image width
     *
     * @var
     */
    private $_width;

    /**
     * New image height
     * @var
     */
    private $_height;

    function __construct()
    {
        //set default size for website images
        $this->setImageWidth(800);
        $this->setImageHeight(600);
    }

    /**
     * Set generated image width
     *
     * @access public
     * @param $width
     */
    public function setImageWidth( $width )
    {
        $this->_width = $width;
    }

    /**
     * Set generated image height
     *
     * @access public
     * @param $height
     */
    public function setImageHeight( $height )
    {
        $this->_height = $height;
    }

    /**
     * Used to crop image by crop top-left coordinates and crop area (X / Y px)
     *
     * @access public
     * @param $filePath - original file path (this file will be cropped)
     * @param array $cropStartCoordinates - coordinates of top-left corner of cropped area
     * @param array $cropSquare
     * @param string $outputFile - generated image path
     * @return $this|bool|\Imagine\Image\ManipulatorInterface
     */
    public function crop( $filePath, array $cropStartCoordinates, array $cropSquare, $outputFile = '' )
    {

        if(!$this->_checkOutputPath($outputFile)) {
            return false;
        }

        $imagine = new Imagine();
        $image = $imagine->open($filePath);

        $cropStartPoint = new Point($cropStartCoordinates[0], $cropStartCoordinates[1]);
        $cropSquareBox = new Box($cropSquare[0], $cropSquare[1]);

        return $image->crop($cropStartPoint, $cropSquareBox)
            ->save($outputFile);

    }

    /**
     * Used to resize image to selected size
     *
     * @access public
     * @param $filePath - original file path (this file will be resized)
     * @param string $outputFile - generated file path
     * @return $this|bool|\Imagine\Image\ManipulatorInterface
     */
    public function resize( $filePath, $outputFile = '' )
    {
        if(!$this->_checkOutputPath($outputFile)) {
            return false;
        }

        $imagine = new Imagine();
        $mode = ImageInterface::THUMBNAIL_OUTBOUND;

        $image = $imagine->open( $filePath );
        $imageSize = $image->getSize();

        $currentWidth = $imageSize->getWidth();
        $currentHeight = $imageSize->getHeight();

        $newSizes = $this->_getResizeSizes($currentWidth, $currentHeight);

        $size = new Box($newSizes['width'], $newSizes['height']);

        return $image->thumbnail($size, $mode)
            ->save($outputFile);
    }

    /**
     * Generate valid resize sizes (check current and new width / height)
     *
     * @access private
     * @param $currentWidth
     * @param $currentHeight
     * @return array
     */
    private function _getResizeSizes($currentWidth, $currentHeight)
    {
        $nx = $this->_width;
        $ny = $this->_height;

        if($currentWidth >= $this->_width && $currentHeight >= $this->_height){
            if($currentWidth > $currentHeight){
                $nx = $this->_width;
                $ny = (int)(($currentHeight / $currentWidth) * $nx);
            }else{
                $ny = $this->_height;
                $nx = (int)(($currentWidth / $currentHeight) * $ny);
            }

        } elseif($currentWidth >= $this->_width){

            $nx = $this->_width;
            $ny = (int)(($currentHeight / $currentWidth) * $nx);

        } elseif($currentHeight >= $this->_height){

            $ny = $this->_height;
            $nx = (int)(($currentWidth / $currentHeight) * $ny);
        } elseif($currentHeight < $this->_height && $currentWidth < $this->_width) {

            $ny = $currentHeight;
            $nx = $currentWidth;
        }

        return array('width' => $nx, 'height' => $ny);
    }

    /**
     * Check - exist or not path for generated file
     *
     * @access public
     * @param $outputFile
     * @return bool
     */
    private function _checkOutputPath( $outputFile )
    {
        if(empty($outputFile)) {
            return false;
        }

        return true;
    }

}