<?php
/**
 * User: Dred
 * Date: 08.10.13
 * Time: 13:19
 */

namespace Domain\MainBundle\Controller;

use Domain\CoreBundle\Controller\CoreController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Domain\CoreBundle\Entity\Appointment;
use Carbon\Carbon;
use DateTime;

/**
 * Class AppointmentController
 */
class AppointmentController extends CoreController
{
    /**
     * @var Domain\CoreBundle\Service\Expert\Scheduler
     */
    //protected $expertScheduler;

    /**
     * @var Domain\CoreBundle\Service\Appointment\Manager
     */
    protected $appointmentManager;

    /**
     *
     * @ParamConverter("appointment", class="CoreBundle:Appointment", options={"id" = "appointmentId"})
     *
     * @param Appointment $appointment
     * @param Carbon $month
     * @return JsonResponse
     */
    public function availableDaysAction(Appointment $appointment, Carbon $month)
    {

        $this->checkAccess($appointment);

        $expertScheduler = $this->get('domain.expert.scheduler');

        $expertScheduler
            ->setExpert($appointment->getExpert())
            ->setCustomSessionDuration($appointment->timeDiffInMinutes())
        ;

        $start = $month;
        $end = $month->copy()->addMonth();

        $result = $expertScheduler->getScheduleRange($start, $end);
        $result = array_keys($result);

        return $this->jsonResponse($result);
    }

    /**
     * @ParamConverter("appointment", class="CoreBundle:Appointment", options={"id" = "appointmentId"})
     *
     * @param Appointment $appointment
     * @param Carbon $day
     */
    public function availableTimeAction(Appointment $appointment, Carbon $day)
    {
        $this->checkAccess($appointment);

        $expertScheduler = $this->get('domain.expert.scheduler');

        $expertScheduler
            ->setExpert($appointment->getExpert())
            ->setCustomSessionDuration($appointment->timeDiffInMinutes())
        ;

        $dayUnavailableErrorMessage = 'This day is unavailable. Please select another one.';

        if (!$expertScheduler->isDayFree($day)) {
            throw $this->createAccessDeniedException($dayUnavailableErrorMessage);
        }

        $timeFormat = 'h:i A';

        $data = $expertScheduler->getScheduleForDayAsHash($day, function($start, $end) use ($timeFormat) {
            return $start->format($timeFormat).' - '.$end->format($timeFormat);
        });

        if (!count($data)) {
            throw $this->createAccessDeniedException($dayUnavailableErrorMessage);
        }

        return $this->jsonResponse($data);

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
        $this->appointmentManager = $this->get('domain.appointment.manager');

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

}
