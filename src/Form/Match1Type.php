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
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Form\FormInterface; // Add this import
use Symfony\Component\Validator\Constraints\Regex;

class Match1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        

     
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d'),
                    'class' => 'form-control'
                ],
                
            ])
            ->add('heure', TextType::class, [
                'attr' => [
                    'placeholder' => 'HH:MM',
                    'class' => 'form-control'
                ],
                
            ])
            ->add('localisation', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'localisation',
                    'placeholder' => 'Sélectionnez sur la carte'
                ],
                'label' => 'Localisation*',
                
            ])
            ->add('terrain', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'terrain',
                    'readonly' => true
                ],
                'label' => 'Adresse complète',
                'required' => false,
                
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
                'attr' => ['class' => 'form-control'],
               
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 3],
                
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Match1::class,
            'empty_data' => function () {
                $match = new Match1();
                $match->setNb_personne(2);
                $match->setStatut('En attente');
                return $match;
            }
        ]);
    }
}