<?php
namespace App\Form;

use App\Entity\Regime;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Range;

class RegimeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('objectif', ChoiceType::class, [
            'choices' => [
                'Maintien de poids' => 'Maintien de poids',
                'Maintien de masse' => 'Maintien de masse',
                'Perte de masse' => 'Perte de masse',
            ],
            'placeholder' => 'Sélectionnez un objectif', // optionnel : affiche une valeur vide
            'attr' => ['class' => 'form-control'], // pour garder ton beau design Bootstrap
        ])
        ->add('calories_journalieres', null, [
            'constraints' => [
                new Range([
                    'min' => 1200,
                    'max' => 3500,
                    'notInRangeMessage' => 'Les calories doivent être entre {{ min }} et {{ max }} kcal.',
                ]),
            ],
            'attr' => [
                'class' => 'form-control',
                'min' => 1200,
                'max' => 3500,
            ],
        ])
            ->add('proteines', IntegerType::class, [
                'constraints' => [
                    new Assert\Range([
                        'min' => 100,
                        'max' => 250,
                        'notInRangeMessage' => 'Les protéines doivent être entre {{ min }}g et {{ max }}g.',
                    ])
                ],
                'attr' => ['class' => 'form-control',
                    'min' => 100,
                    'max' => 250,
                ],
            ])

            ->add('glucides', IntegerType::class, [
                'constraints' => [
                    new Range([
                        'min' => 50,
                        'max' => 500,
                        'notInRangeMessage' => 'Les glucides doivent être entre {{ min }}g et {{ max }}g.',
                    ]),
                ],
            ])
            ->add('lipides', IntegerType::class, [
                'constraints' => [
                    new Range([
                        'min' => 10,
                        'max' => 100,
                        'notInRangeMessage' => 'Les lipides doivent être entre {{ min }}g et {{ max }}g.',
                    ]),
                ],
            ])
            ->add('date_debut', null, [
                'label' => 'Date de début',
                'widget' => 'single_text',
            ])
            ->add('date_fin', null, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
            ])
            ->add('statut')
            ->add('idUser', EntityType::class, [
                'class' => User::class,
                'disabled' => true,
                'label' => 'Utilisateur',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Regime::class,
        ]);
    }
}
