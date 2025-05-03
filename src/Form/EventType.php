<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Validator\Constraints as Assert;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre de l\'événement',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Ex: Tournoi de tennis interclubs',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer un titre pour l\'événement'
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'Le titre ne peut pas dépasser {{ limit }} caractères',
                        'min' => 5,
                        'minMessage' => 'Le titre doit faire au moins {{ limit }} caractères'
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[^<>]*$/',
                        'message' => 'Le titre ne doit pas contenir de balises HTML.'
                    ])
                ]
            ])
            ->add('date', DateTimeType::class, [
                'label' => 'Date et heure',
                'widget' => 'single_text',
                'html5' => true,
                'required' => true,
                'attr' => [
                    'class' => 'form-control datetimepicker',
                    'min' => (new \DateTime())->format('Y-m-d\TH:i')
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez sélectionner une date et heure'
                    ]),
                    new Assert\GreaterThan([
                        'value' => 'today',
                        'message' => 'La date doit être dans le futur'
                    ])
                ]
            ])
            ->add('lieu', TextType::class, [
                'label' => 'Lieu',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Ex: Complexe sportif de Tunis',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez indiquer un lieu'
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'Le lieu ne peut pas dépasser {{ limit }} caractères'
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[^<>]*$/',
                        'message' => 'Le lieu ne doit pas contenir de balises HTML.'
                    ])
                ]
            ])
            ->add('image_url', UrlType::class, [
                'label' => 'URL de l\'image',
                'required' => true,
                'attr' => [
                    'placeholder' => 'https://exemple.com/image.jpg',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez fournir une URL d\'image'
                    ]),
                    new Assert\Url([
                        'message' => 'Veuillez entrer une URL valide'
                    ]),
                    new Assert\Regex([
                        'pattern' => '/\.(jpeg|jpg|gif|png)$/i',
                        'message' => 'L\'URL doit pointer vers une image (JPEG, JPG, GIF, PNG)'
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => true,
                'attr' => [
                    'rows' => 5,
                    'class' => 'form-control',
                    'placeholder' => 'Décrivez votre événement en détail...'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer une description'
                    ]),
                    new Assert\Length([
                        'min' => 20,
                        'minMessage' => 'La description doit faire au moins {{ limit }} caractères',
                        'max' => 2000,
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères'
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[^<>]*$/',
                        'message' => 'La description ne doit pas contenir de balises HTML.'
                    ])
                ]
            ])
            ->add('capacite', IntegerType::class, [
                'label' => 'Capacité maximale',
                'required' => true,
                'attr' => [
                    'min' => 1,
                    'max' => 1000,
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez indiquer une capacité'
                    ]),
                    new Assert\Positive([
                        'message' => 'La capacité doit être un nombre positif'
                    ]),
                    new Assert\LessThanOrEqual([
                        'value' => 1000,
                        'message' => 'La capacité ne peut pas dépasser {{ compared_value }} participants'
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'attr' => [
                'novalidate' => 'novalidate',
                'class' => 'needs-validation'
            ]
        ]);
    }
}