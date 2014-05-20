<?php

namespace Kyoushu\MediaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class ScanMediaCommand extends ContainerAwareCommand{
    
    protected function configure(){
        $this
            ->setName('kyoushu:media:scan')
            ->setDescription('Scan media sources for new files')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Force re-scan all media sources')
        ;
    }
    
    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager(){
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }
    
    private function getMediaSourcesPendingScan(){
        
        $mediaSources = $this->getAllMediaSources();
        
        $now = new \DateTime('now');
        
        return array_filter($mediaSources, function($mediaSource) use ($now){
            
            $lastScanned = $mediaSource->getLastScanned();
            $intervalSeconds = $mediaSource->getScanIntervalSeconds();
            
            if(!$intervalSeconds) return false;
            if($lastScanned === null) return true;
            
            $expires = clone $lastScanned;
            $expires->add( \DateInterval::createFromDateString(sprintf('+%s seconds', $intervalSeconds)) );
            
            if($expires < $now) return true;
            return false;
            
        });
        
    }
    
    private function getAllMediaSources(){
        return $this->getEntityManager()
            ->getRepository('KyoushuMediaBundle:MediaSource')
            ->findAll();
    }
    
    protected function execute(InputInterface $input, OutputInterface $output){
        
        $output->writeln('Scanning media sources');
        
        $scanner = $this->getContainer()->get('kyoushu_media.scanner');
        
        if($input->getOption('force')){
            $output->writeln('Focrinc scan of all media sources');
            $mediaSources = $this->getAllMediaSources();
        }
        else{
            $mediaSources = $this->getMediaSourcesPendingScan();
        }
        
        foreach($mediaSources as $mediaSource){
            $output->writeln(sprintf('Scanning <info>%s</info>', $mediaSource->getName()));
            $scanner->scanMediaSource($mediaSource);
        }
        
    }
    
}
