<?php

namespace Kyoushu\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdminListEntityTableWrapperType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options){
        
        foreach($options['context_forms'] as $contextForm){
            
            $builder->add($contextForm->getRevealId(), 'button', array(
                'label' => $contextForm->getButtonLabel(),
                'attr' => array(
                    'class' => 'tiny',
                    'data-reveal-id' => $contextForm->getRevealId(),
                    'data-entity-table-context' => true
                )
            ));
            
        }        
        
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver){
        
        $resolver->setRequired(array(
            'context_forms'
        ));
        
    }
    
    public function getParent() {
        return 'entity_table_wrapper';
    }
    
    public function getName() {
        return 'admin_list_entity_table_wrapper';
    }
    
}