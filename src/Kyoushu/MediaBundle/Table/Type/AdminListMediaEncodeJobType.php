<?php

namespace Kyoushu\MediaBundle\Table\Type;

use Kyoushu\MediaBundle\Table\Column\BasicColumn;
use Kyoushu\MediaBundle\Table\Column\CallbackColumn;
use Kyoushu\MediaBundle\Table\Column\DateTimeColumn;
use Kyoushu\MediaBundle\Entity\MediaEncodeJob;
use Kyoushu\MediaBundle\Table\Column\ControlColumn;

class AdminListMediaEncodeJobType extends AdminListEntitiesType{
    
    
    public function build(){
        
        if(!$this->definition) return;
        
        $this->addColumn('id', new BasicColumn('ID', 'id'));
        
        $this->addColumn('name', new BasicColumn('Name', 'description'));
        
        /*$this->addColumn('created', new DateTimeColumn(
            'Created',
            'created',
            'jS F Y, H:i'
        ));*/
        
        $this->addColumn('status', new CallbackColumn(
                'Status',
                function(MediaEncodeJob $job){
                    return ucwords($job->getStatus());
                }
        ));
        
        $this->addColumn('statusChanged', new DateTimeColumn(
            'Status Last Changed',
            'statusChanged',
            'jS F Y, H:i'
        ));
        
        $startControl = new ControlColumn(
            'Start',
            'kyoushu_media_admin_start_media_encode_job',
            function(MediaEncodeJob $job){
                return array(
                    'id' => $job->getId()
                );
            }
        );
        
        $startControl->setVisibilityCallback(function(MediaEncodeJob $job){
            return $job->getStatus() === MediaEncodeJob::STATUS_PENDING;
        });
        
        $this->addColumn('start', $startControl);
        
        parent::build();
        
    }
    
}
