<?php

namespace Domain\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email')
            ->add('first_name')
            ->add('last_name')
            ->add(
                'plainPassword',
                'repeated',
                [
                    'first_options'  => array('label' => 'Password'),
                    'second_options' => array('label' => 'Confirm Password'),
                    'type' => 'password',
                    'invalid_message' => 'The password fields must match.',
                ]
            )
            ->add('terms');

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Domain\CoreBundle\Entity\User',
                'validation_groups' => array('registration')
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'userType';
    }
}
