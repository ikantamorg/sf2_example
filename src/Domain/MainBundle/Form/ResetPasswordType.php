<?php
/**
 * User: Dred
 * Date: 15.10.13
 * Time: 15:36
 */

namespace Domain\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'plainPassword',
                'repeated',
                [
                    'first_options'  => [
                        'label' => false,
                        'attr' => [
                            'placeholder' => 'Password'
                        ]
                    ],
                    'second_options' => [
                        'label' => false,
                        'attr' => [
                            'placeholder' => 'Confirm Password'
                        ]
                    ],
                    'type' => 'password',
                    'invalid_message' => 'The password fields must match.',
                ]
            )
            ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'Domain\CoreBundle\Entity\User',
                'validation_groups' => ['password_reset']
            ]
        );
    }


    public function getName()
    {
        return 'password_reset';
    }
}
