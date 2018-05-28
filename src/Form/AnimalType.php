<?php

namespace App\Form;

use App\Entity\Animal;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnimalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('species', ChoiceType::class, [
                'label' => 'Gyvūnas',
                'choices' => [
                    'Šuo' => 'Šuo',
                    'Katė' => 'Katė',
                    'Kita' => 'Kita',
                ]
            ])
            ->add('name', TextType::class, [
                'label' => 'Gyvūno vardas',
            ])
            ->add('age', IntegerType::class, [
                'label' => 'Amžius',
                'attr' => [
                    'step' => 0.1,
                    'min' => 0.1,
                    'max' => 100,
                ]
            ])
            ->add('picture', FileType::class, [
                'label' => 'Pridėti nuotrauką (galimi tik JPG ir PNG formatai)',
                'data_class' => null,
                'attr' => [
                    'class' => 'filestyle',
                    'data-text' => 'Pasirinkti failą',
                    'data-btnClass' => 'btn-primary',
                    'data-placeholder' => 'Failas nepasirinktas',
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Aprašymas',
                'attr' => [
                    'rows' => 5,
                ],
            ])
            ->add('isAvailable', CheckboxType::class, [
                'label' => 'Ar gyvūnas atiduotas?',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Animal::class,
        ]);
    }
}
