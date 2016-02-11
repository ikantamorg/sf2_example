<?php

namespace Domain\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class AppointmentProblemAdmin extends Admin
{
    /**
     * @var
     */
    protected $securityContext;

    public function setSecurityContext($securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * Default Datagrid values
     *
     * @var array
     */
    protected $datagridValues = array(
        '_page' => 1, // Display the first page (default = 1)
        '_sort_order' => 'DESC', // Descendant ordering (default = 'ASC')
        '_sort_by' => 'createdAt' // name of the ordered field (default = the model id field, if any)
    );

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('resolved')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, [
                'sortable' => false,
            ])
            ->add('createdAt')
            ->add('user.fullName', null, [
                'label' => 'User'
            ])
            ->add('user.email', null, [
                'label' => 'Email',
                'sortable' => false,
            ])
            ->add('resolved', null, [
                'label' => 'Resolved?',
                'sortable' => false,
            ])
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                )
            ))
        ;
    }

    public function getTemplate($name)
    {

        switch($name){
            case 'edit':
                return 'AdminBundle:AppointmentProblem:base_edit.html.twig';
            default:
                return parent::getTemplate($name);
        }
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('resolved', null, [
                'required' => false
            ])
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        // Only `list` route will be active
        $collection->clearExcept(array('edit', 'list'));
    }

    public function preUpdate($object)
    {
        if ($object->isResolved()) {
            $user = $this->securityContext->getToken()->getUser();
            $object->setResolvedAt(new \DateTime());
            $object->setResolver($user);
        } else {
            $object->setResolvedAt(null);
            $object->setResolver(null);
        }

    }
}
