<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class EventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'constraints' => [
                    new NotBlank(message: 'Le titre est obligatoire.'),
                ],
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new NotBlank(message: 'La description est obligatoire.'),
                ],
            ])
            ->add('dateDebut', DateTimeType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(message: 'La date est obligatoire.'),
                    new GreaterThan('today', message: 'La date doit être dans le futur.'),
                ],
            ])
            ->add('lieu', TextType::class, [
                'constraints' => [
                    new NotBlank(message: 'Le lieu est obligatoire.'),
                ],
            ])
            ->add('capaciteMax', IntegerType::class, [
                'constraints' => [
                    new NotBlank(message: 'La capacité est obligatoire.'),
                    new Positive(message: 'La capacité doit être un nombre positif.'),
                ],
            ])
            ->add('isPublished', CheckboxType::class, [
                'required' => false,
                'label' => 'Publier l\'événement',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}