<?php

namespace Kyoushu\MediaBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Kyoushu\MediaBundle\Entity\MediaSource;

class DoctrineEventSubscriber implements EventSubscriber {
    
    private $kernelRootDir;
    
    public function __construct($kernelRootDir){
        $this->kernelRootDir = $kernelRootDir;
    }
    
    public function getSubscribedEvents() {
        return array(
            'postPersist',
            'postUpdate'
        );
    }
    
    public function postPersist(LifecycleEventArgs $args){
        $this->createMediaSourceSymlinks($args);
    }
    
    public function postUpdate(LifecycleEventArgs $args){
        $this->createMediaSourceSymlinks($args);
    }
    
    private function createMediaSourceSymlinks(LifecycleEventArgs $args){
        
        $entity = $args->getEntity();
        if(!$entity instanceof MediaSource) return;
        
        if($entity->getPrivate()) return;
        
        $symlinkRoot = sprintf('%s/../web/media-sources', $this->kernelRootDir );
        if(!file_exists($symlinkRoot)){
            mkdir($symlinkRoot, 0777, true);
        }

        $target = $entity->getPath();
        $link = sprintf('%s/%s', $symlinkRoot, $entity->getSlug());
        
        if(file_exists($link)){
            unlink($link);
        }
            
        symlink($target, $link);
        
    }

}
