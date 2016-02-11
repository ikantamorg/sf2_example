<?php
/**
 * User: alkuk
 * Date: 27.04.14
 * Time: 1:07
 */

namespace Domain\CoreBundle\Service\FileSystem;

use Domain\CoreBundle\Service\FileSystem\Interfaces\VideoInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Exception;

class PrivateVideo implements VideoInterface
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
     * {@inheritDoc}
     */
    public function getVideoUri($id)
    {
        return $this->container->get('router')->generate('main_file_video_download', ['id' => $id]);
    }

    /**
     * @param $filename
     *
     * @return null|string
     */
    public function getVideoPath($filename)
    {
        $pathToFile = $this->getPathToFile($filename);

        if (!file_exists($pathToFile)) {
            return null;
        }

        return $pathToFile;
    }

    /**
     * Set video path
     *
     * @param string $pathToVideo
     *
     * @throws \Exception
     */
    protected function setBasePath($pathToVideo)
    {
        $privatePath = $this->container->get('kernel')->getRootDir().'/../private/'.$pathToVideo;
        $this->basePath = realpath($privatePath);
        if (!$this->basePath) {
            throw new Exception(sprintf('The path %s cant be found', $privatePath));
        }
    }

    /**
     * Get path to file
     *
     * @param $filename
     *
     * @return string
     */
    protected function getPathToFile($filename)
    {
        return $this->basePath.'/'.$filename.'.'.$this->videoExt;
    }
}
