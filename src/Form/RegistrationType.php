<?php
/**
 * Created by PhpStorm.
 * User: virga
 * Date: 2018-05-21
 * Time: 11:35
 */

namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\IsTrue;

class RegistrationType extends AbstractType
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
            ])
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'  => array('label' => 'Slaptažodis'),
                'second_options' => array('label' => 'Pakartoti slaptaždoį'),
            ))
            ->add('termsAccepted', CheckboxType::class, array(
                'label' => 'Sutinku su sąlygomis',
                'mapped' => false,
                'constraints' => new IsTrue(),
            ))
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}