<?php
/**
 * User: alkuk
 * Date: 28.02.14
 * Time: 16:54
 */

namespace Domain\CoreBundle\Command\Abstracts;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class Task extends ContainerAwareCommand
{

    /**
     * {@inheritdoc} + set Request
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);

        // Set Request for normal functionality for all other part of application
        if ($container) {
            $container->enterScope('request');
            $container->set('request', new Request(), 'request');
        }
    }

}
 