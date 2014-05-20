<?php

namespace Kyoushu\MediaBundle\Finder\Filter;

use Kyoushu\MediaBundle\Finder\FinderFilterInterface;
use \Doctrine\ORM\QueryBuilder;
use Kyoushu\MediaBundle\Finder\EntityFinder;

class KeywordFilter implements FinderFilterInterface{
    
    private $property;
    private $formType;
    private $formOptions;
    
    public function __construct($property, $formType, $formOptions = array()) {
        $this->property = $property;
        $this->formType = $formType;
        $this->formOptions = $formOptions;
    }

    public function apply(QueryBuilder $queryBuilder, $value){
        
        $parameterName = sprintf('%s_%s', $this->property, uniqid());
        
        $queryBuilder->andWhere(
            sprintf(
                '%s.%s LIKE :%s',
                EntityFinder::QUERY_BUILDER_ALIAS,
                $this->property,
                $parameterName
            )
        );
        
        $queryBuilder->setParameter($parameterName, '%' . $value . '%');
        
    }

    public function getFormOptions() {
        return $this->formOptions;
    }

    public function getFormType() {
        return $this->formType;
    }

    public function getProperty() {
        return $this->property;
    }

}
