<?php
/**
 * User: Dred
 * Date: 24.12.13
 * Time: 10:55
 */

namespace Domain\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\AdminBundle\Form\FormMapper;

class SitePreferencesAdmin extends Admin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        // Only `list` route will be active
        $collection->clearExcept(array('edit', 'list'));
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name')
            ->add('label')
            ->add('value')
            ->add('_action', 'actions', array(
                    'actions' => array(
                        'show' => array(),
                        'edit' => array(),
                    )
                ))
        ;
    }

    public function createQuery($context = 'list')
    {
        switch ($context) {
            case 'list':
                $qb = $this->getModelManager()
                    ->getEntityManager($this->getClass())
                    ->getRepository($this->getClass())
                    ->getSitePreferencesQB();
                break;
            default:
                return parent::createQuery($context);
        }

        $proxyQuery = new ProxyQuery($qb);
        return $proxyQuery;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('value')
        ;

        // 'checkbox', ['label' => 'Switch on maintenance mode?']
    }

}
 