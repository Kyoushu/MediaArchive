<?php

namespace Kyoushu\MediaBundle\Table\Type;

use Kyoushu\MediaBundle\Table\Type\AdminListEntitiesType;
use Kyoushu\MediaBundle\Table\Column\BasicColumn;
use Kyoushu\MediaBundle\Table\Column\ControlColumn;

use Kyoushu\MediaBundle\Entity\Media;

class AdminListMediaType extends AdminListEntitiesType{
    
    public function build(){
        
        if(!$this->definition) return;
        
        $this->addColumn('id', new BasicColumn('ID', 'id'));
        
        $this->addColumn('source', new BasicColumn('Source', 'source.name'));
        
        $this->addColumn('name', new BasicColumn('Name', 'shortDescription'));
        
        $this->addColumn('process', new ControlColumn(
            'Process',
            'kyoushu_media_admin_process_media',
            function(Media $media){
                return array(
                    'id' => $media->getId()
                );
            }
        ));
        
        parent::build();
        
    }
    
}
