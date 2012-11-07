<?php

namespace Venu\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BlogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text',  array(
                        'attr' => array(
                            'placeholder' => 'Title of the blog'
                        )
                    ))
                ->add('blog', "textarea",  array(
                    'attr' => array(
                        'class' => 'tinymce',
                        'data-theme' => 'simple' // simple, advanced, bbcode
                    )
                ))
                ->add('tags', "textarea",  array(
                    'attr' => array(
                        'placeholder' => 'comma seperated'
                    )));
    }

    public function getName()
    {
        return 'venu_adminbundle_postype';
    }
}
