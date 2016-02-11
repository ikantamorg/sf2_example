<?php
/**
 * User: alkuk
 * Date: 16.02.14
 * Time: 1:33
 */

namespace Domain\CoreBundle\Service\FileSystem;

use Domain\CoreBundle\Service\FileSystem\Interfaces\VideoInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LocalVideo
 */
class LocalVideo implements VideoInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var string
     */
    protected $videoUri;

    /**
     * @var string
     */
    protected $videoExt;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $fsVideoParams = array_merge([
                'video' => [
                    'base_path' => '',
                    'extension' => '',
                ]
            ], (array)$this->container->getParameter('file_system'));

        $this->videoExt = $fsVideoParams['video']['extension'];

        $this->setBasePath($fsVideoParams['video']['base_path']);
    }

    /**
     * {@inheritdoc}
     */
    public function getVideoUri($id)
    {
        return $this->videoUri . $id . '.' . $this->videoExt;
    }

    /**
     * Set base path to videos
     *
     * @param $basePath
     */
    protected function setBasePath($basePath)
    {
        //add first and last slash
        $pathLength = mb_strlen($basePath);

        if ($pathLength && $basePath[0] != DIRECTORY_SEPARATOR) {
            $basePath   = DIRECTORY_SEPARATOR . $basePath;
            $pathLength = mb_strlen($basePath);
        }
        if ($pathLength > 1 && $basePath[$pathLength - 1] != DIRECTORY_SEPARATOR) {
            $basePath .= DIRECTORY_SEPARATOR;
        }

        $this->basePath = $basePath;

        $this->modifyUri();
    }

    /**
     * Modify uri to videos
     */
    protected function modifyUri()
    {
        $request       = $this->container->get('request');
        $routerContext = $this->container->get('router')->getContext();


        $secure = $request->isSecure();

        $absoluteUrl = $routerContext->getScheme() . '://' . $routerContext->getHost();

        $port = $secure ? $routerContext->getHttpsPort() : $routerContext->getHttpPort();

        if (!empty($port) && (($secure && $port != 443) || (!$secure && $port != 80))) {
            $absoluteUrl .= ':' . $port;
        }

        $pattern = '#[^/]*.php$#';

        $basePath = rtrim(preg_replace($pattern, '', $routerContext->getBaseUrl()), '/');

        $this->videoUri = $absoluteUrl . $basePath . $this->basePath;
    }
}
 