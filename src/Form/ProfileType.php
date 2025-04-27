<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email_user', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 180]),
                ]
            ])
            ->add('password_user', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
                'required' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new Length(['min' => 6]),
                ],
            ])
            ->add('nom_user', TextType::class, ['label' => 'Nom'])
            ->add('prenom_user', TextType::class, ['label' => 'Prénom'])
            ->add('telephone_user', TextType::class, ['label' => 'Téléphone'])
            ->add('adresse_user', TextType::class, ['label' => 'Adresse'])
            ->add('description_user', TextareaType::class, [
                'label' => 'Description',
                'required' => false
            ])
            ->add('date_naissance_user', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text'
            ])
            ->add('photo_user', FileType::class, [
                'label' => 'Photo de profil',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                    ])
                ]
            ])
            ->add('niveau_joueur', ChoiceType::class, [
                'label' => 'Niveau de jeu',
                'choices' => [
                    'Débutant' => 'DEBUTANT',
                    'Intermédiaire' => 'INTERMEDIAIRE',
                    'Expert' => 'EXPERT'
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('max_distance_user', NumberType::class, [
                'label' => 'Distance maximale (km)',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}