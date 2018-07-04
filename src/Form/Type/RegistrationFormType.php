<?php

namespace Marlinc\UserBundle\Form\Type;

use Marlinc\UserBundle\Form\PersonFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
    /**
     * @var string
     */
    private $class;

    /**
     * @param string $class The User class name
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'form.email',
                'translation_domain' => 'MarlincUserBundle'
            ])
            ->add('username', null, [
                'label' => 'form.username',
                'translation_domain' => 'MarlincUserBundle'
            ])
            ->add('person', PersonFormType::class, [
                'enabled_fields' => [],
                'required_fields' => []
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'translation_domain' => 'MarlincUserBundle',
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                ],
                'first_options' => ['label' => 'form.password'],
                'second_options' => ['label' => 'form.password_confirmation'],
                'invalid_message' => 'marlinc.user.password.mismatch',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        // TODO Add options for embedded person form type.
        $resolver->setDefaults([
            'data_class' => $this->class,
            'csrf_token_id' => 'registration',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'marlinc_user_registration';
    }
}
