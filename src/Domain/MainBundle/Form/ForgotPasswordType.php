<?php
/**
 * User: Dred
 * Date: 20.05.13
 * Time: 17:25
 */

namespace Domain\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ForgotPasswordType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'email',
            'email',
            [
                'attr' => [
                    'placeholder' => 'Enter your email address',
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Email(),
                ]
            ]
        );

    }


    public function getName()
    {
        return 'forgot_password';
    }
}
