<?php
/**
 * User: Dred
 * Date: 13.12.13
 * Time: 14:21
 */

namespace Domain\CoreBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener as TwigExceptionListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\SecurityContextInterface;

class ExceptionListener extends TwigExceptionListener
{

    /**
     * @var \Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine
     */
    protected $tempalting;

    protected $kernel;

    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    private $security;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    public function __construct(
        $controller,
        LoggerInterface $logger = null,
        $templating,
        $kernel,
        SecurityContextInterface $security,
        Router $router
    ) {
        parent::__construct($controller, $logger);

        $this->tempalting = $templating;
        $this->kernel = $kernel;

        $this->security = $security;
        $this->router = $router;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {

        static $handling;

        if (true === $handling) {
            return false;
        }

        $isAjax = $event->getRequest()->isXmlHttpRequest();

        if (!$isAjax && $this->redirectFromCandidateProfileToExpertProfile($event)) {
            $handling = true;

            return;
        }

        if ('prod' != $this->kernel->getEnvironment() && !$isAjax) {
            return;
        }



        $handling = true;

        $exception = $event->getException();
        $request = $event->getRequest();

        $this->logException(
            $exception,
            sprintf(
                'Uncaught PHP Exception %s: "%s" at %s line %s',
                get_class($exception),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            )
        );

        $exc = FlattenException::create($exception);
        $code = $exc->getStatusCode();

        $response = new Response();

        if ($isAjax) {
            $content = $exception->getMessage();
        } else {
            $content = $this->tempalting->render(
                'CoreBundle:Exception:errorpage.html.twig',
                [
                    'exception' => $exception,
                    'status_code'    => $code,
                    'status_text'    => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
                    'exception'      => $exc,
                    'logger'         => $this->logger
                ]
            );
        }

        $response->setContent(
        // create you custom template AcmeFooBundle:Exception:exception.html.twig
            $content
        );

        // HttpExceptionInterface is a special type of exception
        // that holds status code and header details

        if ($exc instanceof HttpExceptionInterface || $exc instanceof FlattenException) {
            $response->setStatusCode($exc->getStatusCode());
            $response->headers->replace($exc->getHeaders());
        } else {
            $response->setStatusCode(500);
        }



        $event->setResponse($response);

        $handling = false;
    }


    /**
     * Redirect Expert from candidate profile
     *
     * @param GetResponseForExceptionEvent $event
     *
     * @return bool
     */
    protected function redirectFromCandidateProfileToExpertProfile(GetResponseForExceptionEvent $event)
    {

        if ($this->security->getToken() && $this->security->isGranted('ROLE_EXPERT')) {

            $candidateProfilePath = $this->router->getRouteCollection()->get('candidate_profile_default')->getPath();

            $request = $event->getRequest();

            // redirect expert from candidate profile to expert profile
            if (stripos($request->getPathInfo(), $candidateProfilePath) === 0) {
                $url = $this->router->generate('expert_profile_edit');
                $event->setResponse(new RedirectResponse($url));

                return true;
            }

        }

        return false;
    }
}
 