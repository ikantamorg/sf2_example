<?php

namespace Ikantam\ImagerBundle\Controller;

use Ikantam\ImagerBundle\Image\ImageGenerator;
use Ikantam\ImagerBundle\Image\ImageLinkParser;
use Ikantam\ImagerBundle\Image\ImagePresetsManager;
use Imagine\Gd\Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ImagePresetController extends Controller
{

    /**
     * .htaccess redirects application to this controller if image path does not exist.
     * This controller check image and generate it using image path
     *
     * @access public
     */
    public function indexAction( Request $request )
    {

        $imageLink = $request->query->get('filepath');
        $imageLink = urldecode($imageLink);

        //if preset not created
        $imageLinkParser = new ImageLinkParser( $imageLink );
        $imageId = $imageLinkParser->getImageId();

        //get preset name from fileName and get data for this preset
        $requiredPresetName = $imageLinkParser->getImagePresetName();
        $requiredPresetObject = new ImagePresetsManager($this->container->getParameter('presets'));
        $requiredPresetData = $requiredPresetObject->getPresetParams($requiredPresetName);

        //get image object using image ID from image link
        $defaultFileObject = $this->getDoctrine()->getRepository('CoreBundle:File')->find( $imageId );

        $defaultImageLink = $this->_getWebPath().$defaultFileObject->getPath().$defaultFileObject->getName();

        $image = $defaultFileObject->getImage();
        //test crop coordinates
        //$cropData = json_decode($defaultImageObject->getCropData());
        $cropStartCoordinates = [$image->getX(), $image->getY()];
        $cropAreaData = [$image->getWidth(), $image->getHeight()];

        //check upload directory before image-generating
        $this->_checkMediaDirectory($imageId, $requiredPresetName);

        //crop image and generate new cropped and resized image (will be placed in imageLink path)
        $imageGenerator = new ImageGenerator();
        $imageGenerator->setImageWidth($requiredPresetData[0]);
        $imageGenerator->setImageHeight($requiredPresetData[1]);
        //if user select crop area


        if(!empty($cropStartCoordinates[0]) && !empty($cropStartCoordinates[1])) {
            $imageGenerator->crop($defaultImageLink, $cropStartCoordinates, $cropAreaData, $imageLink);
            $imageGenerator->resize($imageLink, $imageLink);
        } else {
            $imageGenerator->resize($defaultImageLink, $imageLink);
        }


        return $this->_getImageAfterResizing( $imageLink );

    }

    /**
     * Check existing of upload media dir and generate it if it is not creates
     *
     * @access private
     * @param null $imageId
     * @param string $presetName
     */
    private function _checkMediaDirectory( $imageId = null, $presetName = '' )
    {

        $fullWebPath = $this->_getWebPath();
        $uploadPath = $fullWebPath.'/'.ImageGenerator::IMAGES_UPLOAD_DIR;

        if( $imageId != null ) {
            $uploadPath .= '/'.$imageId;
        }

        if( !empty($presetName) ) {
            $uploadPath .= '/'.$presetName;
        }

        if(!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }
    }

    /**
     * Used to get path of WEB symfony dir
     *
     * @access private
     * @return string
     */
    private function _getWebPath()
    {
        $docRoot =  $this->container->get('kernel')->getRootDir().'/../web';
        return realpath($docRoot);
    }

    private function _getImageAfterResizing( $imageLink )
    {
/*        if ( $file = @file_get_contents($imageLink) ) {

            $type = @mime_content_type($imageLink);

            $response = new Response($file, 200);
            $response->headers->set('Content-Type', $type);
            return $response;
        }*/

        $filePath = realpath($imageLink);

        $response = new BinaryFileResponse($filePath);
        $response->trustXSendfileTypeHeader();
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE
        );

        return $response;
    }

}