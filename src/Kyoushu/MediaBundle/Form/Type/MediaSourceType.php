<?php

namespace Kyoushu\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MediaSourceType extends AbstractType
{
   
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        
        $resolver->setDefaults(array(
            'empty_value' => '',
            'label' => 'Media Source',
            'class' => 'Kyoushu\MediaBundle\Entity\MediaSource',
            'property' => 'name'
        ));
        
    }
    
    public function getParent(){
        return 'entity';
    }
    
    public function getName() {
        return 'media_source';
    }
    
}