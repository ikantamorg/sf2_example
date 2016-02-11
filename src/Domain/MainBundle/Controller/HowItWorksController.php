<?php


namespace Domain\MainBundle\Controller;

use Domain\CoreBundle\Controller\CoreController;
use Symfony\Component\HttpFoundation\Request;

class HowItWorksController extends CoreController
{
    public function indexAction()
    {
        return $this->render('MainBundle:HowItWorks:index.html.twig');
    }
}

