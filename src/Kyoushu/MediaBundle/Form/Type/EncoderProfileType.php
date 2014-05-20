<?php

namespace Kyoushu\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Kyoushu\MediaBundle\MediaEncoder\Manager;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EncoderProfileType extends AbstractType
{
    
    private $encoderManager;
    
    public function __construct(Manager $encoderManager){
        $this->encoderManager = $encoderManager;
    }
   
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        
        $choices = array();
        
        foreach($this->encoderManager->getProfiles() as $profile){
            $choices[$profile->getName()] = $profile->getDescription();
        }
        
        $resolver->setDefaults(array(
            'empty_value' => '',
            'choices' => $choices 
        ));
        
    }
    
    public function getParent(){
        return 'choice';
    }
    
    public function getName() {
        return 'encoder_profile';
    }
    
}