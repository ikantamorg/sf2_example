<?php
/**
 * User: Dred
 * Date: 05.11.13
 * Time: 15:36
 */

namespace Domain\CoreBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Domain\CoreBundle\Entity\Appointment;

class Builder extends ContainerAware
{
    public function expertsPublicTabs(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $uri = $this->container->get('request')->getBaseUrl() . $this->container->get('request')->getPathInfo();
        $menu->setCurrentUri($uri);

        $securityContext = $this->container->get('security.context');

        $isUserLoggedIn = $securityContext->isGranted('IS_AUTHENTICATED_FULLY');
        $isExpert = $securityContext->isGranted('ROLE_EXPERT');

        if (!$isExpert) {
            $menu->addChild(
                'Book a Session',
                [
                    'route' => 'expert_booking',
                    'routeParameters' => ['expert_id' => $options['expert_id']],
                    'linkAttributes' => ['class' => $isUserLoggedIn ? '' : 'registration-required']
                ]
            );
        }

        $menu->addChild(
            'Profile',
            [
                'route' => 'expert_public_profile',
                'routeParameters' => array('expert_id' => $options['expert_id'])
            ]
        );
        $menu->addChild(
            'Reviews',
            [
                'route' => 'expert_public_reviews',
                'routeParameters' => ['expertId' => $options['expert_id']],
            ]
        );



        foreach ($menu as $item) {
            $item->setLinkAttribute('class', $item->getLinkAttribute('class').' tb-btn');
        }

        return $menu;
    }


    public function footerMenu(FactoryInterface $factory)
    {
        $menu = $factory->createItem('root');

        $uri = $this->container->get('request')->getBaseUrl() . $this->container->get('request')->getPathInfo();
        $menu->setCurrentUri($uri);

        $menu->addChild('Home', ['route' => 'main_home']);
        $menu->addChild('FAQ', ['route' => 'faq']);
        $menu->addChild('Our Team', []);
        $menu->addChild('Become an Expert', ['route' => 'become_expert']);
        $menu->addChild('Contact Us', ['route' => 'contact_us']);
        $menu->addChild('Terms', ['route' => 'terms']);



        return $menu;
    }

    public function expertsProfileTabs(FactoryInterface $factory)
    {
        $menu = $factory->createItem('root');

        $uri = $this->container->get('request')->getBaseUrl() . $this->container->get('request')->getPathInfo();
        $menu->setCurrentUri($uri);

        $menu->addChild('Sessions', ['route' => 'expert_appointments_index']);
        $menu->addChild('Transactions', ['route' => 'expert_transactions_index']);
        $menu->addChild('Billing Info', ['route' => 'expert_profile_billing']);
        $menu->addChild('Reviews', ['route' => 'expert_reviews']);

        foreach ($menu as $item) {
            $item->setLinkAttribute('class', $item->getLinkAttribute('class').' tb-btn');
        }

        return $menu;
    }

    public function candidatesProfileTabs(FactoryInterface $factory)
    {
        $menu = $factory->createItem('root');

        $uri = $this->container->get('request')->getBaseUrl() . $this->container->get('request')->getPathInfo();
        $menu->setCurrentUri($uri);

        $menu->addChild('Sessions', ['route' => 'candidate_appointments_index']);
        $menu->addChild('Transactions', ['route' => 'candidate_transactions_index']);

        foreach ($menu as $item) {
            $item->setLinkAttribute('class', $item->getLinkAttribute('class').' tb-btn');
        }

        return $menu;
    }

    public function candidatesAfterAppointmentTabs(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $uri = $this->container->get('request')->getBaseUrl() . $this->container->get('request')->getPathInfo();
        $menu->setCurrentUri($uri);


        if (isset($options['appointmentType']) && $options['appointmentType'] === Appointment::TYPE_DRYRUN) {
            $menu->addChild(
                'Feedback from Expert',
                [
                    'route' => 'candidate_after_appointment_feedback',
                    'routeParameters' => ['appointmentId' => $options['appointmentId']],
                ]
            );
        }
        $menu->addChild(
            'Leave a Review for Expert',
            [
                'route' => 'candidate_after_appointment_review',
                'routeParameters' => ['appointmentId' => $options['appointmentId']],
            ]
        );
        $menu->addChild(
            'Details',
            [
                'route' => 'candidate_after_appointment_index',
                'routeParameters' => ['appointmentId' => $options['appointmentId']],
            ]
        );

        if (!$this->hasProblem($options['appointmentId'])) {
            $menu->addChild(
                'Report a Problem',
                [
                    'route' => 'candidate_after_appointment_problem',
                    'routeParameters' => ['appointmentId' => $options['appointmentId']],
                ]
            );
        }



        foreach ($menu as $item) {
            $item->setLinkAttribute('class', $item->getLinkAttribute('class').' tb-btn');
        }

        return $menu;
    }

    public function expertsAfterAppointmentTabs(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $uri = $this->container->get('request')->getBaseUrl() . $this->container->get('request')->getPathInfo();
        $menu->setCurrentUri($uri);

        if (isset($options['appointmentType']) && $options['appointmentType'] === Appointment::TYPE_DRYRUN) {
            $menu->addChild(
                'Leave Feedback to Candidate',
                [
                    'route' => 'expert_after_appointment_feedback',
                    'routeParameters' => ['appointmentId' => $options['appointmentId']],
                ]
            );
        }
        $menu->addChild(
            'Candidate Review',
            [
                'route' => 'expert_after_appointment_review',
                'routeParameters' => ['appointmentId' => $options['appointmentId']],
            ]
        );
        $menu->addChild(
            'Details',
            [
                'route' => 'expert_after_appointment_index',
                'routeParameters' => ['appointmentId' => $options['appointmentId']],
            ]
        );

        if (!$this->hasProblem($options['appointmentId'])) {
            $menu->addChild(
                'Report a Problem',
                [
                    'route' => 'expert_after_appointment_problem',
                    'routeParameters' => ['appointmentId' => $options['appointmentId']],
                ]
            );
        }

        foreach ($menu as $item) {
            $item->setLinkAttribute('class', $item->getLinkAttribute('class').' tb-btn');
        }

        return $menu;
    }

    /**
     * Header popup menu for expert ()
     *
     * @param FactoryInterface $factory
     * @param array $options
     */
    public function expertHeaderPopup(FactoryInterface $factory, array $options){}
    public function candidateHeaderPopup(FactoryInterface $factory, array $options){}

    /**
     * Check is problem exists
     *
     * @param int $appointmentId
     * @return bool
     */
    protected function hasProblem($appointmentId)
    {
        $em = $this->container
            ->get('doctrine.orm.entity_manager');

        $user = $this->container->get('security.context')->getToken()->getUser();
        $appointment = $em->getRepository('CoreBundle:Appointment')->find($appointmentId);

        return (bool)$em->getRepository('CoreBundle:AppointmentProblem')->findByAppointmentAndUser($appointment, $user);
    }
}
