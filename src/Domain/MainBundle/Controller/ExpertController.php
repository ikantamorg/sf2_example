<?php
/**
 * User: Dred
 * Date: 08.10.13
 * Time: 13:19
 */

namespace Domain\MainBundle\Controller;

use Domain\CoreBundle\Controller\CoreController;
use Domain\CoreBundle\Entity\Review;
use Domain\CoreBundle\Entity\Appointment;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ExpertController
 *
 * @package Domain\MainBundle\Controller
 */
class ExpertController extends CoreController
{
    const REVIEWS_PER_PAGE = 10;

    /**
     * Expert profile view
     *
     * @param $expert_id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function profileAction($expert_id)
    {

        $expert = $this->getExpert($expert_id);

        return $this->render(
            'MainBundle:Expert:profile_view.html.twig',
            [
                'expert' => $expert,
                'info' => $expert->getAdditionalInfo()
            ]
        );
    }

    /**
     * Expert booking
     *
     * @param Request $request
     * @param $expert_id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function bookingAction(Request $request, $expert_id)
    {

        $expert = $this->getExpert($expert_id);

        $errors = [];

        $preferedStep = null;

        $booking = $this->get('domain.booking');
        $booking->setExpert($expert);

        if (!$booking->canBook()) {
            $this->addFlash('error', $booking->getErrorsString());

            return $this->redirectPath('expert_public_profile', ['expert_id' => $expert->getId()]);
        }

        if ($request->isMethod('POST')) {

            if (($postData = $request->request->get('booking'))) {

                if (isset($postData['step']) && isset($postData['data'])) {
                    $step = $booking->getStep($postData['step']);
                    if ($step) {
                        $step->setData($postData['data']);
                    }
                }

                if (isset($postData['prefered_step'])) {
                    $preferedStep = (int) $postData['prefered_step'];
                }

            } else {
                $errors[] = 'Please select appropriate option to continue.';
            }


        }

        if ($booking->isAllStepsDone()) {
            $paymentUrl = $booking->processData();

            return $this->redirect($paymentUrl);
        }

        $currentStep = $booking->getPreferedStep($preferedStep);

        //before then errors check, because errors can be thrown in render method
        $content = $currentStep->render();

        if ($currentStep->hasErrors()) {
            $errors = $currentStep->getErrors();
        }


        return $this->render(
            'MainBundle:Expert:booking.html.twig',
            [
                'expert' => $expert,
                'steps_map' => $booking->getStepsMap(),
                'content' => $content,
                'content_css' => $currentStep->getStyles(),
                'content_js' => $currentStep->getJs(),
                'booking_step' => $currentStep->getPosition(),
                'errors' => $errors
            ]
        );
    }

    public function successBookingAction()
    {
        return $this->render(
            'MainBundle:Expert:successBooking.html.twig'
        );
    }

    /**
     * Display all expert's reviews
     * 
     * @param Request $request
     * @param int $expertId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reviewsAction(Request $request, $expertId = null)
    {
        $expert = $this->getExpert($expertId);

        $reviews = $this->getRepository('CoreBundle:Review')
            ->findAllOrderedByCreatedAt($expert);

        // paginate
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $reviews,
            $request->query->get('page', 1),
            static::REVIEWS_PER_PAGE
        );

        return $this->render(
            'MainBundle:Expert:reviews.html.twig',
            [
                'expert' => $expert,
                'pagination' => $pagination,
                'max_rating' => $this->container->getParameter('expert.max_rating'),
            ]
        );
    }


    /**
     * Try to get expert or throw excepion
     *
     * @param int $expertId
     *
     * @return \Domain\CoreBundle\Entity\Expert
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function getExpert($expertId)
    {
        $expert = $this->getRepository('CoreBundle:Expert')->findOneActiveById($expertId);

        if (!$expert || !$expert->getActive()) {
            throw $this->createNotFoundException();
        }

        return $expert;
    }
}
