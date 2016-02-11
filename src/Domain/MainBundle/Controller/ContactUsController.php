<?php


namespace Domain\MainBundle\Controller;

use Domain\CoreBundle\Controller\CoreController;
use Symfony\Component\HttpFoundation\Request;
use Domain\MainBundle\Form\ContactUsType;

class ContactUsController extends CoreController
{
    public function indexAction(Request $request)
    {
        $form = $this->createForm(new ContactUsType());

        if ($request->isMethod('POST')) {
            $form->submit($request);
            if ($form->isValid()) {
                $form_data=$form->getData();
                $this->get('pp_mailer')->contactUsSendMessage($form_data);
                $this->addFlash('success', 'Your message has been successfully sent.');
                $form = $this->createForm(new ContactUsType());
            }
        }
        return $this->render(
            'MainBundle:ContactUs:index.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }
}

