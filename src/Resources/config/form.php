<?php

declare(strict_types=1);

use Marlinc\UserBundle\Form\Type\ResetPasswordRequestFormType;
use Marlinc\UserBundle\Form\Type\ResettingFormType;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()
        ->set('marlinc.user.form.type.resetting', ResettingFormType::class)
            ->tag('form.type', ['alias' => 'marlinc_user_resetting'])
        ->set('marlinc.user.form.type.reset_password_request', ResetPasswordRequestFormType::class)
            ->tag('form.type', ['alias' => 'marlinc_user_reset_password_request']);
};
