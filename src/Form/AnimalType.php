<?php

namespace App\Form;

use App\Entity\Animal;
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
            ->add('species', TextType::class, [
                'label' => 'Gyvūnas'
            ])
            ->add('age', IntegerType::class, [
                'label' => 'Amžius',
                'attr' => [
                    'min' => 1,
                    'max' => 100,
                ]
            ])
            ->add('picture', FileType::class, [
                'label' => 'Pridėti nuotrauką (galimi tik JPG ir PNG formatai)',
                'data_class' => null,
                'attr' => [
                    'class' => 'filestyle',
                    'data-input' => 'false',
                    'data-buttonText' => 'Pasirinkti failą',
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Aprašymas',
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
