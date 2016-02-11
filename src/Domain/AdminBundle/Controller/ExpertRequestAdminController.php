<?php
/**
 * User: Dred
 * Date: 24.09.13
 * Time: 11:07
 */

namespace Domain\AdminBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Exception;

class ExpertRequestAdminController extends Controller
{
    /**
     * Get entities from request
     *
     * @return null | array
     */
    protected function getBulkActionEntities()
    {

        $request = $this->get('request');

        $data = $request->request->all();

        $entities = null;

        $repository = $this->admin->getModelManager()->getEntityManager($this->admin->getClass())->getRepository($this->admin->getClass());

        if (!empty($data['all_elements'])) {
            $entities = $repository->findAllActive();
        } elseif (!empty($data['idx'])) {
            $entities = $repository->findActiveByIds($data['idx']);
        }


        return $entities;
    }

    public function batchActionDecline(ProxyQueryInterface $selectedModelQuery)
    {
        if (!$this->admin->isGranted('EDIT') || !$this->admin->isGranted('DELETE')) {
            throw new AccessDeniedException();
        }


        $entities = $this->getBulkActionEntities();

        if (!$entities || !count($entities)) {
            $this->addFlash('sonata_flash_info', 'Please select one or more rows.');

            return $this->redirect($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
        }

        $expert_manager = $this->get('expert_manager');


        foreach ($entities as $_entity) {
            $expert_manager->setActiveRequest($_entity)->decline();
        }


        $this->addFlash('sonata_flash_success', count($entities) . ' row(s) succesfully declined.');

        return $this->redirect($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }

    public function batchActionApprove(ProxyQueryInterface $selectedModelQuery)
    {
        if (!$this->admin->isGranted('EDIT') || !$this->admin->isGranted('DELETE')) {
            throw new AccessDeniedException();
        }


        $entities = $this->getBulkActionEntities();

        if (!$entities || !count($entities)) {
            $this->addFlash('sonata_flash_info', 'Please select one or more rows.');

            return $this->redirect($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
        }

        $expert_manager = $this->get('expert_manager');


        $successCount = 0;

        foreach ($entities as $_entity) {
            try {
                $expert_manager->setActiveRequest($_entity)->approve();
            } catch (Exception $e) {
                $this->addFlash('sonata_flash_error', $e->getMessage());
                continue;
            }
            $successCount++;
        }

        if ($successCount) {
            $this->addFlash('sonata_flash_success', $successCount . ' row(s) succesfully approved.');
        }


        return $this->redirect($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }
}




