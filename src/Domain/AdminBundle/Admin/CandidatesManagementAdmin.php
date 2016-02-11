<?php
/**
 * User: dev
 * Date: 29.12.13
 * Time: 23:36
 */

namespace Domain\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

/**
 * Class CandidatesManagementAdmin
 */
class CandidatesManagementAdmin extends Admin
{
    protected $baseRouteName = 'candidates_management';
    protected $baseRoutePattern = 'candidates_management';
    protected $classnameLabel = 'Candidates';

    protected $userChangesSet;

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
            ->add('fullName')
            ->add('email')
            ->add('lastLogin')
            ->add('locked', 'string', [
                    'label' => 'Is Locked?',
                    'template' => 'AdminBundle:Fields:user_locked.html.twig'
                ])
            ->add('_action', 'actions', array(
                    'actions' => array(
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
                    ->getQBOfCandidates();
                break;
            default:
                return parent::createQuery($context);
        }

        $proxyQuery = new ProxyQuery($qb);
        return $proxyQuery;
    }

    /**
     * Edit form
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {

        $formMapper
            ->with('Blocking')
            ->add(
                'locked',
                'checkbox',
                [
                    'label' => 'Block candidate?',
                    'required' => false,
                ]
            )
            ->add(
                'reason',
                'textarea',
                [
                    'label' => 'Reason',
                    'required' => false,
                    "mapped" => false,
                ]
            )
            /*                ->add(
                                'approved',
                                'checkbox',
                                [
                                    'required' => false
                                ]
                            )*/

            ->end()
        ;
    }

    public function preUpdate($user)
    {

        $em = $this->getModelManager()->getEntityManager($this->getClass());


        $uow = $em->getUnitOfWork();
        $uow->computeChangeSets(); // do not compute changes if inside a listener

        $this->userChangesSet = $uow->getEntityChangeSet($user);
        //var_Dump(1, $user->isLocked(), $this->userChangesSet); exit;

    }

    public function postUpdate($user)
    {

        $container = $this->getConfigurationPool()->getContainer();

        if (isset($this->userChangesSet['locked'])) {
            $newValue = $this->userChangesSet['locked'][1];

            $reason = $this->getForm()->get('reason')->getData();

            $domainEventListener = $container->get('domain.core.event.listener');

            switch ($newValue) {
                case true:
                    $prefix = 'lock';
                    break;
                case false:
                    $prefix = 'unlock';
                    break;
            }

            $domainEventListener->dispatch($prefix.'_candidate', 'admin', $user, $reason);

        }
    }

} 