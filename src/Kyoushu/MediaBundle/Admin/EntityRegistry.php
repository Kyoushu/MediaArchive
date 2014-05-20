<?php

namespace Kyoushu\MediaBundle\Admin;

use Kyoushu\MediaBundle\Admin\EntityDefinition;
use Kyoushu\MediaBundle\Admin\Exception\AdminException;
use Doctrine\ORM\EntityManager;

class EntityRegistry {

    private $definitions;
    private $entityManager;
    
    public function __construct(EntityManager $entityManager){
        $this->definitions = array();
        $this->entityManager = $entityManager;
    }
    
    /**
     * Get entityManager
     * @return Doctrine\ORM\EntityManager
     */
    public function getEntityManager(){
        return $this->entityManager;
    }
    
    /**
     * Add definition
     * @param \Kyoushu\MediaBundle\Admin\EntityDefinition $definition
     */
    public function addDefinition(EntityDefinition $definition){
        $this->definitions[$definition->getName()] = $definition;
    }
    
    /**
     * Get definitions
     * @return array
     */
    public function getDefinitions(){
        return $this->definitions;
    }
    
    /**
     * Get definition
     * @param string $name
     * @return Kyoushu\MediaBundle\Admin\EntityDefinition
     * @throws Kyoushu\MediaBundle\Admin\AdminException
     */
    public function getDefinition($name){
        if(!isset($this->definitions[$name])){
            throw new AdminException(sprintf(
                'The entity definition %s does not exist',
                $name
            ));
        }
        return $this->definitions[$name];
    }
    
}
