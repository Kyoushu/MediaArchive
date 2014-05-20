<?php

namespace Kyoushu\MediaBundle\Table\Column;

use Kyoushu\MediaBundle\Table\ColumnInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Closure;

class ControlColumn implements ColumnInterface{
    
    private $label;
    private $route;
    private $parametersCallback;
    private $visibilityCallback;
    
    public function __construct($label, $route, Closure $parametersCallback){
        $this->label = $label;
        $this->route = $route;
        $this->parametersCallback = $parametersCallback;
    }
    
    public function getLabel() {
        return $this->label;
    }

    public function getValue($row) {
        return '';
    }
    
    public function getRoute(){
        return $this->route;
    }
    
    public function getParameters($row){
        $callback = $this->parametersCallback;
        return $callback($row);
    }
    
    public function getType() {
        return 'control';
    }
    
    public function setVisibilityCallback(Closure $visibilityCallback){
        $this->visibilityCallback = $visibilityCallback;
    }
    
    public function isVisible($row){
        if(!isset($this->visibilityCallback)) return true;
        $callback = $this->visibilityCallback;
        return $callback($row);
    }
    

}
