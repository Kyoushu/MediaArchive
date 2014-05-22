<?php

namespace Kyoushu\MediaBundle\Entity;

use Doctrine\ORM\EntityRepository;

class TvShowRepository extends EntityRepository
{
    public function getNameFirstCharacters()
    {
        $result = $this->getEntityManager()
            ->createQuery('SELECT SUBSTRING(s.name, 1, 1) FROM KyoushuMediaBundle:TvShow s ORDER BY s.name ASC')
            ->getScalarResult();
        
        $chars = array_map(
            function($row){
                return $row[1];
            },
            $result
        );
            
        return array_unique($chars);
        
    }
    
    public function findByNameFirstCharacter($firstChar){
        
        return $this->getEntityManager()
            ->createQuery('select s from KyoushuMediaBundle:TvShow s where s.name like :first_char_like')
            ->setParameter('first_char_like', $firstChar . '%')
            ->getResult();
        
    }
    
}