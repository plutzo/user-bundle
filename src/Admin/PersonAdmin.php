<?php

namespace Marlinc\UserBundle\Admin;

use Marlinc\AdminBundle\Admin\AbstractAdmin;
use Marlinc\EntityBundle\Admin\Filter\HasReferenceFilter;
use Marlinc\EntityBundle\Form\Type\EntityReferenceSelectType;
use Marlinc\PostalCodeBundle\Form\Type\PostalCodeSelectType;
use Marlinc\UserBundle\Doctrine\GenderEnumType;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PersonAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('gender')
            ->add('firstname')
            ->add('lastname')
            ->add('email')
            ->add('referencingEntities', null, [], EntityReferenceSelectType::class, [
                'allow_edit' => true,
                'width' => '100%'
            ])
            ->add('has_reference', HasReferenceFilter::class)
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('gender')
            ->add('firstname')
            ->add('lastname')
            ->add('referencingEntities')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                ]
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Name', ['class' => 'col-md-6'])
                ->add('formal', ChoiceType::class, [
                    'choices' => [
                        'Yes' => true,
                        'No' => false
                    ],
                    'expanded' => true
                ])
                ->add('gender', ChoiceType::class, [
                    'choices' => GenderEnumType::getChoices()
                ])
                ->add('firstname')
                ->add('lastname')
            ->end()
            ->with('Address', ['class' => 'col-md-6'])
                ->add('thoroughfare', null, [
                    'required' => false
                ])
                ->add('postalCode', PostalCodeSelectType::class, [
                    'width' => '100%',
                    'placeholder' => 'Please select a postal code.',
                    'help' => 'The information about locality and country is encoded with the postal code.'
                ])
            ->end()
            ->with('Contact', ['class' => 'col-md-6'])
                ->add('crmChannel', null, [
                    'help' => 'Select the preferred way to contact this person.'
                ])
                ->add('email')
                ->add('phone', PhoneNumberType::class, [
                    'default_region' => 'DE',
                    'widget' => PhoneNumberType::WIDGET_COUNTRY_CHOICE,
                    'country_choices' => ['DE', 'AT', 'CH'],
                    'required' => false
                ])
                ->add('mobile', PhoneNumberType::class, [
                    'default_region' => 'DE',
                    'widget' => PhoneNumberType::WIDGET_COUNTRY_CHOICE,
                    'country_choices' => ['DE', 'AT', 'CH'],
                    'required' => false
                ])
            ->end()
            ->with('Newsletter', ['class' => 'col-md-6'])
                ->add('newsletter', null, [
                    'required' => false,
                    'help' => 'If set, this person will receive newsletters via email. For legal reasons in general this box shouldn\'t be checked manually.'
                ])
                ->add('newsletterToken', null, [
                    'disabled' => true,
                    'help' => 'If this field is not empty, the newsletter double-opt-in process hasn\'t been confirmed by the person.'
                ])
            ->end()
            ->with('Additional Information', ['class' => 'col-md-6'])
                ->add('birthday', null, [
                    'widget' => 'single_text',
                    'html5' => true
                ])
            ->end();
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('gender')
            ->add('firstname')
            ->add('lastname')
            ->add('referencingEntities');
    }
}
