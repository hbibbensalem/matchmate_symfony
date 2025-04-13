<?php

namespace App\Form;

use App\Entity\Match1;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface; // Add this import

class Match1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $regions = [
            'Ariana', 'Béja', 'Ben Arous', 'Bizerte', 'Gabès', 
            'Gafsa', 'Jendouba', 'Kairouan', 'Kasserine', 'Kébili',
            'Le Kef', 'Mahdia', 'La Manouba', 'Médenine', 'Monastir',
            'Nabeul', 'Sfax', 'Sidi Bouzid', 'Siliana', 'Sousse',
            'Tataouine', 'Tozeur', 'Tunis', 'Zaghouan'
        ];

        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'attr' => ['min' => (new \DateTime())->format('Y-m-d')]
            ])
            ->add('heure', TextType::class, [
                'attr' => ['placeholder' => 'HH:MM']
            ])
            ->add('localisation', ChoiceType::class, [
                'choices' => array_combine($regions, $regions),
                'placeholder' => 'Choisissez une région'
            ])
            ->add('terrain', TextType::class, [
                'attr' => ['placeholder' => 'Nom du terrain/club']
            ])
            ->add('nb_personne', ChoiceType::class, [
                'choices' => [
                    '2 joueurs' => 2,
                    '4 joueurs' => 4
                ],
                'empty_data' => 2, // Valeur par défaut
                'getter' => function (Match1 $match, FormInterface $form): int {
                    try {
                        return $match->getNb_personne();
                    } catch (\Error $e) {
                        return 2; // Fallback value
                    }
                },
                'setter' => function (Match1 $match, $value): void {
                    $match->setNb_personne((int)$value);
                }
            ])
            ->add('typesport', ChoiceType::class, [
                'choices' => [
                    'Padel' => 'Padel',
                    'Ping-pong' => 'Ping-pong',
                    'Tennis' => 'Tennis'
                ],
                'empty_data' => 'Padel' // Valeur par défaut
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'empty_data' => '' // Valeur par défaut
            ]);
            
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Match1::class,
            'empty_data' => function () {
                $match = new Match1();
                $match->setNb_personne(2); // Initialisation par défaut
                $match->setStatut('En attente');
                return $match;
            }
        ]);
    }
}