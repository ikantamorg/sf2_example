<?php
/**
 * User: Dred
 * Date: 13.12.13
 * Time: 17:05
 */

namespace Domain\MainBundle\Controller;

use Domain\CoreBundle\Controller\CoreController;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends CoreController
{
    /**
     * Redirect to expert page with flash error message
     *
     * @param Request $request
     * @param $appointmentId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function failAction(Request $request, $appointmentId)
    {
        $appointment = $this->getRepository('CoreBundle:Appointment')->find($appointmentId);

        if (!$appointment) {
            return $this->createNotFoundException();
        }

        $this->addFlash('error', 'Payment failed. Please try again.');

        return $this->redirectPath('expert_public_profile', ['expert_id' => $appointment->getExpert()->getId()]);

    }
}
 