<?php

namespace Kyoushu\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdminEntityFinderType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        $finder = $builder->getData();
        
        foreach($finder->getFilters() as $filterName => $filter){
            
            $builder->add(
                $filterName,
                $filter->getFormType(),
                $filter->getFormOptions()
            );
            
        }
        
        $builder->add('filter', 'submit', array(
            'attr' => array(
                'class' => 'tiny'
            )
        ));
        
    }
    
    public function getName() {
        return 'admin_entity_finder';
    }
    
}