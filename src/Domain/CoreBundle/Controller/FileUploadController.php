<?php
/**
 * User: Dred
 * Date: 08.11.13
 * Time: 15:42
 */

namespace Domain\CoreBundle\Controller;


class FileUploadController extends CoreController
{

    public function indexAction()
    {
        $uploadHandler = $this->get('i_uploader');
        $files = $uploadHandler->upload();

        $data = ['files' => []];

        foreach ($files['files'] as $file) {
            $data['files'][] = [
                'id' => $file->getId(),
                'name' => $file->getName(),
                'size' => $file->getSize(),
                'type' => $file->getType()
            ];
        }

        return $this->jsonResponse($data);
    }

}
 