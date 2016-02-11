<?php

namespace Domain\AdminBundle\Controller;

use Domain\AdminBundle\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class LoginController extends Controller
{
    public function indexAction()
    {

        $request = $this->getRequest();
        $session = $request->getSession();

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        $form = $this->createForm(
            new LoginType(),
            [
                'email' => $session->get(SecurityContext::LAST_USERNAME)
            ]
        );

        return $this->render(
            'AdminBundle:Login:index.html.twig',
            array(
                'error'         => $error,
                'form' => $form->createView(),
            )
        );

    }
}
