<?php
/**
 * User: dev
 * Date: 09.10.13
 * Time: 12:19
 */

namespace Domain\CoreBundle\DataFixtures\ORM;

use Hautelook\AliceBundle\Alice\DataFixtureLoader;
use Nelmio\Alice\Fixtures;
use Doctrine\Common\Persistence\ObjectManager;
use \Weotch\Faker\Provider\Image;

class ExpertsLoader extends DataFixtureLoader
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $this->manager = $manager;
        /** @var $loader \Hautelook\AliceBundle\Alice\Loader */
        $loader = $this->container->get('hautelook_alice.loader');
        $loader->setObjectManager($manager);

        $loader->setProviders(array($this, new Image));

        foreach ($this->getProcessors() as $processor) {
            $loader->addProcessor($processor);
        }

        $loader->load($this->getFixtures());
    }

    /**
     * {@inheritDoc}
     */
    protected function getFixtures()
    {
        return [];

        $kernel = $this->container->get('kernel');

        return  [
            $kernel->locateResource('@CoreBundle/Resources/config/fixtures.yml')
        ];

    }
}
