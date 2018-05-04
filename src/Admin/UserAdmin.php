<?php

namespace Marlinc\UserBundle\Admin;

use Marlinc\EntityBundle\Admin\Filter\HasReferenceFilter;
use Marlinc\UserBundle\Doctrine\GenderEnumType;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\UserBundle\Admin\Model\UserAdmin as BaseAdmin;
use Sonata\UserBundle\Form\Type\SecurityRolesType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserAdmin extends BaseAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('email')
            ->add('groups')
            ->add('enabled', null, ['editable' => true])
            ->add('createdAt')
            ->add('allReferencingEntities', 'array')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ]
            ]);

        if ($this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
            $listMapper
                ->add('impersonating', 'string', ['template' => 'SonataUserBundle:Admin:Field/impersonating.html.twig']);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper): void
    {
        $filterMapper
            ->add('email')
            ->add('person.newsletter')
            ->add('groups')
            // TODO add AJAX filter ->add('referencingEntities')
            ->add('has_reference', HasReferenceFilter::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        // define group zoning
        $formMapper
            ->tab('User')
            ->with('User data', ['class' => 'col-md-6'])->end()
            ->with('Personal info', ['class' => 'col-md-6'])->end()
            ->end()
            ->tab('Security')
            ->with('Status', ['class' => 'col-md-4'])->end()
            ->with('Groups', ['class' => 'col-md-4'])->end()
            ->with('Keys', ['class' => 'col-md-4'])->end()
            ->with('Roles', ['class' => 'col-md-12'])->end()
            ->end();

        $formMapper
            ->tab('User')
            ->with('User data')
            ->add('email')
            ->add('plainPassword', RepeatedType::class, [
                'required' => (!$this->getSubject() || is_null($this->getSubject()->getId())),
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
            ])
            ->end()
            ->with('Personal info')
            // TODO: Embed PersonAdmin
            ->add('locale', LanguageType::class)
            ->add('person.gender', ChoiceType::class, [
                'choices' => GenderEnumType::getChoices(),
                'required' => true
            ])
            ->add('person.firstname')
            ->add('person.lastname')
            ->add('person.phone', PhoneNumberType::class, [
                'default_region' => 'DE',
                'widget' => PhoneNumberType::WIDGET_COUNTRY_CHOICE,
                'country_choices' => ['DE', 'AT', 'CH'],
                'required' => false
            ])
            ->add('dateOfBirth', DateType::class, [
                'years' => range(date('Y') - 90, date('Y') - 15),
                'required' => false
            ])
            ->end()
            ->end()
            ->tab('Security')
            ->with('Status')
            ->add('enabled', null, ['required' => false])
            ->end()
            ->with('Groups')
            ->add('groups', 'sonata_type_model', [
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ])
            ->end()
            ->with('Roles')
            ->add('roles', SecurityRolesType::class, [
                'label' => 'form.label_roles',
                'expanded' => true,
                'multiple' => true,
                'required' => false,
            ])
            ->end()
            ->with('Keys')
            ->add('twoStepVerificationCode', null, ['required' => false])
            ->end()
            ->end();
    }
}