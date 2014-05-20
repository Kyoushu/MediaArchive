<?php

namespace Kyoushu\MediaBundle\Table\Type;

use Kyoushu\MediaBundle\Table\Type\AdminListEntitiesType;
use Kyoushu\MediaBundle\Table\Column\BasicColumn;
use Kyoushu\MediaBundle\Table\Column\ControlColumn;

use Kyoushu\MediaBundle\Entity\TvShow;

class AdminListTvShowsType extends AdminListEntitiesType{
    
    public function build(){
        
        if(!$this->definition) return;
        
        $this->addColumn('id', new BasicColumn('ID', 'id'));
        
        $this->addColumn('name', new BasicColumn('Name', 'name'));
        
        $this->addColumn('scan', new ControlColumn(
            'Process',
            'kyoushu_media_admin_process_tv_show',
            function(TvShow $show){
                return array(
                    'id' => $show->getId()
                );
            }
        ));
        
        parent::build();
        
    }
    
}
