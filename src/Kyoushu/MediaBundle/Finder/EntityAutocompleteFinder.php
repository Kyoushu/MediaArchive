<?php

namespace Kyoushu\MediaBundle\Finder;

use Kyoushu\MediaBundle\Finder\Factory;
use Symfony\Component\PropertyAccess\PropertyAccess;

class EntityAutocompleteFinder{
    
    private $factory;
    private $entityClass;
    
    private $searchString;
    private $searchProperties;
    private $property;
    private $limit;
    
    const DEFAULT_LIMIT = 20;
    
    public function __construct(Factory $factory, $entityClass){
        $this->factory = $factory;
        $this->entityClass = $entityClass;
        $this->limit = self::DEFAULT_LIMIT;
    }
    
    /**
     * Get limit
     * @return integer
     */
    public function getLimit() {
        return $this->limit;
    }

    /**
     * Set limit
     * @param integer $limit
     * @return \Kyoushu\MediaBundle\Finder\EntityAutocompleteFinder
     */
    public function setLimit($limit) {
        $this->limit = (int)$limit;
        return $this;
    }
        
    /**
     * Get entityManager
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEntityManager(){
        return $this->factory->getEntityManager();
    }

    /**
     * Get entityClass
     * @return string
     */
    public function getEntityClass() {
        return $this->entityClass;
    }
    
    /**
     * Get searchString
     * @return string
     */
    public function getSearchString() {
        return trim($this->searchString);
    }

    /**
     * Get searchProperties
     * @return array
     */
    public function getSearchProperties() {
        return $this->searchProperties;
    }

    /**
     * Get property
     * @return string
     */
    public function getProperty() {
        return $this->property;
    }

    /**
     * Set searchString
     * @param string $searchString
     * @return \Kyoushu\MediaBundle\Finder\EntityAutocompleteFinder
     */
    public function setSearchString($searchString) {
        $this->searchString = $searchString;
        return $this;
    }

    /**
     * Set search properties
     * @param array $searchProperties
     * @return \Kyoushu\MediaBundle\Finder\EntityAutocompleteFinder
     */
    public function setSearchProperties(array $searchProperties) {
        $this->searchProperties = $searchProperties;
        return $this;
    }

    /**
     * Set property
     * @param string $property
     * @return \Kyoushu\MediaBundle\Finder\EntityAutocompleteFinder
     */
    public function setProperty($property) {
        $this->property = $property;
        return $this;
    }
    
    public function getResult(){
        
        $entities = $this->getEntityManager()
            ->getRepository( $this->entityClass )
            ->findAll();
        
        $searchProperties = $this->searchProperties;
        $searchString = $this->searchString;
        $searchStringParts = explode(' ', $searchString);
        $accessor = PropertyAccess::createPropertyAccessor();
        
        $result = array();
        
        foreach($entities as $entity){
            
            if(count($result) >= $this->limit) break;
            
            if(!$searchString){
                $result[] = $entity;
            }
            elseif(count($searchProperties) === 0){
                $result[] = $entity;
            }
            else{
                foreach($searchProperties as $property){
                    
                    $value = $accessor->getValue($entity, $property);
                    $searchStringPartsMatched = 0;
                    
                    foreach($searchStringParts as $searchStringPart){
                        
                        if($searchStringPart && $value && stristr($value, $searchStringPart)){
                            $searchStringPartsMatched++;
                        }
                        
                        if($searchStringPartsMatched === count($searchStringParts)){
                            $result[] = $entity;
                            continue;
                        }
                        
                    }
                    
                    
                }
            }
            
        }
        
        return $result;        
        
    }
    

}
