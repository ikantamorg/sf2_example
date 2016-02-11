<?php
/**
 * User: Dred
 * Date: 30.09.13
 * Time: 14:07
 */

namespace Domain\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\File\File;

class CoreController extends Controller
{
    /**
     * {@inherit}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->init();
    }

    /**
     * This function run after controller got service container
     */
    protected function init()
    {
    }

    /**
     * Get parameter from service container
     *
     * @param $name The parameter name
     *
     * @return mixed The parameter value
     */
    protected function getParameter($name)
    {
        return $this->container->getParameter($name);
    }

    /**
     * Checks if a parameter exists.
     *
     * @param $name The parameter name
     *
     * @return bool The presence of parameter in container
     */
    protected function hasParameter($name)
    {
        return $this->container->hasParameter($name);
    }

    /**
     * Short method for adding something to flashbag
     *
     * @param $type
     * @param $message
     *
     * @return $this
     */
    protected function addFlash($type, $message)
    {
        $this->get('session')->getFlashBag()->add($type, $message);
        return $this;
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
     * Return Json Response
     *
     * @param $data
     * @param int $code
     *
     * @return JsonResponse
     */
    protected function jsonResponse($data, $code = 200, $headers = array())
    {
        if (empty($headers['Content-Type']) && $this->has('request')) {
            $request = $this->get('request');

            // set output as text/html for old browsers
            if (strpos($request->server->get('HTTP_USER_AGENT'), 'MSIE') !== false) {
                $headers['Content-Type'] = 'text/html';
            }
        }

        return new JsonResponse($data, $code, $headers);
    }

    /**
     * Return Json Response with 403(Forbidden) code
     *
     * @param $data
     * @param array $headers
     *
     * @return JsonResponse
     */
    public function jsonForbiddenResponse($data, $headers = array())
    {
        return $this->jsonResponse($data, 403, $headers);
    }

    /**
     * Get entity manager
     *
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    protected function getEm()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * Get EM repository
     *
     * @param $name
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getRepository($name)
    {
        return $this->getEm()->getRepository($name);
    }


    /**
     * Returns a AccessDeniedHttpException.
     *
     * This will result in a 403 response code. Usage example:
     *
     *     throw $this->createAccessDeniedException('Access Denied!');
     *
     * @param string    $message  A message
     * @param \Exception $previous The previous exception
     *
     * @return AccessDeniedHttpException
     */
    public function createAccessDeniedException($message = 'Access Denied', \Exception $previous = null)
    {
        return new AccessDeniedHttpException($message, $previous);
    }

    /**
     * Create Response
     *
     * @param string $content
     * @param int $status
     * @param array $headers
     *
     * @return Response
     */
    public function response($content = '', $status = 200, $headers = array())
    {
        return new Response($content, $status, $headers);
    }

    /**
     * Create response for mod_xsendfile
     *
     * @param $filePath
     * @param null $fileName
     * @param null $contentType
     *
     * @return Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function privateFileResponse($filePath, $fileName = null, $contentType = null)
    {

        $mods = apache_get_modules();

        if (array_search('mod_xsendfile',$mods) === false){
            throw $this->createAccessDeniedException('Invalid server configuration.');
        }

        // check if file exists
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException();
        }

        $file = new File($filePath);

        if (!$file->isReadable()) {
            throw $this->createNotFoundException();
        }

        $contentType = $contentType ?: $file->getMimeType();

        $response = new Response();
        $response->headers->set('X-Sendfile', $file->getRealPath());
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $fileName));
        $response->headers->set('Content-Type', $contentType);

        return $response;
    }
}