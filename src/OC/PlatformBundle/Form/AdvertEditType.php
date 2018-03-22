<?php

namespace OC\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class AdvertEditType2 extends AbstractType
{
    public function getParent ()
    {
        return AdvertType::class;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('date');
    }
}
