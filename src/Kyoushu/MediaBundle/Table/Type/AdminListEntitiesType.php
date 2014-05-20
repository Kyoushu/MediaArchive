<?php

namespace Kyoushu\MediaBundle\Table\Type;

use Kyoushu\MediaBundle\Table\Table;
use Kyoushu\MediaBundle\Table\Column\BasicColumn;
use Kyoushu\MediaBundle\Table\Column\ControlColumn;
use Kyoushu\MediaBundle\Admin\EntityDefinition;

class AdminListEntitiesType extends Table{
    
    protected $definition;
    
    public function setDefinition(EntityDefinition $definition){
        $this->definition = $definition;
        $this->build();
        return $this;
    }
    
    public function build(){
        
        if(!$this->definition) return;
        
        $definition = $this->definition;
        
        if(!$this->hasColumn('id')){
            $this->addColumn('id', new BasicColumn(
                'ID',
                'id'
            ));
        }
        
        if(!$this->hasColumn('name')){
            $this->addColumn('name', new BasicColumn(
                'Name',
                $definition->getNameProperty()
            ));
        }
        
        $this->addColumn('edit', new ControlColumn(
            'Edit',
            'kyoushu_media_admin_edit',
            function($row) use ($definition){
                return array(
                    'entityName' => $definition->getName(),
                    'id' => $row->getId()
                );
            }
        ));
        
        $this->addColumn('delete', new ControlColumn(
            'Delete',
            'kyoushu_media_admin_delete',
            function($row) use ($definition){
                return array(
                    'entityName' => $definition->getName(),
                    'id' => $row->getId()
                );
            }
        ));
        
    }
    
}
