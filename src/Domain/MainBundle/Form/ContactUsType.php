<?php

namespace Domain\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactUsType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'first_name',
                'text',
                array(
                    'label' => 'First Name',
                    'invalid_message' => "Not an integer",
                    'attr' => array(
                        'autocomplete' => 'off',
                        'tabindex' => 1,
                        'maxlength' => 50,
                    ),
                    'required' => true,
                    'constraints' => array(
                        new NotBlank(),
                    )
                )
            )
            ->add(
                'last_name',
                'text',
                array(
                    'label' => 'Last Name',
                    'invalid_message' => "Not an integer",
                    'attr' => array(
                        'autocomplete' => 'off',
                        'tabindex' => 1,
                        'maxlength' => 50,
                    ),
                    'required' => true,
                    'constraints' => array(
                        new NotBlank(),
                    )
                )
            )
            ->add(
                'phone',
                'text',
                array(
                    'label' => 'Phone',
                    'attr' => array(
                        'autocomplete' => 'off',
                        'tabindex' => 1,
                        'maxlength' => 50,
                    ),
                    'required' => true,
                    'constraints' => array(
                        new NotBlank(),
                    )
                )
            )
            ->add(
                'email',
                'email',
                array(
                    'label' => 'Email',
                    'attr' => array(
                        'autocomplete' => 'off',
                        'tabindex' => 1,
                        'maxlength' => 50,
                    ),
                    'required' => true,
                    'constraints' => array(
                        new Email(),
                        new NotBlank()
                    )
                )
            )
            ->add(
                'message',
                'textarea',
                array(
                    'label' => 'Message',
                    'attr' => array(
                        'autocomplete' => 'off',
                        'tabindex' => 1,
                        'maxlength' => 50,
                    ),
                    'required' => true,
                    'constraints' => array(
                        new NotBlank(),
                    )
                )
            )
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array());
    }


    public function getName()
    {
        return 'contact_us';
    }
}
