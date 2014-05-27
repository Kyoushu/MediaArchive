<?php

namespace Kyoushu\MediaBundle\Entity;

use Doctrine\ORM\EntityRepository;

class TvShowRepository extends EntityRepository
{
    public function getNameFirstCharacters()
    {
        $result = $this->getEntityManager()
            ->getRepository('KyoushuMediaBundle:TvShow')
            ->createQueryBuilder('t')
            ->select('SUBSTRING(t.name, 1, 1)')
            ->innerJoin('t.media', 'm')
            ->innerJoin('m.source', 's')
            ->andWhere('s.encoderDestination = 1')
            ->orderBy('t.name', 'ASC')
            ->getQuery()
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
            ->getRepository('KyoushuMediaBundle:TvShow')
            ->createQueryBuilder('t')
            ->innerJoin('t.media', 'm')
            ->innerJoin('m.source', 's')
            ->andWhere('s.encoderDestination = 1')
            ->andWhere('t.name LIKE :first_char_like')
            ->setParameter('first_char_like', $firstChar . '%')
            ->orderBy('t.name', 'ASC')
            ->getQuery()
            ->getResult();
        
    }
    
}