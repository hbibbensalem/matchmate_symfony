<?php

namespace App\Form;
use App\Entity\User;
use App\Entity\Suivi;
use App\Entity\Regime;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class SuiviType extends AbstractType
{
    private UserRepository $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add('id_user', EntityType::class, [
                'class' => User::class,
                'choices' => $this->userRepo->findPremiumPlayers(),
                'choice_label' => function (User $user) {
                    return $user->getNomUser().' '.$user->getPrenomUser();
                },
                'label' => 'Joueur Premium',
                'placeholder' => 'Sélectionner un joueur premium',
            ])
            ->add('poids', NumberType::class)
            ->add('tour_de_taille', NumberType::class)
            ->add('imc', NumberType::class)
            ->add('date_suivi', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('taille', NumberType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Suivi::class,
        ]);
    }
}