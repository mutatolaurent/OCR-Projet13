<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', TextType::class, [
                'attr' => ['autocomplete' => 'family-name']
            ])
            ->add('firstName', TextType::class, [
                'attr' => ['autocomplete' => 'given-name']
            ])
            ->add('email', TextType::class, [
                'attr' => ['autocomplete' => 'email']
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,  // Utilisation de RepeatedType pour la confirmation du mot de passe
                'mapped' => false,  // Le champ n'est pas directement mappé à l'entité User, car le mot de passe sera haché avant d'être stocké
                'invalid_message' => 'Le mot de passe ne correspond pas.',
                'required' => true,
                'attr' => ['autocomplete' => 'new-password'],   //
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation mot de passe'],
                'constraints' => [
                    new NotBlank(
                        message: 'Please enter a password',
                    ),
                    new Length(
                        min: 6,
                        minMessage: 'Votre mot de passe doit faire au moins {{ limit }} caractères.',
                        max: 4096,
                    ),
                    new Regex([
                        'pattern' => '/[A-Z]/',
                        'message' => 'Votre mot de passe doit contenir au moins une lettre majuscule.',
                    ]),
                    new Regex([
                        'pattern' => '/[0-9]/',
                        'message' => 'Votre mot de passe doit contenir au moins un chiffre.',
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,  // Le champ n'est pas directement mappé à l'entité User, car il ne correspond pas à une propriété de l'entité
                'constraints' => [
                    new IsTrue(
                        message: 'Vous devez accepter les CGU de Green Goodies.',
                    ),
                ],
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
