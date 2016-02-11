<?php
/**
 * User: Dred
 * Date: 25.11.13
 * Time: 16:58
 */

namespace Domain\CoreBundle\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\FormLoginFactory;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

class RoleAccessFactory extends FormLoginFactory
{

    public function addConfiguration(NodeDefinition $node)
    {
        parent::addConfiguration($node);

        /**
         * @var \Symfony\Component\Config\Definition\ArrayNode
         */
        /**
         * @var \Symfony\Component\Config\Definition\Builder\NodeBuilder
         */
        $builder = $node->children();
        $builder->variableNode('allowed_roles')
/*            ->children()
                ->scalarNode('driver')->end()
                ->scalarNode('host')->end()
                ->scalarNode('username')->end()
                ->scalarNode('password')->end()
                ->end()
            ->end()*/
        ;

    }

    protected function getAuthProviderId()
    {
        return 'security.authentication.provider.core';
    }


    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {

        $provider = $this->getAuthProviderId();
        $providerId = $provider.'.'.$id;

        $providerDefinition = new DefinitionDecorator($provider);

        // pass roles as first argument
        $providerDefinition->addMethodCall('setAllowedRoles', [$config['allowed_roles']]);

        $container
            ->setDefinition($providerId, $providerDefinition)
            ->replaceArgument(0, new Reference($userProviderId))
            ->replaceArgument(2, $id);

        return $providerId;
    }

    public function getKey()
    {
        return 'roles-form-login';
    }

}