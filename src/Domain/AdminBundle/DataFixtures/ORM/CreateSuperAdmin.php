<?php
/**
 * User: Dred
 * Date: 22.05.13
 * Time: 13:55
 */

namespace Domain\AdminBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CreateSuperAdmin implements FixtureInterface, ContainerAwareInterface
{

    /**
    * @var ContainerInterface
    */
    private $container;


    /**
     * get admin credentials
     *
     * @return array
     */
    public function getAdminData()
    {
        return [
            'email' => 'admin@admin.com',
            'password' => 'password'
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $admin = $this->getAdminData();

        $userManager = $this->container->get('fos_user.user_manager');

        $userAdmin = $userManager->findUserByEmail($admin['email']);
        if ($userAdmin) {
            return;
        }
        $userAdmin = $userManager->createUser();

        $userAdmin->setEmail($admin['email'])
            ->setUsername($admin['email'])
            ->setPlainPassword($admin['password'])
            ->setFirstName('')
            ->setLastName('')
            ->setEnabled(true)
            ->setTerms(true);

        $userAdmin->addRole("ROLE_SUPER_ADMIN");

        $userManager->updateUser($userAdmin, true);
    }
}
