<?php
/**
 * User: Dred
 * Date: 01.11.13
 * Time: 12:51
 */

namespace Domain\CoreBundle\Listener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class AccessListener
{
    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    private $security;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * @var string current path
     */
    private $currentPath;

    public function __construct(SecurityContextInterface $security, RouterInterface $router, Request $request)
    {
        $this->security = $security;
        $this->router = $router;
        $this->request = $request;
        $this->currentPath = $this->request->getPathInfo();
    }

    public function onKernelRequest(GetResponseEvent $event)
    {

        if (!$this->security->getToken()) {
            return;
        }

        $this->redirectFormAuthPages($event);
    }



    /**
     * Redirect auth users from auth pages
     *
     * @param GetResponseEvent $event
     *
     * @return bool
     */
    protected function redirectFormAuthPages(GetResponseEvent $event)
    {

        if ($this->security->isGranted('ROLE_USER') && stripos($this->currentPath, '/auth') === 0) {

            if ($this->security->isGranted('ROLE_EXPERT')) {
                $route = 'expert_profile_edit';
            } elseif ($this->security->isGranted('ROLE_CANDIDATE')) {
                $route = 'candidate_profile_edit';
            } else {
                $route = 'main_home';
            }

            $url = $this->router->generate($route);

            $event->setResponse(new RedirectResponse($url));

            return true;
        }

        return false;
    }
}
 