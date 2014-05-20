<?php

namespace Kyoushu\MediaBundle\Table;

interface ColumnInterface{
    
    public function getType();
    
    public function getValue($row);
    
    public function getLabel();
    
}