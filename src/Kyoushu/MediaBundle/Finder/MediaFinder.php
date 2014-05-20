<?php

namespace Kyoushu\MediaBundle\Finder;

use Kyoushu\MediaBundle\Finder\Factory;
use Kyoushu\MediaBundle\Entity\TvShow;
use Kyoushu\MediaBundle\Finder\Exception\FinderException;

class MediaFinder {
    
    protected $factory;
    
    protected $category;
    
    protected $includePrivate;
    
    protected $tvShow;
    protected $seasonNumber;
    
    protected $sortOrder;
    protected $sortField;
    
    protected $offset;
    protected $limit;
        
    const SORT_ASC = 'ASC';
    const SORT_DESC = 'DESC';
    
    const DEFAULT_LIMIT = 20;
    
    public function __construct(Factory $factory){
        $this->factory = $factory;
        $this->category = null;
        
        $this->sortOrder = null;
        $this->sortBy = null;
        
        $this->offset = 0;
        $this->limit = self::DEFAULT_LIMIT;
    }
    
    /**
     * Get seasonNumber
     * @return integer
     */
    public function getSeasonNumber() {
        return $this->seasonNumber;
    }

    /**
     * Set seasonNumber
     * @param integer $seasonNumber
     * @return \Kyoushu\MediaBundle\Finder\MediaFinder
     */
    public function setSeasonNumber($seasonNumber) {
        $this->seasonNumber = (int)$seasonNumber;
        return $this;
    }

        
    /**
     * Get entityManager
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager(){
        return $this->factory->getEntityManager();
    }
    
    /**
     * Set tvShow
     * @param \Kyoushu\MediaBundle\Entity\TvShow $tvShow
     * @return \Kyoushu\MediaBundle\Finder\MediaFinder
     */
    public function setTvShow(TvShow $tvShow){
        $this->tvShow = $tvShow;
        return $this;
    }
    
    /**
     * Get tvShow
     * @return \Kyoushu\MediaBundle\Entity\TvShow
     */
    public function getTvShow() {
        return $this->tvShow;
    }
        
    /**
     * Set category
     * @param string $category
     * @return \Kyoushu\MediaBundle\Finder\MediaFinder
     */
    public function setCategory($category) {
        $this->category = $category;
        return $this;
    }
    
    /**
     * Get category
     * @return string
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * Set includePrivate
     * @param boolean $includePrivate
     * @return \Kyoushu\MediaBundle\Finder\MediaFinder
     */
    public function setIncludePrivate($includePrivate) {
        $this->includePrivate = $includePrivate;
        return $this;
    }

    /**
     * get includePrivate
     * @return boolean
     */
    public function getIncludePrivate() {
        return $this->includePrivate;
    }
        
    public function sortByReleaseDate($sortOrder = null){
        if($sortOrder === null) $sortOrder = self::SORT_DESC;
        $this->sortOrder = strtoupper($sortOrder);
        $this->sortField = 'm.releaseDate';
        return $this;
    }
    
    /**
     * Create a query builder for the current search parameters
     * @return \Doctrine\ORM\QueryBuilder
     * @throws FinderException
     */
    public function createQueryBuilder(){
        
        $qb = $this->getEntityManager()
                ->getRepository('KyoushuMediaBundle:Media')
                ->createQueryBuilder('m');
        
        $qb->innerJoin('m.source', 's');
        
        if(!$this->includePrivate){
            $qb->andwhere('s.private = 0');
        }
        
        if($this->tvShow){
            $qb->andWhere('m.tvShow = :tv_show_id');
            $qb->setParameter('tv_show_id', $this->tvShow->getId());
        }
        
        if($this->seasonNumber){
            $qb->andWhere('m.seasonNumber = :season_number');
            $qb->setParameter('season_number', $this->seasonNumber);
        }
        
        if($this->category){
            $qb->andWhere('m.category = :category');
            $qb->setParameter('category', $this->category);
        }
        
        if($this->sortField !== null && $this->sortOrder !== null){
            
            if($this->sortOrder !== self::SORT_ASC && $this->sortOrder !== self::SORT_DESC){
                throw new FinderException(sprintf(
                    '"%s" is not a valid sort order',
                    $this->sortOrder
                ));
            }
            
            $qb->orderBy($this->sortField, $this->sortOrder);
            
        }
        
        return $qb;
    }
    
    /**
     * Get offset
     * @return integer
     */
    public function getOffset() {
        return $this->offset;
    }

    /**
     * Get limit
     * @return integer
     */
    public function getLimit() {
        return $this->limit;
    }

    /**
     * Set offset
     * @param integer $offset
     * @return \Kyoushu\MediaBundle\Finder\MediaFinder
     */
    public function setOffset($offset) {
        $this->offset = (int)$offset;
        return $this;
    }

    /**
     * Set limit
     * @param integer $limit
     * @return \Kyoushu\MediaBundle\Finder\MediaFinder
     */
    public function setLimit($limit) {
        $this->limit = (int)$limit;
        return $this;
    }

        
    /**
     * Get results
     * @return array
     */
    public function getResult(){
        $result = $this->createQueryBuilder()
            ->getQuery()
            ->getResult();
        
        return array_slice($result, $this->offset, $this->limit);
    }
    
}
