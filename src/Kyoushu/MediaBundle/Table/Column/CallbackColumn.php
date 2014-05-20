<?php

namespace Kyoushu\MediaBundle\Table\Column;

use Kyoushu\MediaBundle\Table\ColumnInterface;
use Closure;

class CallbackColumn implements ColumnInterface{
    
    private $label;
    private $callback;
    
    public function __construct($label, Closure $callback){
        $this->label = $label;
        $this->callback = $callback;
    }
    
    public function getLabel() {
        return $this->label;
    }

    public function getType() {
        return 'basic';
    }

    public function getValue($row){
        $callback = $this->callback;
        return $callback($row);
    }

}
