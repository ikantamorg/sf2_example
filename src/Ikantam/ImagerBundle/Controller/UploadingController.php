<?php

namespace Ikantam\ImagerBundle\Controller;

use Ikantam\ImagerBundle\Uploader\UploadHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Domain\CoreBundle\Entity\Images;
use Ikantam\ImagerBundle\Image\ImageLinkParser;
use Ikantam\ImagerBundle\Image\ImageGenerator;
use Ikantam\ImagerBundle\Image\ImageLinkGenerator;

class UploadingController extends Controller
{

    public function indexAction()
    {
        $uploaderStaticFilesLoader = $this->get('uploaderFilesLoader');
        $cropperStaticFilesLoader = $this->get('cropperFilesLoader');

        $cropperCss = $cropperStaticFilesLoader->getCss();
        $cropperJs = $cropperStaticFilesLoader->getJs();
        $blueImpCss = $uploaderStaticFilesLoader->getCss();
        $blueImpJs = $uploaderStaticFilesLoader->getJs();

        $cropUrl = $this->get('router')->generate('domain_image_crop_url');

        return $this->render('IkantamImagerBundle:Uploading:index.html.twig', array(
            'blueImpCss' => $blueImpCss,
            'blueImpJs' => $blueImpJs,
            'cropperCss' => $cropperCss,
            'cropperJs' => $cropperJs,
            'cropUrl' => $cropUrl
        ));
    }

    public function cropAction(Request $request)
    {
        $cropParams = $request->request->get('cropData');
        $linkParser = new ImageLinkParser($request->request->get('imageLink'));
        $imageID = $linkParser->getImageId();
        $imageName = $request->request->get('imageName');

        $cropStartCoordinates = array($cropParams['xCoord'], $cropParams['yCoord']);
        $cropArea = array($cropParams['width'], $cropParams['height']);
        $cropData = json_encode(array('startCoordinates' => $cropStartCoordinates, 'cropArea' => $cropArea));

        $imagesRepository = $this->getDoctrine()->getRepository('CoreBundle:Images');
        $image = $imagesRepository->find($imageID);
        $image->setUpdated(time());
        $image->setImageName($imageName);
        $image->setOriginalPath('/'.ImageGenerator::IMAGES_UPLOAD_DIR.'/'.$imageID);
        $image->setCropData($cropData);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($image);
        $entityManager->flush();

        $result = array('imageId' => $image->getID());

        $response = new JsonResponse($result, 200, ['Content-Type' => 'application/json; charset=utf-8']);

        return $response;
    }

    public function getCroppedImageAction(Request $request)
    {
        $imageId = (int)$request->request->get('imageId');
        $presetName = $request->request->get('presetName');

        $imageObject = $this->getDoctrine()->getRepository('CoreBundle:Images')->find($imageId);
        $imageLinkGenerator = $this->get('imageLinkGenerator');
        $imageLink = $imageLinkGenerator->generateLink($imageObject, $presetName);

        return new Response( $this->renderView('IkantamImagerBundle:Uploading:croppedImage.html.twig', array('imageLink' => $imageLink )) );
    }

    public function cropAlternativeAction(Request $request)
    {
        $image_id = $request->request->get('id');
        $crop_data = $request->request->get('crop');
        $perset = $request->request->get('perset');

        $imagesRepository = $this->getDoctrine()->getRepository('CoreBundle:Images');
        $image = $imagesRepository->find($image_id);
        $image->setUpdated(time());

        $image->fetchCropData($crop_data);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($image);
        $entityManager->flush();

        $imageLinkGenerator = $this->get('imageLinkGenerator');
        $imageLink = $imageLinkGenerator->generateLink($image, $perset);

        $data = [
            'perset_url' => $request->getBasePath().$imageLink
        ];

        $response = new JsonResponse($data, 200, ['Content-Type' => 'application/json; charset=utf-8']);

        return $response;
    }



}
