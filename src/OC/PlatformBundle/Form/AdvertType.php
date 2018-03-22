<?php

namespace OC\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use OC\PlatformBundle\Repository\CategoryRepository;

class AdvertType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Arbitrairement, on récupère toutes les catégories qui commencent par "D"
        $pattern = 'T%';
    
        $builder
            ->add('date',      DateTimeType::class)
            ->add('title',     TextType::class)
            ->add('author',    TextType::class)
            ->add('content',   TextareaType::class)
            ->add('published', CheckboxType::class, array('required' => false))
            ->add('image', ImageType::class)
            /** 
             * CollectionType
             * ->add('categories', CollectionType::class, array(
                   'entry_type'   => CategoryType::class,
                   'allow_add'    => true,
                   'allow_delete' => true
            ))
             * 
             */
             /** 
              * EntityType
              */
            ->add('categories', EntityType::class, array(
                  'class'        => 'OCPlatformBundle:Category',
                  'choice_label' => 'name',
                  'multiple'     => true,
                  'expanded' => false,
                  'query_builder' => function(CategoryRepository $repository) use($pattern) {
                  return $repository->getLikeQueryBuilder($pattern);
                  }
            ))
            ->add('save',      SubmitType::class);
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OC\PlatformBundle\Entity\Advert'
        ));
    }
  
}
