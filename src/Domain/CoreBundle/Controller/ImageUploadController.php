<?php
/**
 * User: Dred
 * Date: 30.09.13
 * Time: 15:33
 */

namespace Domain\CoreBundle\Controller;

use Domain\CoreBundle\Entity\Image;
use Ikantam\ImagerBundle\Image\ImageGenerator;
use Ikantam\ImagerBundle\Image\ImagePresetsManager;

class ImageUploadController extends CoreController
{
    protected $default_preset_name = 'big';

    public function indexAction()
    {
        $uploadHandler = $this->get('i_uploader');
        $data = $uploadHandler->upload();

        foreach ($data['files'] as $_file) {
            switch($_file->getType()) {
                case 'image/jpeg':
                case 'image/gif':
                case 'image/png':
                case 'image/pjpeg':
                case 'image/x-png':
                    $this->addImage($_file);
                    break;
            }
        }

        $files_array = [];

        $preset = $this->getRequest()->request->get('preset');

        foreach ($data['files'] as $_file) {
            $tmp_data = [
                'id' => $_file->getId()
            ];

            if($_file->getImage()){
                $image = $_file->getImage();



                $tmp_data['image'] = [
                    'image_id' => $image->getId(),
                    'width' => $image->getWidth(),
                    'height' => $image->getHeight(),
                    'x' => $image->getX(),
                    'y' => $image->getY(),
                    'x2' => $image->getX2(),
                    'y2' => $image->getY2(),
                    'url' => $image->getPresetUrl($this->default_preset_name)
                ];
                if($preset){
                    $tmp_data['image']['preset_url'] =  $image->getPresetUrl($preset);
                }
            }
            $files_array[] = $tmp_data;
        }


        return $this->jsonResponse(['files' => $files_array]);
    }

    public function addImage($file)
    {

        $image_file_path = $this->get('kernel')->getRootDir() . '/../web'.$file->getPath().$file->getName();

        $requiredPresetObject = new ImagePresetsManager($this->container->getParameter('presets'));
        $requiredPresetData = $requiredPresetObject->getPresetParams($this->default_preset_name);

        $imageGenerator = new ImageGenerator();
        $imageGenerator->setImageWidth($requiredPresetData[0]);
        $imageGenerator->setImageHeight($requiredPresetData[1]);
        $imageGenerator->resize($image_file_path, $image_file_path);

        $image = new Image();
        $image->setFile($file);

        $size = getimagesize($image_file_path);

        $image->setX(0);
        $image->setY(0);

        $image->setWidth($size[0]);
        $image->setHeight($size[1]);

        $image->setX2($size[0]);
        $image->setY2($size[1]);

        $em = $this->getDoctrine()->getManager();

        $em->persist($image);
        $em->flush();

        return $image;
    }

    public function cropAction()
    {
        $user = $this->getUser();
        $data = $this->getRequest()->request->all();

        if (empty($data['file_id']) || empty($data['preset'])) {
            return $this->createNotFoundException();
        }
        $em = $this->getDoctrine()->getManager();
        $file = $em->find('CoreBundle:File', $data['file_id']);

        if (!$file || $file->getUser()->getId() != $user->getId() || !$file->getImage()) {
            return $this->createNotFoundException();
        }

        $image = $file->getImage();

        if (!empty($data['crop_data'])) {
            $crop_data = $data['crop_data'];
            $image->setWidth($crop_data['w']);
            $image->setHeight($crop_data['h']);
            $image->setX($crop_data['x']);
            $image->setY($crop_data['y']);
            $image->setX2($crop_data['x2']);
            $image->setY2($crop_data['y2']);

            $em->persist($image);
            $em->flush();
        }


        return $this->jsonResponse(['url' => $image->getPresetUrl($data['preset'])]);
    }

}
