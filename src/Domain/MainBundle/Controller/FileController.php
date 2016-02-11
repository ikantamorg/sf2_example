<?php
/**
 * User: dev
 * Date: 27.01.14
 * Time: 1:35
 */

namespace Domain\MainBundle\Controller;

use Domain\CoreBundle\Controller\CoreController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Domain\CoreBundle\Entity\Appointment;

/**
 * Class FileController
 */
class FileController extends CoreController
{

    /**
     * Return Resume file
     *
     * @param $appointmentId
     *
     * @return BinaryFileResponse
     */
    public function resumeAction($appointmentId)
    {
        $user = $this->getUser();
        if (! $user) {
            throw $this->createNotFoundException();
        }

        $appointment = $this->getRepository('CoreBundle:Appointment')
            ->find($appointmentId);

        if (! $appointment) {
            throw $this->createNotFoundException();
        }

        $resumeFile = $appointment->getResumeFile();
        if (! $resumeFile) {
            $this->createNotFoundException();
        }

        if ($user->getId() != $appointment->getCandidate()->getId()) {
            $expert = $user->getExpert();
            if (! $expert) {
                throw $this->createNotFoundException();
            }

            if ($expert->getId() != $appointment->getExpert()->getId()) {
                throw $this->createNotFoundException();
            }
        }


        $downloaderService = $this->get('i_downloader');
        $resumeFilePath = $downloaderService->filePath($resumeFile->getRelativePath());

        $response = new BinaryFileResponse($resumeFilePath);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $appointment->getPrettyResumeFilename()
        );

        return $response;
    }

    /**
     * @param Appointment $appointment
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     *
     * @ParamConverter("appointment", class="CoreBundle:Appointment")
     */
    public function videoToPlayAction(Appointment $appointment)
    {
        /**
         * @var \Domain\CoreBundle\Entity\User
         */
        $user = $this->getUser();

        if (!$user) {
            throw $this->createNotFoundException();
        }

        /**
         * @var \Domain\CoreBundle\Service\Appointment\Manager
         */
        $appointmentManager = $this->get('domain.appointment.manager');

        $appointmentManager->setAppointment($appointment);

        $expert = $user->getExpert();

        if ($expert) {
            $appointmentManager->setExpert($expert);
        } else {
            $appointmentManager->setCandidate($user);
        }

        if (!$appointmentManager->hasAccess()) {
            throw $this->createAccessDeniedException();
        }

        $privateVideoFileSystem = $this->get('domain.core.file_system.video');
        $videoPath = $privateVideoFileSystem->getVideoPath($appointment->getSessionId());

        if (!$videoPath) {
            throw $this->createNotFoundException();
        }

        $videoName = $appointmentManager->getSessionMachineName().'.'.pathinfo($videoPath, PATHINFO_EXTENSION);

        return $this->privateFileResponse($videoPath, $videoName);
    }

    /**
     * @param Appointment $appointment
     * @ParamConverter("appointment", class="CoreBundle:Appointment")
     */
    public function videoToDownloadAction(Appointment $appointment)
    {
        $response = $this->videoToPlayAction($appointment);
        $response->headers->set('Content-Type', 'application/octet-stream');

        return $response;
    }

}