<?php

namespace Kyoushu\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class AdminEncodeMediaTableContextType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options){
        
        $builder->add('profile', 'encoder_profile', array(
            'constraints' => array(
                new Assert\NotBlank()
            )
        ));
        
        $builder->add('encodeMedia', 'submit', array(
            'attr' => array(
                'class' => 'tiny'
            )
        ));
        
    }
    
    public function getParent() {
        return 'entity_table_context';
    }
    
    public function getName() {
        return 'admin_encode_media_table_context';
    }
    
}