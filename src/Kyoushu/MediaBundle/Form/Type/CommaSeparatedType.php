<?php

namespace Kyoushu\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Kyoushu\MediaBundle\Form\DataTransformer\CommaSeparatedToArrayTransformer;

class CommaSeparatedType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options){
        $transformer = new CommaSeparatedToArrayTransformer();
        $builder->addModelTransformer($transformer);
    }
    
    public function getParent(){
        return 'hidden';
    }
    
    public function getName() {
        return 'comma_separated';
    }
    
}
