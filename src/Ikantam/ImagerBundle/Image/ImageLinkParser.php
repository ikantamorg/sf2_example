<?php

namespace Ikantam\ImagerBundle\Image;

use Ikantam\ImagerBundle\Image\ImageGenerator;
use Symfony\Component\Config\Definition\Exception\Exception;

class ImageLinkParser
{

    private $_imageLink;
    private $_imageId;
    private $_presetName;

    function __construct( $imageLink )
    {
        $this->_imageLink = $imageLink;
        $this->_imageId = null;
        $this->_presetName = '';
        $this->_parseLink();
        $this->_checkParsedData();
    }

    public function getImageId()
    {
        return $this->_imageId;
    }

    public function getImagePresetName()
    {
        return $this->_presetName;
    }

    private function _parseLink()
    {
        $link = $this->_imageLink;
        $link = str_replace(ImageGenerator::IMAGES_UPLOAD_DIR,'', $link);
        if($link[0] && $link[0] == '/'){
            $link = substr($link, 1);
        }
        $pieces = explode('/', $link);

        $this->_imageId = $pieces[0];
        $this->_presetName = $pieces[1];


    }

    private function _checkParsedData()
    {
        if( $this->_imageId == null ) {
            throw new Exception("Image ID not founded in link");
        }

        if( empty($this->_presetName) ) {
            throw new Exception("Preset not founded in link");
        }
    }
}