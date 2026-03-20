<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType; 
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NoSuspiciousCharacters;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\PasswordStrength;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'mapped' => false,
                'required' => true,
                "constraints" => [
                new NotBlank(
                    message : 'Please enter an email',
                ),
                new Length(
                    min: 5,
                    minMessage: 'Your email should be at least {{ limit }} characters',
                    max: 180,
                    maxMessage: 'Your email should be at most {{ limit }} characters',
                ),
                new Email(
                    message: 'The email {{ value }} is not a valid email.',
                ),
                new NoSuspiciousCharacters(),
            ]])
            ->add('prenom', TextType::class, [
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank(
                        message: 'Please enter your first name',
                    ),
                    new Length(
                        min: 2,
                        minMessage: 'Your first name should be at least {{ limit }} characters',
                        max: 80,
                        maxMessage: 'Your first name should be at most {{ limit }} characters',
                    ),
                    new NoSuspiciousCharacters(),
                ],
            ])
            ->add('nom', TextType::class, [
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank(
                        message: 'Please enter your last name',
                    ),
                    new Length(
                        min: 2,
                        minMessage: 'Your last name should be at least {{ limit }} characters',
                        max: 80,
                        maxMessage: 'Your last name should be at most {{ limit }} characters',
                    ),
                    new NoSuspiciousCharacters(),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue(
                        message: 'You should agree to our terms.',
                    ),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(
                        message: 'Please enter a password',
                    ),
                    new Length(
                        min: 6,
                        minMessage: 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        max: 4096,
                    ),
                    new PasswordStrength(),
                    new NotCompromisedPassword(
                        message: 'This password has been leaked in a data breach, it must not be used. Please use another password more strong.',
                    ),
                ],
            ])
            ->add('confirmPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(
                        message: 'Please confirm your password',
                    ),
                    new Length(
                        min: 6,
                        minMessage: 'Your password confirmation should be at least {{ limit }} characters',
                        max: 4096,
                    ),
                    new PasswordStrength(),
                    new NotCompromisedPassword(
                        message: 'This password has been leaked in a data breach, it must not be used. Please use another password more strong.',
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