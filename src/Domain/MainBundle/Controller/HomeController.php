<?php

namespace Domain\MainBundle\Controller;

use Domain\CoreBundle\Controller\CoreController;

class HomeController extends CoreController
{

    /**
     * Website homepage
     *
     * @access public
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {

        $all_industries = $this->getRepository('CoreBundle:Industry')->findAll();
        $experts_count  = $this->getRepository('CoreBundle:Expert')->activeCount();

        return $this->render(
            'MainBundle:Home:index.html.twig',
            [
                'industries' => $all_industries,
                'experts_count' => $experts_count
            ]
        );
    }
}
