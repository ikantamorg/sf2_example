<?php

namespace Domain\AdminBundle\Controller;

use Domain\AdminBundle\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class SqlController extends Controller
{
    public function sqlAction(){

        global $DBDEF;
        $DBDEF=array(
            'user'=> $this->container->getParameter('database_user'),#required
            'pwd'=> null, #required
            'db'=> $this->container->getParameter('database_name'),  #optional, default DB
            'host' => $this->container->getParameter('database_host'),#optional
            'port'=>"",#optional
            'chset'=>"utf8",#optional, default charset
        );

        $path = $this->container->get('kernel')->locateResource('@AdminBundle/phpminiadmin.php');

        require_once $path;

        exit;
    }
}
