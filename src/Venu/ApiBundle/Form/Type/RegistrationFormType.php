<?php
namespace Venu\ApiBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // add your custom field
        $builder->add('name');
        $builder->add('dob');
        $builder->add('gender');
        $builder->add('location');
    }

    public function getName()
    {
        return 'pp_api_registration';
    }
} 