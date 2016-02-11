<?php
/**
 * User: Dred
 * Date: 10.10.13
 * Time: 11:29
 */

namespace Ikantam\FilesBundle\Service;

use \Symfony\Component\DependencyInjection\ContainerInterface;
use \finfo;
use \stdClass;

class Downloader
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $download_path;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->upload_path = $this->container->get('kernel')->getRootDir() . '/../web'
            . $this->container->getParameter('upload_handler.upload_directory')
        ;
        $this->download_path = $this->upload_path
            . $this->container->getParameter('upload_handler.temp_directory')
        ;

    }

    /**
     * Try to download file
     *
     * @param $url
     *
     * @return null|stdClass
     */
    public function download($url)
    {
        $filename = basename($url);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);


        $data = @file_get_contents($url);

        if (!$data) {
            return null;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($data);

        if (empty($ext) && ($ext = $this->getExtByMime($mime))) {
            $filename.='.'.$ext;
        }

        $new_filepath = $this->download_path.$filename;

        file_put_contents($new_filepath, $data);

        unset($data);

        $_file = new stdClass;
        $_file->name = $filename;
        $_file->type = $mime;
        $_file->size = filesize($new_filepath);
        $_file->path = $new_filepath;

        return $_file;
    }

    /**
     * Generate file path for user to save file
     * 
     * @param string $relativePath - path relative to upload folder
     * @return string
     */
    public function filePath($relativePath)
    {
        return $this->upload_path . $relativePath;
    }

    protected function getExtByMime($mime)
    {
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/gif' => 'gif',
            'image/png' => 'png',
        ];

        return isset($extensions[$mime]) ? $extensions[$mime] : null;
    }

}
