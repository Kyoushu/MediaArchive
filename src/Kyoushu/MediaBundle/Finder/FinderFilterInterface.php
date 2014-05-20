<?php

namespace Kyoushu\MediaBundle\Finder;

use Doctrine\ORM\QueryBuilder;

interface FinderFilterInterface{
    
    public function __construct($property, $formType, $formOptions = array());
    
    public function apply(QueryBuilder $queryBuilder, $value);
    
    public function getProperty();
    
    public function getFormType();
    
    public function getFormOptions();
    
}