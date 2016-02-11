<?php
/**
 * User: dev
 * Date: 23.12.13
 * Time: 23:07
 */

namespace Domain\CoreBundle\Listener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

/**
 * Class MaintenanceListener
 */
class MaintenanceListener
{

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * @var string current path
     */
    private $currentPath;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
     */
    private $templating;

    /**
     * @param EntityManager $em
     * @param EngineInterface $templating
     * @param Request $request
     */
    public function __construct(EntityManager $em, EngineInterface $templating, Request $request)
    {
        $this->request = $request;
        $this->em = $em;
        $this->templating = $templating;
        $this->currentPath = $this->request->getPathInfo();
    }


    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $repository = $this->em->getRepository('CoreBundle:AdminSetting');

        if (!$repository->isMaintenanceMode()
            || stripos($this->currentPath, '/admin') === 0
            || stripos($this->currentPath, '/_') === 0
        ) {
            return;
        }

        $event->setResponse(
            $this->templating->renderResponse('CoreBundle:State:Maintenance.html.twig')
        );


    }

}
