<?php
/**
 * Created by PhpStorm.
 * User: virga
 * Date: 2018-05-25
 * Time: 18:32
 */

namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Vardas'
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Pavardė'
            ])
            ->add('email', EmailType::class, [
                'label' => 'El. paštas'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}