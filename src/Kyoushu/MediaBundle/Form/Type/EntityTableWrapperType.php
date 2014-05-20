<?php

namespace Kyoushu\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Kyoushu\MediaBundle\Form\CollectionOptionGenerator\ValueGenerator;

class EntityTableWrapperType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options){
        
        $table = $builder->getData();
        $data = $table->getData();
        
        $idData = array();
        foreach($data as $row){
            $idData[$row->getId()] = false;
        }
        
        $idValueGenerator = new ValueGenerator(array_keys($idData));
        
        $builder->add('selectedIds', 'collection', array(
            'data' => $idData,
            'mapped' => false,
            'type' => 'checkbox',
            'options' => array(
                'value' => $idValueGenerator,
                'attr' => array(
                    'data-table-row-checkbox' => true
                )
            )
        ));
        
        $builder->add('selectAll', 'button', array(
            'label' => 'Select All',
            'attr' => array(
                'data-table-select-all-row-checkboxes' => true,
                'class' => 'tiny'
            )
        ));
        
        $builder->add('deselectAll', 'button', array(
            'label' => 'De-select All',
            'attr' => array(
                'data-table-deselect-all-row-checkboxes' => true,
                'class' => 'tiny'
            )
        ));
        
        $builder->add('invert', 'button', array(
            'label' => 'Invert',
            'attr' => array(
                'data-table-invert-row-checkboxes' => true,
                'class' => 'tiny'
            )
        ));
        
        
        
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver){
        
        $resolver->setDefaults(array(
            'class' => 'Kyoushu\MediaBundle\Table\Table',
            'attr' => array(
                'data-entity-table-wrapper' => true
            )
        ));
        
    }
    
    public function getName() {
        return 'entity_table_wrapper';
    }
    
}