<?php

namespace Kyoushu\MediaBundle\Table\Type;

use Kyoushu\MediaBundle\Table\Table;
use Kyoushu\MediaBundle\Table\Column\BasicColumn;
use Kyoushu\MediaBundle\Table\Column\ControlColumn;

class AdminListEntityDefinitionsType extends Table{
    
    public function build(){
        
        $this->addColumn('name', new BasicColumn(
                'Name',
                'humanNamePlural'
        ));
        
        $this->addColumn('list', new ControlColumn(
            'List',
            'kyoushu_media_admin_list',
            function($row){
                return array(
                    'entityName' => $row->getName()
                );
            }
        ));
        
    }
    
}
