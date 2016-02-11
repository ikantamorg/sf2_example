<?php

namespace Ikantam\ImagerBundle\Image;
use Domain\CoreBundle\Entity\Image;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;

class ImageLinkGenerator
{

    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function generateLink(Image $image, $presetName, $absolute = false)
    {
        $request = $this->container->get('request');
        $basePath = $request->getBasePath();
        if ($absolute) {
            // TODO: Port detection
            $basePath = $request->getScheme() . '://' . $request->getHttpHost() . $basePath;
        }
        return $basePath.self::genLink($image, $presetName);
    }

    static private function _generateFileName( Image $image )
    {
        $imageName = $image->getUpdated()->getTimestamp().'-'.$image->getFile()->getName();
        return $imageName;
    }

    /**
     * Generate link with out base path (like .../web/)
     *
     * @param Image $image
     * @param $presetName
     *
     * @return string
     */
    static public function genLink(Image $image, $presetName){
        //$imagePath = $image->getFile()->getPath().$presetName;
        $imageName = '/upload/persets/'.$image->getFile()->getId().'/'.$presetName.'/'.self::_generateFileName($image);

        return $imageName;
    }

}