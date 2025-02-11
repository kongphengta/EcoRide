<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class PasswordUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            -> add('actualPassword', PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'attr' => [
                    'placeholder' => 'Entrez votre mot de passe actuel',
                ],
                'mapped' => false,
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                  new Length([
                        'min' => 6,
                        'max' => 25
                    ]),
                ],
                'first_options'  => [
                    'label' => 'Votre nouveau mot de Passe',
                    'hash_property_path' => 'password',
                    'attr' => [
                    'placeholder' => 'Choissisez votre nouveau mot de passe',
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmez votre nouveau mot de passe',
                    'attr' => [
                    'placeholder' => 'Confirmez votre nouveau mot de passe',
                    ],
             
                ],                                                                                                  

                'mapped' => false,
            ])
            ->add ('submit', SubmitType::class, [
                'label' => 'Mettre à jour mon mot de passe',
                'attr' => [                                                                                                                                 
                    'class' => 'btn btn-warning',                                                                                                                                                                                                                                                                                                                                                                                                  
                ],
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $user = $form->getConfig()->getOptions()['data'];
                $passwordHacher = $form->getConfig()->getOptions()['passwordHacher'];

                // récupérer le mot de passe saisi par l'utilisateur et le comparer avec le mot de passe en base de données(l'entité User)
                $isValid = $passwordHacher->isPasswordValid($user, $form->get('actualPassword')->getData());
              
                // si le mot de passe saisi dans le champ actualPassword est incorrect envoyer une erreur
                if (!$isValid) {
                    $form->get('actualPassword')->addError(new FormError('Le mot de passe saisi est incorrect'));
                }
            })
  
        ;                                                                                                                                                                                                                                                                                                                                                                                                                                                           
  
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'passwordHacher' => null,
        ]);
    }
}
