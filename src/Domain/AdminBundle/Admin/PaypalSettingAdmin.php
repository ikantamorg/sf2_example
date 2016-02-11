<?php

namespace Domain\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

class PaypalSettingAdmin extends Admin
{
    protected $baseRouteName = 'paypal_setting';
    protected $baseRoutePattern = 'paypal_setting';
    protected $perPageOptions = [];

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
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

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('value')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('label')
            ->add('value')
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        // Only `list` route will be active
        $collection->clearExcept(array('edit', 'list'));
    }

    public function createQuery($context = 'list')
    {
        switch ($context) {
            case 'list':
                $qb = $this->getModelManager()
                    ->getEntityManager($this->getClass())
                    ->getRepository($this->getClass())
                    ->getPaymentQB();
                break;
            default:
                return parent::createQuery($context);
        }

        $proxyQuery = new ProxyQuery($qb);
        return $proxyQuery;
    }

}
