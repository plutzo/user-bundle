<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Marlinc\UserBundle\Event;

/**
 * Contains all events thrown in the FOSUserBundle.
 */
final class UserEvents
{
    /**
     * The CHANGE_PASSWORD_INITIALIZE event occurs when the change password process is initialized.
     *
     * This event allows you to modify the default values of the user before binding the form.
     *
     * @Event("Marlinc\UserBundle\Event\GetResponseUserEvent")
     */
    const CHANGE_PASSWORD_INITIALIZE = 'marlinc.user.change_password.edit.initialize';

    /**
     * The CHANGE_PASSWORD_SUCCESS event occurs when the change password form is submitted successfully.
     *
     * This event allows you to set the response instead of using the default one.
     *
     * @Event("Marlinc\UserBundle\Event\FormEvent")
     */
    const CHANGE_PASSWORD_SUCCESS = 'marlinc.user.change_password.edit.success';

    /**
     * The CHANGE_PASSWORD_COMPLETED event occurs after saving the user in the change password process.
     *
     * This event allows you to access the response which will be sent.
     *
     * @Event("Marlinc\UserBundle\Event\FilterUserResponseEvent")
     */
    const CHANGE_PASSWORD_COMPLETED = 'marlinc.user.change_password.edit.completed';

    /**
     * The PROFILE_EDIT_INITIALIZE event occurs when the profile editing process is initialized.
     *
     * This event allows you to modify the default values of the user before binding the form.
     *
     * @Event("Marlinc\UserBundle\Event\GetResponseUserEvent")
     */
    const PROFILE_EDIT_INITIALIZE = 'marlinc.user.profile.edit.initialize';

    /**
     * The PROFILE_EDIT_SUCCESS event occurs when the profile edit form is submitted successfully.
     *
     * This event allows you to set the response instead of using the default one.
     *
     * @Event("Marlinc\UserBundle\Event\FormEvent")
     */
    const PROFILE_EDIT_SUCCESS = 'marlinc.user.profile.edit.success';

    /**
     * The PROFILE_EDIT_COMPLETED event occurs after saving the user in the profile edit process.
     *
     * This event allows you to access the response which will be sent.
     *
     * @Event("Marlinc\UserBundle\Event\FilterUserResponseEvent")
     */
    const PROFILE_EDIT_COMPLETED = 'marlinc.user.profile.edit.completed';

    /**
     * The REGISTRATION_INITIALIZE event occurs when the registration process is initialized.
     *
     * This event allows you to modify the default values of the user before binding the form.
     *
     * @Event("Marlinc\UserBundle\Event\UserEvent")
     */
    const REGISTRATION_INITIALIZE = 'marlinc.user.registration.initialize';

    /**
     * The REGISTRATION_SUCCESS event occurs when the registration form is submitted successfully.
     *
     * This event allows you to set the response instead of using the default one.
     *
     * @Event("Marlinc\UserBundle\Event\FormEvent")
     */
    const REGISTRATION_SUCCESS = 'marlinc.user.registration.success';

    /**
     * The REGISTRATION_FAILURE event occurs when the registration form is not valid.
     *
     * This event allows you to set the response instead of using the default one.
     * The event listener method receives a Marlinc\UserBundle\Event\FormEvent instance.
     *
     * @Event("Marlinc\UserBundle\Event\FormEvent")
     */
    const REGISTRATION_FAILURE = 'marlinc.user.registration.failure';

    /**
     * The REGISTRATION_COMPLETED event occurs after saving the user in the registration process.
     *
     * This event allows you to access the response which will be sent.
     *
     * @Event("Marlinc\UserBundle\Event\FilterUserResponseEvent")
     */
    const REGISTRATION_COMPLETED = 'marlinc.user.registration.completed';

    /**
     * The REGISTRATION_CONFIRM event occurs just before confirming the account.
     *
     * This event allows you to access the user which will be confirmed.
     *
     * @Event("Marlinc\UserBundle\Event\GetResponseUserEvent")
     */
    const REGISTRATION_CONFIRM = 'marlinc.user.registration.confirm';

    /**
     * The REGISTRATION_CONFIRMED event occurs after confirming the account.
     *
     * This event allows you to access the response which will be sent.
     *
     * @Event("Marlinc\UserBundle\Event\FilterUserResponseEvent")
     */
    const REGISTRATION_CONFIRMED = 'marlinc.user.registration.confirmed';

    /**
     * The RESETTING_RESET_REQUEST event occurs when a user requests a password reset of the account.
     *
     * This event allows you to check if a user is locked out before requesting a password.
     * The event listener method receives a Marlinc\UserBundle\Event\GetResponseUserEvent instance.
     *
     * @Event("Marlinc\UserBundle\Event\GetResponseUserEvent")
     */
    const RESETTING_RESET_REQUEST = 'marlinc.user.resetting.reset.request';

    /**
     * The RESETTING_RESET_INITIALIZE event occurs when the resetting process is initialized.
     *
     * This event allows you to set the response to bypass the processing.
     *
     * @Event("Marlinc\UserBundle\Event\GetResponseUserEvent")
     */
    const RESETTING_RESET_INITIALIZE = 'marlinc.user.resetting.reset.initialize';

    /**
     * The RESETTING_RESET_SUCCESS event occurs when the resetting form is submitted successfully.
     *
     * This event allows you to set the response instead of using the default one.
     *
     * @Event("Marlinc\UserBundle\Event\FormEvent ")
     */
    const RESETTING_RESET_SUCCESS = 'marlinc.user.resetting.reset.success';

    /**
     * The RESETTING_RESET_COMPLETED event occurs after saving the user in the resetting process.
     *
     * This event allows you to access the response which will be sent.
     *
     * @Event("Marlinc\UserBundle\Event\FilterUserResponseEvent")
     */
    const RESETTING_RESET_COMPLETED = 'marlinc.user.resetting.reset.completed';

    /**
     * The SECURITY_IMPLICIT_LOGIN event occurs when the user is logged in programmatically.
     *
     * This event allows you to access the response which will be sent.
     *
     * @Event("Marlinc\UserBundle\Event\UserEvent")
     */
    const SECURITY_IMPLICIT_LOGIN = 'marlinc.user.security.implicit_login';

    /**
     * The RESETTING_SEND_EMAIL_INITIALIZE event occurs when the send email process is initialized.
     *
     * This event allows you to set the response to bypass the email confirmation processing.
     * The event listener method receives a Marlinc\UserBundle\Event\GetResponseNullableUserEvent instance.
     *
     * @Event("Marlinc\UserBundle\Event\GetResponseNullableUserEvent")
     */
    const RESETTING_SEND_EMAIL_INITIALIZE = 'marlinc.user.resetting.send_email.initialize';

    /**
     * The RESETTING_SEND_EMAIL_CONFIRM event occurs when all prerequisites to send email are
     * confirmed and before the mail is sent.
     *
     * This event allows you to set the response to bypass the email sending.
     * The event listener method receives a Marlinc\UserBundle\Event\GetResponseUserEvent instance.
     *
     * @Event("Marlinc\UserBundle\Event\GetResponseUserEvent")
     */
    const RESETTING_SEND_EMAIL_CONFIRM = 'marlinc.user.resetting.send_email.confirm';

    /**
     * The RESETTING_SEND_EMAIL_COMPLETED event occurs after the email is sent.
     *
     * This event allows you to set the response to bypass the the redirection after the email is sent.
     * The event listener method receives a Marlinc\UserBundle\Event\GetResponseUserEvent instance.
     *
     * @Event("Marlinc\UserBundle\Event\GetResponseUserEvent")
     */
    const RESETTING_SEND_EMAIL_COMPLETED = 'marlinc.user.resetting.send_email.completed';

    /**
     * The USER_CREATED event occurs when the user is created with UserManipulator.
     *
     * This event allows you to access the created user and to add some behaviour after the creation.
     *
     * @Event("Marlinc\UserBundle\Event\UserEvent")
     */
    const USER_CREATED = 'marlinc.user.user.created';

    /**
     * The USER_PASSWORD_CHANGED event occurs when the user is created with UserManipulator.
     *
     * This event allows you to access the created user and to add some behaviour after the password change.
     *
     * @Event("Marlinc\UserBundle\Event\UserEvent")
     */
    const USER_PASSWORD_CHANGED = 'marlinc.user.user.password_changed';

    /**
     * The USER_ACTIVATED event occurs when the user is created with UserManipulator.
     *
     * This event allows you to access the activated user and to add some behaviour after the activation.
     *
     * @Event("Marlinc\UserBundle\Event\UserEvent")
     */
    const USER_ACTIVATED = 'marlinc.user.user.activated';

    /**
     * The USER_DEACTIVATED event occurs when the user is created with UserManipulator.
     *
     * This event allows you to access the deactivated user and to add some behaviour after the deactivation.
     *
     * @Event("Marlinc\UserBundle\Event\UserEvent")
     */
    const USER_DEACTIVATED = 'marlinc.user.user.deactivated';

    /**
     * The USER_PROMOTED event occurs when the user is created with UserManipulator.
     *
     * This event allows you to access the promoted user and to add some behaviour after the promotion.
     *
     * @Event("Marlinc\UserBundle\Event\UserEvent")
     */
    const USER_PROMOTED = 'marlinc.user.user.promoted';

    /**
     * The USER_DEMOTED event occurs when the user is created with UserManipulator.
     *
     * This event allows you to access the demoted user and to add some behaviour after the demotion.
     *
     * @Event("Marlinc\UserBundle\Event\UserEvent")
     */
    const USER_DEMOTED = 'marlinc.user.user.demoted';
}
