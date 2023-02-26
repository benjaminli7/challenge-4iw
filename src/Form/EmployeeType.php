<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Regex;


class EmployeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez un prénom',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Prénom',
                ],
                'required' => true,

            ])
            ->add('lastName', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez un nom',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Nom',
                ],
                'required' => true,
            ])
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
            ->add('phone', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez un numéro de téléphone',
                    ]),
                    new Length([
                        'max' => 10,
                        'maxMessage' => 'Le numéro de téléphone ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                    new Regex([
                        'pattern' => '/^[0-9]+$/',
                        'message' => 'Le numéro de téléphone ne doit contenir que des chiffres.',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Téléphone',
                ],
                'required' => true,
            ]);




        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $employee = $event->getData();
            $form = $event->getForm();
    
            // checks if the Employee object is "new"
            // If no data is passed to the form, the data is "null".
            // This should be considered a new "Employee"
            if (!$employee || null === $employee->getId()) {
                $form->add('plainPassword', RepeatedType::class, [
                    'invalid_message' => 'Les mots de passe doivent correspondre.',
                    'first_options'  => ['label' => 'Mot de passe', 'attr' => ['placeholder' => 'Mot de passe']],
                    'second_options' => ['label' => 'Confirmation du mot de passe', 'attr' => ['placeholder' => 'Confirmation du mot de passe']],
                    'type' => PasswordType::class,
                    'required' => true,
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
