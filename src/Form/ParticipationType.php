<?php

namespace App\Form;

use App\Entity\Participation;
use App\Entity\User;
use App\Entity\Event;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id_user', // ou un autre champ comme 'username' si disponible
                'label' => 'Utilisateur',
                'required' => true,
            ])
            ->add('idevent', EntityType::class, [
                'class' => Event::class,
                'choice_label' => 'titre', // Afficher le titre de l'événement
                'label' => 'Événement',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participation::class,
        ]);
    }
}