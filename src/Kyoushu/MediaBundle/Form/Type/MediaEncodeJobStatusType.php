<?php

namespace Kyoushu\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Kyoushu\MediaBundle\Entity\MediaEncodeJob;

class MediaEncodeJobStatusType extends AbstractType
{
   
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        
        $choices = array(
            MediaEncodeJob::STATUS_PENDING => 'Pending',
            MediaEncodeJob::STATUS_ENCODING => 'Encoding',
            MediaEncodeJob::STATUS_DONE => 'Done',
            MediaEncodeJob::STATUS_FAILED => 'Failed'
        );
                
        $resolver->setDefaults(array(
            'choices' => $choices ,
            'empty_value' => ''
        ));
        
    }
    
    public function getParent(){
        return 'choice';
    }
    
    public function getName() {
        return 'media_encode_job_status';
    }
    
}