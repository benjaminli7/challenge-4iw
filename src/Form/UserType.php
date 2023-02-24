<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez votre prénom',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Prénom',
                ],
                'required' => true,
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez votre nom',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Nom',
                ],
                'required' => true,
            ])
            ->add('plainPassword', RepeatedType::class, [
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'first_options'  => ['label' => 'Mot de passe', 'attr' => ['placeholder' => 'Mot de passe']],
                'second_options' => ['label' => 'Confirmation du mot de passe', 'attr' => ['placeholder' => 'Confirmation du mot de passe']],
                'type' => PasswordType::class,
                'required' => true,
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $user = $event->getData();
            $form = $event->getForm();

            // if user is not new, remove email field
            if ($user->getFirstname() !== null && $user->getLastname() !== null) {
                $form->remove('email');
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
