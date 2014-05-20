<?php

namespace Kyoushu\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Kyoushu\MediaBundle\Form\Type\CommaSeparatedType;

class EntityTableContextType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add('entityIds', new CommaSeparatedType(), array(
            'attr' => array(
                'data-entity-table-context-entity-ids' => true
            )
        ));
    }
    
    public function getName() {
        return 'entity_table_context';
    }
    
}