<?php
// src/Form/UserType.php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Validator\Constraints\File;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $isEdit = $options['is_edit'] ?? false;
        
        $builder
            ->add('email_user', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank(['message' => 'L\'email ne peut pas être vide']),
                    new Length(['max' => 180]),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('password_user', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => !$isEdit,
                'mapped' => false,
                'constraints' => $isEdit ? [] : [
                    new NotBlank(['message' => 'Le mot de passe est obligatoire']),
                    new Length(['min' => 6, 'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères']),
                    new Regex([
                        'pattern' => '/\d/',
                        'message' => 'Le mot de passe doit contenir au moins un chiffre'
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'new-password'
                ]
            ])
            ->add('nom_user', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank(['message' => 'Le nom ne peut pas être vide']),
                    new Length(['max' => 50]),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('prenom_user', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank(['message' => 'Le prénom ne peut pas être vide']),
                    new Length(['max' => 50]),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('sexe_user', ChoiceType::class, [
                'label' => 'Sexe',
                'choices' => [
                    'Male' => 'M',
                    'Female' => 'F'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le sexe doit être sélectionné']),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('telephone_user', TextType::class, [
                'label' => 'Téléphone',
                'constraints' => [
                    new NotBlank(['message' => 'Le téléphone ne peut pas être vide']),
                    new Regex([
                        'pattern' => '/^[259][0-9]*$/',
                        'message' => 'Le téléphone doit commencer par 2, 5 ou 9'
                    ]),
                    new Length(['max' => 15]),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('description_user', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 3]
            ])
            ->add('adresse_user', TextType::class, [
                'label' => 'Adresse',
                'constraints' => [
                    new NotBlank(['message' => 'L\'adresse ne peut pas être vide']),
                    new Length(['max' => 255]),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('role', ChoiceType::class, [
                'label' => 'Rôle',
                'choices' => [
                    'Joueur' => 'PLAYER',
                    'Nutritionniste' => 'NUTRITIONIST'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le rôle doit être sélectionné']),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('experience', ChoiceType::class, [
                'label' => 'Expérience',
                'required' => false,
                'attr' => ['class' => 'form-control role-dependent role-nutritionist'],
            ])
            ->add('salaire', NumberType::class, [
                'label' => 'Salaire',
                'required' => false,
                'attr' => ['class' => 'form-control role-dependent role-nutritionist'],
            ])
            ->add('niveau_joueur', ChoiceType::class, [
                'label' => 'Niveau du joueur',
                'required' => false,
                'attr' => ['class' => 'form-control role-dependent role-player'],
            ])
            ->add('max_distance_user', NumberType::class, [
                'label' => 'Distance maximale (km)',
                'required' => false,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^\d+$/',
                        'message' => 'La distance doit être un nombre entier'
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0
                ]
            ])
            ->add('is_premium', ChoiceType::class, [
                'label' => 'Premium',
                'required' => false,
                'choices' => [
                    'Oui' => true,
                    'Non' => false
                ],
                'attr' => ['class' => 'form-control role-dependent role-player'],
            ])
            ->add('photo_user', FileType::class, [
                'label' => 'Photo de profil',
                'required' => false,
                'mapped' => true,
                'data_class' => null,
                'constraints' => $isEdit ? [] : [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['image/jpeg', 'image/png','image/jpg'],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG ou PNG)',
                    ])
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('piece_jointe', FileType::class, [
                'label' => 'Pièce jointe',
                'required' => false,
                'mapped' => true,
                'data_class' => null,
                'attr' => ['class' => 'form-control']
            ])
            ->add('date_naissance_user', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(['message' => 'La date de naissance ne peut pas être vide']),
                ],
                'attr' => ['class' => 'form-control']
            ]);

        // Add transformers for file fields

        $builder->get('piece_jointe')
            ->addModelTransformer(new CallbackTransformer(
                function ($filePath) use ($options) {
                    if (empty($filePath)) {
                        return null;
                    }
                    $fullPath = $options['uploads_directory'].'/'.$filePath;
                    return file_exists($fullPath) ? $fullPath : null;
                },
                function ($uploadedFile) {
                    return $uploadedFile;
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false,
            'uploads_directory' => null,
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();
                $groups = ['Default'];
                
                if ($data instanceof User && !$data->getIdUser()) {
                    $groups[] = 'registration';
                }
                
                return $groups;
            }
        ]);
        
        $resolver->setRequired('uploads_directory');
    }
}