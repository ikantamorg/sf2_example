<?php
/**
 * User: Dred
 * Date: 02.10.13
 * Time: 15:31
 */

namespace Domain\CoreBundle\Controller;

use HWI\Bundle\OAuthBundle\Controller\ConnectController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class OAuthController extends ConnectController
{
    public function connectServiceAction(Request $request, $service)
    {

        $connect = $this->container->getParameter('hwi_oauth.connect');
        if (!$connect) {
            throw new NotFoundHttpException();
        }

        $hasUser = $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED');
        if (!$hasUser) {
            return $this->redirectPath('main_home');
        }

        // Get the data from the resource owner
        $resourceOwner = $this->getResourceOwnerByName($service);

        $session = $request->getSession();
        $key = $request->query->get('key', time());

        if ($resourceOwner->handles($request)) {
            $accessToken = $resourceOwner->getAccessToken(
                $request,
                $this->generate('hwi_oauth_connect_service', array('service' => $service), true)
            );

            // save in session
            $session->set('_hwi_oauth.connect_confirmation.'.$key, $accessToken);
        } else {
            $accessToken = $session->get('_hwi_oauth.connect_confirmation.'.$key);
        }
        if (!$accessToken) {
            return $this->redirectPath('main_home');
        }
        $userInformation = $resourceOwner->getUserInformation($accessToken);

        /** @var $currentToken OAuthToken */

        $currentToken = $this->container->get('security.context')->getToken();

        $currentUser  = $currentToken->getUser();

        $this->container->get('hwi_oauth.account.connector')->connect($currentUser, $userInformation);

        $this->authenticateUser($request, $currentUser, $service, $accessToken, false);


        return $this->redirectPath('main_home');
    }


    /**
     * Short method for redirecting to local path
     *
     * @param $path
     * @param array $params
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function redirectPath($path, $params = [])
    {
        return $this->redirect($this->generateUrl($path, $params));
    }
    /**
     * Generates a URL from the given parameters.
     *
     * @param string         $route         The name of the route
     * @param mixed          $parameters    An array of parameters
     * @param Boolean|string $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
     *
     * @return string The generated URL
     *
     * @see UrlGeneratorInterface
     */
    public function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }


    /**
     * Returns a RedirectResponse to the given URL.
     *
     * @param string  $url    The URL to redirect to
     * @param integer $status The status code to use for the Response
     *
     * @return RedirectResponse
     */
    public function redirect($url, $status = 302)
    {
        return new RedirectResponse($url, $status);
    }

    /**
     * Gets a service by id.
     *
     * @param string $id The service id
     *
     * @return object The service
     */
    public function get($id)
    {
        return $this->container->get($id);
    }
}
