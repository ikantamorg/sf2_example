<?php

namespace Ikantam\ImagerBundle\Controller;

use Ikantam\ImagerBundle\Image\ImageGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImagerController extends Controller
{

    public function deleteImageAction(Request $request )
    {
        $imageId = $request->request->get('imageID');
        $imageName = $request->request->get('imageName');

        $manager = $this->getDoctrine()
            ->getManager();

        $imageToDelete = $this->getDoctrine()
            ->getRepository('CoreBundle:Images')
            ->findBy(array(
                'id' => $imageId,
                'imageName' => $imageName
            ));

        if( isset($imageToDelete[0]) ) {
            $manager->remove($imageToDelete[0]);
            $manager->flush();
            $this->removeImageFolder( $imageId );
        }

        return new Response();
    }

    private function removeImageFolder( $imageId )
    {
        $webPath = $this->container->getParameter('kernel.root_dir').'/../web/';
        $uploadPath = ImageGenerator::IMAGES_UPLOAD_DIR;

        $imageToRemovePath = $webPath.''.$uploadPath.'/'.$imageId;

        if(is_dir($imageToRemovePath)) {
            $this->rrmdir($imageToRemovePath);
        }
    }

    private function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") $this->rrmdir($dir."/".$object); else unlink($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }
}
