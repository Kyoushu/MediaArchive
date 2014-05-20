<?php

namespace Kyoushu\MediaBundle\Table\Column;

use Kyoushu\MediaBundle\Table\ColumnInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class BasicColumn implements ColumnInterface{
    
    private $accessor;
    private $propertyPath;
    private $label;
    
    public function __construct($label, $propertyPath){
        $this->label = $label;
        $this->propertyPath = $propertyPath;
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }
    
    public function getLabel() {
        return $this->label;
    }

    public function getType() {
        return 'basic';
    }

    public function getValue($row) {
        return $this->accessor->getValue($row, $this->propertyPath);
    }

}
