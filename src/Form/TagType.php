<?php

namespace App\Form;

use App\Entity\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;

class TagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez un tag',
                    ]),
                ],
                'label' => 'Nom du tag',
                'attr' => [
                    'placeholder' => 'Nom du tag',
                ],
                'required' => true,
            ])
            ->add('color', ColorType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez une couleur',
                    ]),
                ],
                'label' => 'Couleur du tag',
                'attr' => [
                    'placeholder' => 'Couleur du tag',
                ],
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
        ]);
    }
}
