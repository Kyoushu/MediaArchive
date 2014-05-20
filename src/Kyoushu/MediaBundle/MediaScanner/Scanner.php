<?php

namespace Kyoushu\MediaBundle\MediaScanner;

use Doctrine\ORM\EntityManager;
use Kyoushu\MediaBundle\Entity\MediaSource;
use Kyoushu\MediaBundle\Entity\Media;
use Symfony\Component\Finder\Finder;

class Scanner{
    
    const REGEX_FILENAME_MEDIA = '/\.(mpg|avi|mkv|mov|mp4|m4v)$/i';
        
    private $entityManager;
    
    public function __construct(EntityManager $entityManager){
        $this->entityManager = $entityManager;
    }
    
    public function scanMediaSource(MediaSource $mediaSource){
        
        $rootPath = $mediaSource->getPath();
        $regexRootPath = sprintf('/^%s\//', preg_quote($rootPath, '/'));
        
        $finder = new Finder();
        $finder->files()->in($rootPath)->name(self::REGEX_FILENAME_MEDIA);
        
        foreach($finder as $file){
            $relPath = preg_replace($regexRootPath, '', (string)$file);
            $this->createMedia($mediaSource, $relPath);
        }
        
        $mediaSource->setLastScanned(new \DateTime('now'));
        $this->entityManager->persist($mediaSource);
        $this->entityManager->flush();
        
    }
    
    public function scanAllMediaSources(){
        $mediaSources = $this->entityManager->getRepository('KyoushuMediaBundle:MediaSource')->findAll();
        foreach($mediaSources as $mediaSource){
            $this->scanMediaSource($mediaSource);
        }
    }
    
    public function createMedia(MediaSource $mediaSource, $relPath){
        
        $exists = $this->entityManager->getRepository('KyoushuMediaBundle:Media')
                ->createQueryBuilder('m')
                ->select('count(m.id)')
                ->where('m.relPath = :relPath')
                ->andWhere('m.source = :mediaSourceId')
                ->setParameter('relPath', $relPath)
                ->setParameter('mediaSourceId', $mediaSource->getId())
                ->getQuery()
                ->getSingleScalarResult();
        
        if($exists) return false;
        
        $media = new Media();
        $media->setRelPath($relPath);
        $media->setScanned(new \DateTime('now'));
        $media->setSource($mediaSource);
        
        $this->entityManager->persist($media);
        $this->entityManager->flush();
        $this->entityManager->detach($media);
        
        return $media;
        
    }
    
    
    
}