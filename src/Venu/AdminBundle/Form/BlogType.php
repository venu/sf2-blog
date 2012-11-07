<?php

namespace Venu\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BlogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title')
                ->add('blog', "textarea", array('required' => false))
                ->add('tags');
    }

    public function getName()
    {
        return 'venu_adminbundle_postype';
    }
}
