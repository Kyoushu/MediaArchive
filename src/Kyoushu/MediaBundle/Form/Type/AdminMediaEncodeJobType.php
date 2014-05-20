<?php

namespace Kyoushu\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdminMediaEncodeJobType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        $builder->add('sourceMedia', 'media', array(
            'exclude_encoded' => true
        ));
        
        $builder->add('encoderProfileName', 'encoder_profile');
        
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        
        $resolver->setDefaults(array(
            'data_class' => 'Kyoushu\MediaBundle\Entity\MediaEncodeJob'
        ));
        
    }
    
    public function getName() {
        return 'admin_media_encode_job';
    }
    
}