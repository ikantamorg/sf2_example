<?php
/**
 * User: alkuk
 * Date: 16.02.14
 * Time: 2:19
 */

namespace Domain\CoreBundle\Service\FileSystem\Interfaces;

interface VideoInterface
{

    /**
     * Return uri to video by video identifier
     *
     * @param $id
     *
     * @return string
     */
    public function getVideoUri($id);

}
 