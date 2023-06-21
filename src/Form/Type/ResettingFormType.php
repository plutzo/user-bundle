<?php

declare(strict_types=1);


namespace Marlinc\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ResettingFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'options' => [
                'translation_domain' => 'MarlincUserBundle',
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
            ],
            'constraints' => [
                new NotBlank()
            ],
            'first_options' => ['label' => 'form.new_password'],
            'second_options' => ['label' => 'form.new_password_confirmation'],
            'invalid_message' => 'password.mismatch',
        ]);
    }
}
