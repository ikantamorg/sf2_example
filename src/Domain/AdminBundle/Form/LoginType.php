<?php

namespace Domain\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LoginType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'email', array('label' => 'Email'  ));
        $builder->add('password', 'password', array('label' => 'Password' ));
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
