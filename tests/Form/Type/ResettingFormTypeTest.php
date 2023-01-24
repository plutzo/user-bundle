<?php

declare(strict_types=1);


namespace Marlinc\UserBundle\Tests\Form\Type;

use Marlinc\UserBundle\Form\Type\ResettingFormType;
use Marlinc\UserBundle\Tests\App\Entity\User;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Form\FormTypeExtensionInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ResettingFormTypeTest extends TypeTestCase
{
    public function testSubmit(): void
    {
        $user = new User();

        $form = $this->factory->create(ResettingFormType::class, $user);
        $formData = [
            'plainPassword' => [
                'first' => 'test',
                'second' => 'test',
            ],
        ];
        $form->submit($formData);

        static::assertTrue($form->isSynchronized());
        static::assertSame($user, $form->getData());
        static::assertSame('test', $user->getPlainPassword());
    }

    /**
     * @return FormTypeInterface[]
     */
    protected function getTypes(): array
    {
        return [
            new ResettingFormType(User::class),
        ];
    }

    /**
     * @return FormTypeExtensionInterface[]
     */
    protected function getTypeExtensions(): array
    {
        $validator = $this->createStub(ValidatorInterface::class);
        $validator->method('validate')->willReturn(new ConstraintViolationList());

        return [
            new FormTypeValidatorExtension($validator),
        ];
    }
}
