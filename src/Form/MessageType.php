<?php
/**
 * Created by PhpStorm.
 * User: tadas
 * Date: 2018-05-17
 * Time: 10:00
 */

namespace App\Form;


use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Vardas'
            ])
            ->add('email', EmailType::class, [
                'label' => 'El. paštas'
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'Telefono numeris'
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Žinutė'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }

}