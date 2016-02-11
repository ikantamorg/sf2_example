<?php

namespace Domain\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LoginType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add(
                    'email',
                    null,
                    array(
                        'label' => false,
                        'invalid_message' => "Not an integer",
                        'attr' => array(
                            'autocomplete' => 'off',
                            'tabindex' => 1,
                            'maxlength' => 50,
                            'placeholder' => 'Email',
                        )
                    )
                )
                ->add(
                    'password',
                    'password',
                    array(
                        'label' => false,
                        'attr' => array(
                            'autocomplete' => 'off',
                            'tabindex' => 2,
                            'maxlength' => 50,
                            'placeholder' => 'Password',
                    ),
                    )
                );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'intention' => 'authenticate'
            )
        );
    }


    public function getName()
    {
        return 'login';
    }
}
