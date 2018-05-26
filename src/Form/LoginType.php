<?php
/**
 * Created by PhpStorm.
 * User: virga
 * Date: 2018-05-21
 * Time: 16:11
 */

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

    final class LoginType extends AbstractType
    {
    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtils;
    public function __construct(AuthenticationUtils $authenticationUtils)
    {
        $this->authenticationUtils = $authenticationUtils;
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('_username', TextType::class, [
                'label'=>'El.paštas',
                'required'=> true,
            ])
            ->add('_password', PasswordType::class, [
                'label' => 'Slaptažodis',
                'required'=> true,
            ])
            ->add('_target_path', HiddenType::class)
        ;
        $authUtils = $this->authenticationUtils;
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($authUtils) {
            // get the login error if there is one
            $error = $authUtils->getLastAuthenticationError();
            if ($error) {
                $event->getForm()->addError(new FormError($error->getMessage()));
            }
            $event->setData(array_replace((array) $event->getData(), array(
                '_username' => $authUtils->getLastUsername(),
            )));
        });
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        /* Note: the form's csrf_token_id must correspond to that for the form login
         * listener in order for the CSRF token to validate successfully.
         */
        $resolver->setDefaults(array(
            'csrf_token_id' => 'authenticate',
        ));
    }
    public function getBlockPrefix()
    {
        return '';
    }

}