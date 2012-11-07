<?php

namespace Venu\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('comment');
    }

    public function getName()
    {
        return 'venu_blogbundle_commenttype';
    }
}
