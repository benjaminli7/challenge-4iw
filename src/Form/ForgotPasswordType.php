<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForgotPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez une adresse email',
                    ]),
                    new Email([
                        'message' => 'Entrez une adresse email valide',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Email',
                ],
                'required' => true,
            ])
        ;
    }
}