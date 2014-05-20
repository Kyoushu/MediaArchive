<?php

namespace Kyoushu\MediaBundle\Table;

use Kyoushu\MediaBundle\Table\ColumnInterface;
use Kyoushu\MediaBundle\Table\Exception\TableException;
use Symfony\Component\Form\FormInterface;

class Table {

    private $data;
    private $columns;
    private $formWrapper;
    
    public function __construct(){
        $this->columns = array();
        $this->data = array();
        $this->build();
        $this->formWrapper = null;
    }
    
    public function build(){
        
    }
    
    public function setFormWrapper(FormInterface $formWrapper){
        $this->formWrapper = $formWrapper;
        return $this;
    }
    
    public function getFormWrapper(){
        return $this->formWrapper;
    }
    
    public function createFormWrapperView(){
        return $this->formWrapper->createView();
    }
    
    public function hasFormWrapper(){
        return $this->formWrapper !== null;
    }
    
    public function hasColumn($name){
        return isset($this->columns[$name]);
    }
    
    public function addColumn($name, ColumnInterface $column){
        $this->columns[$name] = $column;
        return $this;
    }
    
    public function getColumn($name){
        if(!isset($this->columns[$name])){
            throw new TableException(sprintf(
                'The column %s does not exist',
                $name
            ));
        }
        return $this->columns[$name];
    }
    
    public function removeColumn($name){
        if(!isset($this->columns[$name])){
            throw new TableException(sprintf(
                'The column %s does not exist',
                $name
            ));
        }
        unset($this->columns[$name]);
    }
    
    public function setData(array $data){
        $this->data = $data;
        return $this;
    }
    
    public function getData(){
        return $this->data;
    }
    
    public function getColumns(){
        return $this->columns;
    }
    
}
