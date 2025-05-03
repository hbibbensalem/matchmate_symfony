<?php
namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomProduit', TextType::class, [
                'attr' => ['pattern' => '^[a-zA-Z][a-zA-Z0-9 ]*$'] // HTML5 seulement
            ])
            ->add('descriptionProduit', TextareaType::class, [
                'attr' => ['class' => 'form-control', 'rows' => 5]
            ])
            ->add('prixProduit', IntegerType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('quantiteProduit', IntegerType::class, [
                'attr' => ['class' => 'form-control']
            ])
          
            ->add('imageProduit', FileType::class, [
                'label' => 'Image du produit',
                'mapped' => false,
                'required' => false, // Rend le champ obligatoire dans le formulaire
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\NotBlank([
                        'message' => 'Veuillez sélectionner une image.'
                    ]),
                    new \Symfony\Component\Validator\Constraints\Image([
                        'maxSize' => '1M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                        'mimeTypesMessage' => 'Formats acceptés : JPEG, PNG, GIF (max 1Mo).'
                    ])
                ],
                'attr' => ['accept' => 'image/jpeg,image/png,image/gif,image/jpg']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
            'validation_groups' => function () {
                return ['Default'];
            },
        ]);
    }
}