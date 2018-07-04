<?php

namespace Marlinc\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfileFormType extends AbstractType
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
        $constraintsOptions = [
            'message' => 'marlinc.user.current_password.invalid',
        ];

        if (!empty($options['validation_groups'])) {
            $constraintsOptions['groups'] = [reset($options['validation_groups'])];
        }

        $builder
            ->add('username', null, [
                'label' => 'form.username',
                'translation_domain' => 'MarlincUserBundle'
            ])
            ->add('email', EmailType::class, [
                'label' => 'form.email',
                'translation_domain' => 'MarlincUserBundle'
            ])
            ->add('current_password', PasswordType::class, [
                'label' => 'form.current_password',
                'translation_domain' => 'MarlincUserBundle',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(),
                    new UserPassword($constraintsOptions),
                ],
                'attr' => [
                    'autocomplete' => 'current-password',
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        // TODO: Add option for embedded person form type.
        $resolver->setDefaults([
            'data_class' => $this->class,
            'csrf_token_id' => 'profile',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'marlinc_user_profile';
    }
}
