<?php

namespace Kyoushu\MediaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Kyoushu\MediaBundle\Entity\MediaEncodeJob;
use Doctrine\ORM\NoResultException;

class AutoQueueEncodeJobCommand extends ContainerAwareCommand{
    
    protected function configure(){
        $this
            ->setName('kyoushu:media:auto-queue-encode-job')
            ->setDescription('Automatically create an encode job if there isn\'t one pending')
        ;
    }
    
    /**
     * Get Doctrine ORM entity manager
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEntityManager(){
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }
    
    private function countJobsPending(){
        return $this->getEntityManager()
            ->getRepository('KyoushuMediaBundle:MediaEncodeJob')
            ->createQueryBuilder('j')
            ->select('COUNT(j.id)')
            ->andWhere('j.status = :pending_status')
            ->setParameter('pending_status', MediaEncodeJob::STATUS_PENDING)
            ->getQuery()
            ->getSingleScalarResult();
    }
    
    private function getNextUnencodedMedia(){
        try{
            $media = $this->getEntityManager()
                ->getRepository('KyoushuMediaBundle:Media')
                ->createQueryBuilder('m')
                ->leftJoin('m.sourceEncodeJobs', 'j')
                ->groupBy('m.id')
                ->andHaving('COUNT(j.id) = 0')
                ->orderBy('m.releaseDate', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();   
            
            $processor = $this->getContainer()->get('kyoushu_media.processor');
            $processor->processMedia($media);
            
            return $media;
        }
        catch(NoResultException $e){
            return null;
        }
    }
    
    protected function execute(InputInterface $input, OutputInterface $output){
        
        $jobsPending = $this->countJobsPending();
        $output->writeln(sprintf('<info>%s encode jobs</info> pending', $jobsPending));
        
        if($jobsPending > 0){
            $output->writeln('Cannot generate new encode job while current jobs are still pending');
            return;
        }
        
        $media = $this->getNextUnencodedMedia();
        
        if(!$media){
            $output->writeln('No media candidates found');
            return;
        }
        
        $output->writeln(sprintf('Creating encode job for <info>%s</info>', $media));
        
        $encoderManager = $this->getContainer()->get('kyoushu_media.encoder_manager');
        $profileName = $encoderManager->getDefaultProfileName();
        
        $job = new MediaEncodeJob();
        $job->setStatus(MediaEncodeJob::STATUS_PENDING);
        $job->setSourceMedia($media);
        $job->setEncoderProfileName($profileName);
        
        $em = $this->getEntityManager();
        $em->persist($job);
        $em->flush();
        
    }
    
}
