<?php
/**
 * User: Dred
 * Date: 05.12.13
 * Time: 18:15
 */

namespace Domain\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Domain\CoreBundle\Controller\CoreController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Domain\CoreBundle\Entity\Appointment;
use Carbon\Carbon;
use DateTime;
use Exception;

/**
 * Class AppointmentsController
 */
abstract class AbstractAppointmentsController extends CoreController
{
    /**
     * @var string date format
     */
    protected $fromJsDateFormat;

    /**
     * @var string date format
     */
    protected $toJsDateFormat;

    /**
     * @var string date-time format
     */
    protected $fromJsDateTimeFormat;

    /**
     * @var string date format
     */
    protected $fullDateTimeFormat;

    /**
     * @var \Domain\CoreBundle\Service\Appointment\Manager
     */
    protected $appointmentManager;

    /**
     * Reject appointment action (same for Candidate and Expert)
     *
     * @param Request $request
     * @param $appointmentId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function cancelAction(Request $request, $appointmentId)
    {
        $appointment = $this->getAppointment($appointmentId);

        // after success action remove this appointment from view
        $out = [
            'remove' => [
                $appointmentId
            ]
        ];

        try {
            $this->appointmentManager->cancel();
        } catch (Exception $e) {
            if ($this->appointmentManager->isVisible()) {
                unset($out['remove']);
                $out['attrs'] = $this->appointmentManager->getAppointmentAttrs();
            }

            $out['error'] = $e->getMessage();
        }

        return $this->jsonResponse($out);
    }

    /**
     * Session action
     *
     * @param $appointmentId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function onlineAction($appointmentId)
    {
        $appointment = $this->getAppointment($appointmentId);

        if ($this->appointmentManager->isEnded()) {
            $route = $this->appointmentManager->getActorOption('expert_after_appointment_index', 'candidate_after_appointment_index');

            return $this->redirectPath($route, ['appointmentId' => $appointment->getId()]);
        } elseif (!$this->appointmentManager->canEnter()) {
            $viewLink = $this->appointmentManager->getViewLink();

            return $this->redirect($viewLink);
        }

        $userType = $this->appointmentManager->getActorTypeAsString();

        $timeFormat = 'h:i A';
        $dateFormat = 'F d, Y';

        $appointmentTime = $appointment->getStartDate()->format($timeFormat) .
            ' - '. $appointment->getEndDate()->format($timeFormat);

        $opponentName = $this->appointmentManager->getOpponentName();

        $appointmentDurationSec = $appointment->timeDiffInSeconds();
        $elapsedTime = $appointmentDurationSec - $this->appointmentManager->getRemainingTime();

        $pageSettings = [
            'appointment' => [
                'id' => $appointment->getId(),
                'server_url' => $this->getParameter('appointment_screen')['media_server_url'],
                'session_id' => $appointment->getSessionId().'|'.$userType,
                'user_type' => $userType,
                'lobby_time' => 60,
                'opponent_name' => $opponentName,
                'elapsed_time' => $elapsedTime,
                'duration_in_sec' => $appointmentDurationSec,
                'data' => [
                    'type' => $appointment->getFriendlyType(),
                    'date' => $appointment->getStartDate()->format($dateFormat),
                    'time' => $appointmentTime,
                    'duration' => $appointment->timeDiffInMinutes(),
                    'timezone' => $appointment->getStartDate()->format('eP'),
                ],
            ]
        ];


        return $this->render(
            'CoreBundle:Appointments:online.html.twig',
            [
                'page_settings' => $pageSettings,
                'opponent_name' => $opponentName,
                'finished_template' => $this->getFinishedTemplateName(),
                'appointment' => $appointment,
                'can_end' => $this->appointmentManager->canEnd(),
            ]
        );
    }


    /**
     * Change date of appointment
     *
     * @ParamConverter("appointment", class="CoreBundle:Appointment", options={"id" = "appointmentId"})
     *
     * @param Request $request
     * @param Appointment $appointment
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     * @throws \Exception
     */
    public function changeDateAction(Request $request, Appointment $appointment)
    {
        if (!$request->isMethod('PATCH')) {
            $this->createAccessDeniedException();
        }

        $patchContent = $request->getContent();
        $patchContent = @json_decode($patchContent, true);

        if (!$patchContent) {
            $this->createAccessDeniedException();
        }

        if (empty($patchContent['change_date'])) {
            $this->createAccessDeniedException();
        }

        $date = $patchContent['change_date'];

        try {
            $start = Carbon::createFromFormat($this->fromJsDateTimeFormat, $date);
        } catch(Exception $e) {
            throw $this->createAccessDeniedException();
        }

        $this->checkAccess($appointment);

        $appointmentDuration = $appointment->timeDiffInMinutes();

        $end = $start->copy();
        $end->addMinutes($appointmentDuration);

        try {
            if (!$this->appointmentManager->changeDate($start, $end)) {
                throw new Exception('Access Denied');
            };
        } catch(Exception $e) {
            throw $e;
        }


        $out = [
            'attrs' => $this->appointmentManager->getAppointmentAttrs()
        ];

        return $this->jsonResponse($out);
    }

    /**
     * Set some data from config
     */
    protected function init()
    {
        $dateFormats = $this->getParameter('date_formats');

        $this->fromJsDateFormat = $dateFormats['from_js'];
        $this->toJsDateFormat = $dateFormats['to_js'];
        $this->fullDateTimeFormat = $dateFormats['full_date_time'];
        $this->fromJsDateTimeFormat = $dateFormats['from_js_with_time'];
    }

    /**
     * Try to extract time range or create
     *
     * @param Request $request
     *
     * @return array contains ['startDate' => DateTime, 'endDate' => DateTime]
     */
    protected function getRangeOfDates(Request $request)
    {
        $textStartDate = $request->query->get('start');
        if (!$textStartDate || !($startDate = DateTime::createFromFormat($this->fromJsDateFormat, $textStartDate))) {
            $startDate = new DateTime('first day of last month');
        }

        $textEndDate = $request->query->get('end');
        if (!$textEndDate || !($endDate = DateTime::createFromFormat($this->fromJsDateFormat, $textEndDate))) {
            $endDate = clone $startDate;
            $endDate->modify('+ 2month');
            $endDate->modify('last day of this month');

        }

        $startDate->setTime(0, 0, 0);
        $endDate->setTime(23, 59, 59);

        return [
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];

    }

    /**
     * Get appointment by id
     *
     * Same time load service
     *
     * @param $appointmentId
     *
     * @return Appointment
     */
    protected function getAppointment($appointmentId)
    {

        $appointment = $this->getRepository('CoreBundle:Appointment')->find($appointmentId);

        if (!$appointment) {
            //@TODO throw exception
            throw $this->createNotFoundException("Appointment doesn't exist.");
        }

        if (!$this->appointmentManager) {
            $this->appointmentManager = $this->get('domain.appointment.manager');
        }

        $this->setActor();

        $this->appointmentManager->setAppointment($appointment);

        if (!$this->appointmentManager->hasAccess()) {
            throw new Exception("You don't have permission.");
        }

        return $appointment;
    }

    /**
     * Approve appointment action
     *
     * @param Request $request
     * @param $appointmentId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function approveAction(Request $request, $appointmentId)
    {

        $appointment = $this->getAppointment($appointmentId);

        $out = [];

        try {
            $this->appointmentManager->approve();
            $out['remove'] = $this->appointmentManager->getCompetitorsIds();

        } catch (Exception $e) {
            $out['error'] = $e->getMessage();
        }

        if ($this->appointmentManager->isVisible()) {
            $out['attrs'] = $this->appointmentManager->getAppointmentAttrs();
        } else {
            $out['remove'] = [$appointmentId];
        }


        return $this->jsonResponse($out);
    }

    /**
     * Check access to appointment
     *
     * @param Appointment $appointment
     *
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    protected function checkAccess(Appointment $appointment)
    {
        if (!$this->appointmentManager) {
            $this->appointmentManager = $this->get('domain.appointment.manager');
        }

        $user = $this->getUser();
        $security = $this->get('security.context');


        if ($security->isGranted('ROLE_EXPERT')) {
            $this->appointmentManager->setExpert($user->getExpert());
        } elseif ($security->isGranted('ROLE_CANDIDATE')) {
            $this->appointmentManager->setCandidate($user);
        } else {
            throw $this->createAccessDeniedException();
        }

        $this->appointmentManager->setAppointment($appointment);

        if (!$this->appointmentManager->canChangeDate()) {
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * Set current actor (candiate or expert) to appointmentManager;
     */
    abstract protected function setActor();

    /**
     * Should return full path for template
     *
     * @return mixed
     */
    abstract protected function getFinishedTemplateName();
}
 