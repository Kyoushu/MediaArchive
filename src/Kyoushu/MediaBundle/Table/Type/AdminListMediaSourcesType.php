<?php

namespace Kyoushu\MediaBundle\Table\Type;

use Kyoushu\MediaBundle\Table\Type\AdminListEntitiesType;
use Kyoushu\MediaBundle\Table\Column\BasicColumn;
use Kyoushu\MediaBundle\Table\Column\BooleanColumn;
use Kyoushu\MediaBundle\Table\Column\ControlColumn;
use Kyoushu\MediaBundle\Table\Column\DateTimeColumn;

use Kyoushu\MediaBundle\Entity\MediaSource;

class AdminListMediaSourcesType extends AdminListEntitiesType{
    
    public function build(){
        
        if(!$this->definition) return;
        
        $this->addColumn('id', new BasicColumn('ID', 'id'));
        
        $this->addColumn('name', new BasicColumn('Name', 'name'));
        
        $this->addColumn('lastScanned', new DateTimeColumn(
            'Last Scanned',
            'lastScanned',
            'jS F Y, H:i'
        ));
        
        $this->addColumn('private', new BooleanColumn('Private', 'private'));
        
        $this->addColumn('encoderDestination', new BooleanColumn('Encoder Destination', 'encoderDestination'));
        
        $this->addColumn('scan', new ControlColumn(
            'Scan',
            'kyoushu_media_admin_scan_media_source',
            function(MediaSource $mediaSource){
                return array(
                    'id' => $mediaSource->getId()
                );
            }
        ));
        
        parent::build();
        
    }
    
}
