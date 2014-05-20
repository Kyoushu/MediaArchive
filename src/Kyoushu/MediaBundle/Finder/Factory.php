<?php

namespace Kyoushu\MediaBundle\Finder;

use Doctrine\ORM\EntityManager;
use Kyoushu\MediaBundle\Finder\MediaFinder;
use Kyoushu\MediaBundle\Finder\EntityFinder;
use Kyoushu\MediaBundle\Admin\EntityDefinition;
use Kyoushu\MediaBundle\Finder\EntityAutocompleteFinder;

class Factory {
    
    private $entityManager;
    
    function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }
    
    /**
     * Get entityManager
     * @return Doctrine\ORM\EntityManager
     */
    public function getEntityManager() {
        return $this->entityManager;
    }
    
    public function createMediaFinder(){
        return new MediaFinder($this);
    }
    
    public function createEntityFinder(EntityDefinition $definition){
        return new EntityFinder($this, $definition);
    }
    
    public function createEntityAutocompleteFinder($entityClass){
        return new EntityAutocompleteFinder($this, $entityClass);
    }
    
}
