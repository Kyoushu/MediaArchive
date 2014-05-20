<?php

namespace Kyoushu\MediaBundle\Table\Column;

use Kyoushu\MediaBundle\Table\ColumnInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class DateTimeColumn implements ColumnInterface{
    
    private $accessor;
    private $propertyPath;
    private $label;
    private $format;
    
    public function __construct($label, $propertyPath, $format = 'jS F Y'){
        $this->label = $label;
        $this->format = $format;
        $this->propertyPath = $propertyPath;
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }
    
    public function getLabel() {
        return $this->label;
    }

    public function getType() {
        return 'datetime';
    }

    public function getValue($row) {
        $value = $this->accessor->getValue($row, $this->propertyPath);
        if($value instanceof \DateTime){
            return $value->format($this->format);
        }
        return '';
    }

}
