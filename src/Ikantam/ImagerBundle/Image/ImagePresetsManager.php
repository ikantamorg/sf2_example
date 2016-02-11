<?php

namespace Ikantam\ImagerBundle\Image;

use Symfony\Component\Config\Definition\Exception\Exception;

class ImagePresetsManager
{

    private $_presets;

    function __construct( $presets )
    {
        $this->_presets = $presets;
    }

    public function getPresetParams( $presetName )
    {
        if(isset($this->_presets[$presetName])) {
            $requestedPreset = $this->_presets[$presetName];
            return array((int)$requestedPreset['width'], (int)$requestedPreset['height']);
        } else {
            throw new Exception('Preset "'.$presetName.'" does not exist');
        }
    }


}