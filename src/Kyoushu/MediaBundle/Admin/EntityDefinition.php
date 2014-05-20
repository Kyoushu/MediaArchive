<?php

namespace Kyoushu\MediaBundle\Admin;

use Kyoushu\MediaBundle\Finder\FinderFilterInterface;
use Kyoushu\MediaBundle\Admin\Exception\AdminException;
use Kyoushu\MediaBundle\Finder\Filter\EqualsFilter;
use Kyoushu\MediaBundle\Admin\ListContextForm;

class EntityDefinition{
    
    private $name;
    private $class;
    private $nameProperty;
    private $listTableClass;
    private $listView;
    private $listFinderFiltersConfig;
    private $listContextForms;
    
    public function __construct($name, $config){
        $this->name = $name;
        $this->class = $config['class'];
        $this->listTableClass = $config['list_table_class'];
        $this->nameProperty = $config['name_property'];
        $this->listView = $config['list_view'];
        $this->listFinderFiltersConfig = $config['list_finder_filters'];
        
        $this->listContextForms = array();
        foreach($config['list_context_forms'] as $name => $listContextFormConfig){
            $this->listContextForms[] = new ListContextForm($this, $name, $listContextFormConfig);
        }
        
    }
    
    /**
     * get listContextForms
     * @return array
     */
    public function getListContextForms() {
        return $this->listContextForms;
    }
        
    /**
     * Get default filter array
     * @return array
     */
    public function createDefaultListFinderFilters(){
        
         $filters = array();
         
         $filters['id'] = new EqualsFilter('id', 'text', array(
             'label' => 'ID'
         ));
         
         return $filters;
        
    }
    
    public function createListFinderFilters(){
        $filters = array();
        
        foreach($this->createDefaultListFinderFilters() as $name => $filter){
            $filters[$name] = $filter;
        }
        
        foreach($this->listFinderFiltersConfig as $name => $config){
            
            $class = $config['class'];
            $formType = $config['form_type'];
            $formOptions = $config['form_options'];
            $property = $config['property'];
            
            $arguments = array($property, $formType, $formOptions);
            
            $ref = new \ReflectionClass($class);
            $filter = $ref->newInstanceArgs($arguments);
            
            if(!$filter instanceof FinderFilterInterface){
                throw new AdminException(sprintf(
                    '%s does not implement Kyoushu\MediaBundle\Finder\FinderFilterInterface',
                    $class
                ));
            }
            
            $filters[$name] = $filter;
        }
        return $filters;
    }
    
    public function getName(){
        return $this->name;
    }
    
    public function getHumanName(){
        return ucwords(str_replace('_', ' ', $this->getName()));
    }
    
    public function getHumanNamePlural(){
        return sprintf('%ss', $this->getHumanName());
    }
    
    public function getClass(){
        return $this->class;
    }
    
    public function getNameProperty() {
        return $this->nameProperty;
    }
    
    public function createListTable(){
        
        $class = (
            $this->listTableClass ?
            $this->listTableClass :
            'Kyoushu\MediaBundle\Table\Type\AdminListEntitiesType'
        );
              
        return new $class;
            
    }
    
    public function getListView(){
        if($this->listView) return $this->listView;
        return 'KyoushuMediaBundle:Admin:list.html.twig';
    }
    
}