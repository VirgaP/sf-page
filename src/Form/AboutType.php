<?php
/**
 * Created by PhpStorm.
 * User: tadas
 * Date: 2018-05-18
 * Time: 11:07
 */

namespace App\Form;


use App\Entity\About;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AboutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('phoneNumber', TextType::class, [
                'label' => 'Telefono nr.',
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresas',
            ])
            ->add('text', TextareaType::class, [
                'label' => 'Tekstas',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => About::class,
        ]);
    }

}