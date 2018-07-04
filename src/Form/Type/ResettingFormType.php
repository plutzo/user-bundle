<?php

namespace Marlinc\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResettingFormType extends AbstractType
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
        $builder->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'options' => [
                'translation_domain' => 'MarlincUserBundle',
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
            ],
            'first_options' => ['label' => 'form.new_password'],
            'second_options' => ['label' => 'form.new_password_confirmation'],
            'invalid_message' => 'marlinc.user.password.mismatch',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->class,
            'csrf_token_id' => 'resetting',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'marlinc_user_resetting';
    }
}
