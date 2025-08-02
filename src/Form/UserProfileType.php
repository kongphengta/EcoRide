<?php

namespace App\Form;

use App\Entity\Configuration;
use App\Entity\Role;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('password')
            ->add('firstname')
            ->add('lastname')
            ->add('telephone')
            ->add('resetToken')
            ->add('resetTokenCreatedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('adresse')
            ->add('dateNaissance')
            ->add('photo')
            ->add('sexe')
            ->add('pseudo')
            ->add('dateInscription')
            ->add('isVerified')
            ->add('verificationToken')
            ->add('isProfileComplete')
            ->add('credits')
            ->add('configuration', EntityType::class, [
                'class' => Configuration::class,
                'choice_label' => 'id',
            ])
            ->add('ecoRideRoles', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
