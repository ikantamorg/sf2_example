<?php


namespace Domain\MainBundle\Controller;

use Domain\CoreBundle\Controller\CoreController;
use Symfony\Component\HttpFoundation\Request;

class FaqController extends CoreController
{
    public function indexAction()
    {
        return $this->render('MainBundle:Faq:index.html.twig');
    }
}

