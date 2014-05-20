<?php

namespace Kyoushu\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdminEditEntityType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        $builder->add('save', 'submit');
        
    }
    
    public function getParent(){
        return 'annotation_reader';
    }
    
    public function getName() {
        return 'admin_edit';
    }
    
}