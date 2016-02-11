<?php

namespace Domain\AdminBundle\Admin;

//use Domain\CoreBundle\Entity\Company;
use Domain\CoreBundle\Entity\Education;
use Domain\CoreBundle\Entity\Experience;
//use Domain\CoreBundle\Entity\Interest;
use Domain\CoreBundle\Entity\Skill;
use Domain\CoreBundle\Entity\User;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Exception;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ExpertRequestAdmin
 */
class ExpertRequestAdmin extends Admin
{

    protected $baseRouteName = 'expert_requests';
    protected $baseRoutePattern = 'expert_requests';

    protected $formOptions = array(
        'validation_groups' => array()
    );

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }


    /**
     * Edit form
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {

        $formMapper
            ->with('General')
                ->add(
                    'message',
                    'textarea',
                    [
                        'label' => 'Message for requester',
                        'required' => false
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

    /**
     * List page
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('user.first_name', null, array('label' => 'First Name'))
            ->add('user.last_name', null, array('label' => 'Last Name'))
            ->add('user.email', null, array('label' => 'User'))
            ->add(
                'user.linkedinProfileLink',
                'external_link',
                [
                    'label' => 'LinkedIn Profile',
                    'sortable' => false,
                    'title' => 'View'
                ]
            )
            ->add('created', null, [])
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'edit' => []
                    ]
                ]
            )
            //->addIdentifier('user.email')
            //->addIdentifier('message')
            //->addIdentifier('user.linkedinProfileLink')
        ;
    }

    /**
     * Dont show users without linkedin accounts
     *
     * @param string $context
     * @return \Sonata\AdminBundle\Datagrid\ProxyQueryInterface|ProxyQuery
     */
    public function createQuery($context = 'list')
    {


        $queryBuilder = $this->getModelManager()
            ->getEntityManager($this->getClass())
            ->getRepository($this->getClass())
            ->queryBuilderActiveRequests();

        $queryBuilder->getDqlPart('orderBy');


        $proxyQuery = new ProxyQuery($queryBuilder);
        return $proxyQuery;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
        $collection->remove('delete');
        $collection->remove('export');
        $collection->remove('show');
    }

    public function getTemplate($name)
    {

        switch($name){
            case 'edit':
                return 'AdminBundle:CRUD:base_edit.html.twig';
            case 'list':
                return 'AdminBundle:CRUD:list.html.twig';
            default:
                return parent::getTemplate($name);
        }
    }

    public function postUpdate($expertRequestObj)
    {
        $expert_manager = $this->container->get('expert_manager');

        $expert_manager->setActiveRequest($expertRequestObj);

        $action = $this->request->request->get('action');
        $form_data = $this->request->request->get($this->getForm()->getName(), []);

        try{
            switch ($action) {
                case 'approve':
                    $expert_manager->approve();
                    break;
                case 'decline':
                    $expert_manager->decline();
                    break;
            }
        } catch(Exception $e){
            $flashBag = $this->request->getSession()->getFlashBag();
            $flashBag->clear();
            $flashBag->add('sonata_flash_error', $e->getMessage());
        }


    }


    public function getBatchActions()
    {
        if (!$this->isGranted('EDIT') || !$this->isGranted('DELETE')) {
            return [];
        }

        return [
            'approve' => [
                'label' => 'Approve',
                'ask_confirmation' => true
            ],
            'decline' => [
                'label' => 'Decline',
                'ask_confirmation' => true
            ]
        ];

    }
}
