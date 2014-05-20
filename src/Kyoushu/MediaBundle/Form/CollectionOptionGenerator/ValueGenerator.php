<?php

namespace Kyoushu\MediaBundle\Form\CollectionOptionGenerator;

class ValueGenerator {
    
    private $values;
    
    public function __construct(array $values){
        $this->values = $values;
    }
    
    public function __toString(){
        $keyVal = each($this->values);
        return (string)$keyVal['value'];
    }
    
}
