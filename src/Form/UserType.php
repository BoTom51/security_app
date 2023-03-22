<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // MODIF : Il y a un probleme de conversion de "roles" en string quand il contient qu'une string. 
        // L'attribut "multiple : true" regle le probleme.
        $builder
            ->add('email', TextType::class)
            ->add('roles', ChoiceType::class, [
                'multiple' => true
            ])
            // RepeatedType = pour la confirmation de mot de passe, le champs MDP est doublé et a un trainement particulier.
            // Validation de correspondant et message d'erreur.
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les Mots de passe doivent être identiques !'
            ])
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // 'crsf_protection' => methode pour corriger l'erreur : 
        // CSRF token error, isValid() return false au submit du form
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => false,
        ]);
    }
}
