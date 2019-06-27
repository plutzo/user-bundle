<?php
/**
 * Created by PhpStorm.
 * User: elias
 * Date: 01.09.17
 * Time: 16:57
 */

namespace Marlinc\UserBundle\Form;

use Marlinc\PostalCodeBundle\Form\Type\PostalCodeSelectType;
use Marlinc\UserBundle\Entity\Person;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonFormType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'Please choose' => '',
                    'Male' => 'm',
                    'Female' => 'f'
                ],
            ])
            ->add('firstname')
            ->add('lastname');

        // Optional fields from here

        if (in_array('thoroughfare', $options['enabled_fields'])) {
            $builder->add('thoroughfare', null, [
                'required' => in_array('thoroughfare', $options['required_fields'])
            ]);
        }

        if (in_array('postalCode', $options['enabled_fields'])) {
            if ($options['country'] === null) {
                $builder->add('postalCode', PostalCodeSelectType::class, [
                    'required' => in_array('postalCode', $options['required_fields']),
                    'attr' => [
                        'data-theme' => 'bootstrap'
                    ],
                    'width' => '100%'
                ]);
            } else {
                $builder->add('postalCode', PostalCodeSelectType::class, [
                    'required' => in_array('postalCode', $options['required_fields']),
                    'remote_route' => 'ajax_postalcode_country',
                    'remote_params' => ['country' => $options['country']],
                    'attr' => [
                        'data-theme' => 'bootstrap'
                    ],
                    'width' => '100%'
                ]);
            }
        }

        if (in_array('phone', $options['enabled_fields'])) {
            $builder->add('phone', PhoneNumberType::class, [
                'default_region' => 'DE',
                'widget' => PhoneNumberType::WIDGET_COUNTRY_CHOICE,
                'country_choices' => ['DE', 'AT', 'CH'],
                'required' => in_array('phone', $options['required_fields']),
            ]);
        }

        if (in_array('mobile', $options['enabled_fields'])) {
            $builder->add('mobile', PhoneNumberType::class, [
                'default_region' => 'DE',
                'widget' => PhoneNumberType::WIDGET_COUNTRY_CHOICE,
                'country_choices' => ['DE', 'AT', 'CH'],
                'required' => in_array('mobile', $options['required_fields']),
            ]);
        }

        if (in_array('email', $options['enabled_fields'])) {
            $builder->add('email', RepeatedType::class, [
                'first_name'  => 'input1',
                'second_name' => 'input2',
                'first_options' => ['label' => 'E-Mail'],
                'second_options' => ['label' => 'Repeat E-Mail'],
                'invalid_message' => 'Die eingegebenen Mail-Adressen in den beiden Feldern stimmen nicht überein.'
            ]);
        }

        if (in_array('newsletter', $options['enabled_fields'])) {
            $builder->add('newsletter', null, [
                'required' => in_array('newsletter', $options['required_fields']),
            ]);
        }

        if (in_array('birthday', $options['enabled_fields'])) {
            $builder->add('birthday', null, [
                'widget' => 'choice',
                'html5' => false,
                'format' => 'dd.MM.yyyy',
                'placeholder' => ['year' => 'Jahr', 'month' => 'Monat', 'day' => 'Tag'],
                'years' => [2030,2029,2028,2027,2026,2025,2024,2023,2022,2021,2020,2019,2018,2017,2016,2015,2014,2013,2012,2011,2010,2009,2008,2007,2006,2005,2004,2003,2002,2001,2000,1999,1998,1997,1996,1995,1994,1993,1992,1991,1990,1989,1988,1987,1986,1985,1984,1983,1982,1981,1980,1979,1978,1977,1976,1975,1974,1973,1972,1971,1970,1969,1968,1967,1966,1965,1964,1963,1962,1961,1960,1959,1958,1957,1956,1955,1954,1953,1952,1951,1950,1949,1948,1947,1946,1945,1944,1943,1942,1941,1940,1939,1938,1937,1936,1935,1934,1933,1932,1931,1930],
                'attr' => ['class' => 'personDateField'],
                'required' => in_array('birthday', $options['required_fields']),
            ]);
        }

        if (in_array('passportNr', $options['enabled_fields'])) {
            $builder->add('passportNr', null, [
                'required' => in_array('passportNr', $options['required_fields'])
                ]
            );
        }

        if (in_array('passportIssueDate', $options['enabled_fields'])) {
            $builder->add('passportIssueDate', null, [
                    'widget' => 'choice',
                    'html5' => true,
                    'format' => 'dd.MM.yyyy',
                    'placeholder' => ['year' => 'Jahr', 'month' => 'Monat', 'day' => 'Tag'],
                    'years' => [2030,2029,2028,2027,2026,2025,2024,2023,2022,2021,2020,2019,2018,2017,2016,2015,2014,2013,2012,2011,2010,2009,2008,2007,2006,2005,2004,2003,2002,2001,2000,1999,1998,1997,1996,1995,1994,1993,1992,1991,1990,1989,1988,1987,1986,1985,1984,1983,1982,1981,1980,1979,1978,1977,1976,1975,1974,1973,1972,1971,1970,1969,1968,1967,1966,1965,1964,1963,1962,1961,1960,1959,1958,1957,1956,1955,1954,1953,1952,1951,1950,1949,1948,1947,1946,1945,1944,1943,1942,1941,1940,1939,1938,1937,1936,1935,1934,1933,1932,1931,1930],
                    'attr' => ['class' => 'personDateField'],
                    'required' => in_array('passportIssueDate', $options['required_fields'])
                ]
            );
        }

        if (in_array('passportValidDate', $options['enabled_fields'])) {
            $builder->add('passportValidDate', null, [
                    'widget' => 'choice',
                    'html5' => true,
                    'format' => 'dd.MM.yyyy',
                    'placeholder' => ['year' => 'Jahr', 'month' => 'Monat', 'day' => 'Tag'],
                    'years' => [2030,2029,2028,2027,2026,2025,2024,2023,2022,2021,2020,2019,2018,2017,2016,2015,2014,2013,2012,2011,2010,2009,2008,2007,2006,2005,2004,2003,2002,2001,2000,1999,1998,1997,1996,1995,1994,1993,1992,1991,1990,1989,1988,1987,1986,1985,1984,1983,1982,1981,1980,1979,1978,1977,1976,1975,1974,1973,1972,1971,1970,1969,1968,1967,1966,1965,1964,1963,1962,1961,1960,1959,1958,1957,1956,1955,1954,1953,1952,1951,1950,1949,1948,1947,1946,1945,1944,1943,1942,1941,1940,1939,1938,1937,1936,1935,1934,1933,1932,1931,1930],
                    'attr' => ['class' => 'personDateField'],
                    'required' => in_array('passportValidDate', $options['required_fields'])
                ]
            );
        }

        if (in_array('driverLicenseNr', $options['enabled_fields'])) {
            $builder->add('driverLicenseNr', null, [
                    'required' => in_array('driverLicenseNr', $options['required_fields'])
                ]
            );
        }

        if (in_array('driverLicenseValid', $options['enabled_fields'])) {
            $builder->add('driverLicenseValid', null, [
                    'required' => in_array('driverLicenseValid', $options['required_fields'])
                ]
            );
        }

        if (in_array('nationality', $options['enabled_fields'])) {
            $builder->add('nationality', null, [
                    'required' => in_array('nationality', $options['required_fields'])
                ]
            );
        }

        if (in_array('company', $options['enabled_fields'])) {
            $builder->add('company', null, [
                    'required' => in_array('company', $options['required_fields'])
                ]
            );
        }

        if (in_array('customerId', $options['enabled_fields'])) {
            $builder->add('customerId', null, [
                    'required' => in_array('customerId', $options['required_fields'])
                ]
            );
        }


    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
            'country' => null,
            'enabled_fields' => ['postalCode', 'thoroughfare', 'phone', 'email'],
            'required_fields' => ['postalCode', 'thoroughfare', 'phone', 'email'],
            'validation_groups' => function (FormInterface $form) {
                $groups = [];

                if ($form->getConfig()->hasOption('required_fields') && is_array($form->getConfig()->getOption('required_fields'))) {
                    $groups = $form->getConfig()->getOption('required_fields');
                }

                $groups[] = 'Default';

                return $groups;
            },
            'birthday' => true
        ]);
    }
}