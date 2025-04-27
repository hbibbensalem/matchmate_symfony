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
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $isEdit = $options['is_edit'] ?? false;
        
        $builder
            ->add('email_user', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un email.']),
                    new Length(['max' => 180, 'maxMessage' => 'L\'email ne peut pas dépasser {{ limit }} caractères.']),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('password_user', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => !$isEdit,
                'mapped' => false,
                'constraints' => !$isEdit ? [
                    new NotBlank(['message' => 'Veuillez entrer un mot de passe.']),
                    new Length(['min' => 6, 'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.']),
                    new Regex([
                        'pattern' => '/\d/',
                        'message' => 'Le mot de passe doit contenir au moins un chiffre.'
                    ]),
                ] : [],
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'new-password'
                ]
            ])
            ->add('nom_user', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un nom.']),
                    new Length(['max' => 50, 'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères.']),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('prenom_user', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un prénom.']),
                    new Length(['max' => 50, 'maxMessage' => 'Le prénom ne peut pas dépasser {{ limit }} caractères.']),
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
                    new NotBlank(['message' => 'Veuillez entrer un numéro de téléphone.']),
                    new Regex([
                        'pattern' => '/^[259][0-9]*$/',
                        'message' => 'Le numéro doit commencer par 2, 5 ou 9.'
                    ]),
                    new Length(['max' => 15, 'maxMessage' => 'Le numéro de téléphone ne peut pas dépasser {{ limit }} caractères.']),
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
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'address-input',
                    'readonly' => true, // Empêche la modification manuelle
                ]
            ])
            ->add('role', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'Joueur' => 'PLAYER',
                    'Nutritionniste' => 'NUTRITIONIST'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner un rôle']),
                ],
                'attr' => [
                    'class' => 'role-hidden-input',
                    'data-card-selector' => 'role-card'
                ],
                'placeholder' => false,
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('experience', ChoiceType::class, [
                'label' => 'Expérience',
                'required' => false,
                'choices' => [
                    'One Year' => 'One Year',
                    'Two Years' => 'Two Years',
                    'Three Years' => 'Three Years',
                    'Four Years' => 'Four Years',
                ],
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
                'choices' => [
                    'Débutant' => 'DEBUTANT',
                    'Intermédiaire' => 'INTERMEDIAIRE',
                    'Expert' => 'EXPERT'
                ],
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
            ])
            ->add('recaptcha', EWZRecaptchaType::class, [
                'label' => false,
                'mapped' => false,
                'constraints' => [
                    new RecaptchaTrue(),
                ],
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