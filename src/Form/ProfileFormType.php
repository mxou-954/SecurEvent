<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NoSuspiciousCharacters;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                "constraints" => [
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
                'constraints' => [
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
                'constraints' => [
                    new Length(
                        min: 2,
                        minMessage: 'Your last name should be at least {{ limit }} characters',
                        max: 80,
                        maxMessage: 'Your last name should be at most {{ limit }} characters',
                    ),
                    new NoSuspiciousCharacters(),
                ],
            ])
            ->add('currentPassword', PasswordType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Length(min: 8, max: 255),
                ],
            ])
            ->add('newPassword', PasswordType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Length(min: 8, max: 255),
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