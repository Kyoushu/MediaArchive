<?php

namespace Kyoushu\MediaBundle\Finder;

use Kyoushu\MediaBundle\Finder\Factory;
use Kyoushu\MediaBundle\Admin\EntityDefinition;
use Kyoushu\MediaBundle\Finder\FinderFilterInterface;
use Kyoushu\MediaBundle\Finder\Exception\FinderException;

class EntityFinder{
    
    private $definition;
    private $factory;
    private $filters;
    private $perPage;
    private $page;
    private $data;
    
    const QUERY_BUILDER_ALIAS = 'e';
    
    public function __construct(Factory $factory, EntityDefinition $definition){
        $this->factory = $factory;
        $this->definition = $definition;
        $this->filters = array();
        $this->data = array();
    }
    
    /**
     * 
     * @return integer
     */
    public function getPerPage() {
        if(!$this->perPage || $this->perPage < 1){
            return 1;
        }
        return $this->perPage;
    }

    /**
     * 
     * @return integer
     */
    public function getPage() {
        if(!$this->page || $this->page < 1){
            return 1;
        }
        return $this->page;
    }

    /**
     * 
     * @param integer $perPage
     * @return \Kyoushu\MediaBundle\Finder\EntityFinder
     */
    public function setPerPage($perPage) {
        $this->perPage = (int)$perPage;
        return $this;
    }

    /**
     * 
     * @param integer $page
     * @return \Kyoushu\MediaBundle\Finder\EntityFinder
     */
    public function setPage($page) {
        $this->page = (int)$page;
        return $this;
    }
        
    /**
     * @return \Kyoushu\MediaBundle\EntityDefinition
     */
    public function getDefinition() {
        return $this->definition;
    }
    
    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager(){
        return $this->factory->getEntityManager();
    }
    
    /**
     * Add filter
     * @param \Kyoushu\MediaBundle\FinderFilterInterface $filter
     * @return \Kyoushu\MediaBundle\Finder\EntityFinder
     */
    public function addFilter($name, FinderFilterInterface $filter){
        $this->filters[$name] = $filter;
        return $this;
    }
    
    /**
     * Get filter
     * @param string $name
     * @return \Kyoushu\MediaBundle\FinderFilterInterface
     * @throws FinderException
     */
    public function getFilter($name){
        if(!isset($this->filters[$name])){
            throw new FinderException(sprintf(
                'The filter %s does not exist',
                $name
            ));
        }
        return $this->filters[$name];
    }
    
    /**
     * 
     * @return array
     */
    public function getFilters(){
        return $this->filters;
    }
    
    /**
     * 
     */
    public function createQueryBuilder(){
       
        $entityClass = $this->getDefinition()->getClass();
        
        $queryBuilder = $this->getEntityManager()
            ->getRepository($entityClass)
            ->createQueryBuilder(self::QUERY_BUILDER_ALIAS);
        
        foreach($this->filters as $filterName => $filter){
            $value = $this->__get($filterName);
            if($value === null) continue;
            $filter->apply($queryBuilder, $value);
        }
        
        return $queryBuilder;
        
    }
    
    public function countTotal(){
        return $this->createQueryBuilder()
            ->select(sprintf('count(%s.id)', self::QUERY_BUILDER_ALIAS))
            ->getQuery()
            ->getSingleScalarResult();
    }
    
    public function getResult(){
        
        $queryBuilder = $this->createQueryBuilder();
        
        if(isset($this->perPage) && isset($this->page)){
            
            $perPage = $this->getPerPage();
            $page = $this->getPage();
            
            $maxResults = $perPage;
            $firstResult = $perPage * ($page - 1);
            
            $queryBuilder->setMaxResults($maxResults);
            $queryBuilder->setFirstResult($firstResult);
            
        }
        
        return $queryBuilder->getQuery()->getResult();
        
    }
    
    public function __set($name, $value){
        $this->data[$name] = $value;
    }
    
    public function __get($name){
        if(!isset($this->data[$name])) return null;
        return $this->data[$name];
    }
    
}
