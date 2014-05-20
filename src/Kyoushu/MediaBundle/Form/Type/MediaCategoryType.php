<?php

namespace Kyoushu\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Kyoushu\MediaBundle\Entity\Media;

class MediaCategoryType extends AbstractType
{
   
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        
        $choices = array(
            Media::CATEGORY_TV => 'TV Episode',
            Media::CATEGORY_MOVIE => 'Movie',
            Media::CATEGORY_UNKNOWN => 'Unknown'
        );
                
        $resolver->setDefaults(array(
            'empty_value' => '',
            'choices' => $choices 
        ));
        
    }
    
    public function getParent(){
        return 'choice';
    }
    
    public function getName() {
        return 'media_category';
    }
    
}