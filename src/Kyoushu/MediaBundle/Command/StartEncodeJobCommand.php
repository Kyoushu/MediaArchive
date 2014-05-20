<?php

namespace Kyoushu\MediaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Kyoushu\MediaBundle\Entity\MediaEncodeJob;
use Kyoushu\MediaBundle\MediaEncoder\Exception\MediaEncoderException;
use Symfony\Component\Console\Input\InputOption;
use Doctrine\ORM\NoResultException;

class StartEncodeJobCommand extends ContainerAwareCommand{
    
    const NAME = 'kyoushu:media:start-encode-job';
    
    protected function configure(){
        $this
            ->setName(self::NAME)
            ->addOption('id', null, InputOption::VALUE_REQUIRED, 'MediaEncodeJob entity ID')
            ->addOption('auto', null, InputOption::VALUE_NONE, 'Automatically start the next pending job if none running')
            ->setDescription('Encode a video in the encode job queue')
        ;
    }
    
    /**
     * Get Doctrine ORM entity manager
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEntityManager(){
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }
    
    /**
     * 
     * @return \Kyoushu\MediaBundle\MediaScanner\Processor
     */
    private function getMediaProcessor(){
        return $this->getContainer()->get('kyoushu_media.processor');
    }
    
    private function getDestinationMediaSource(){
        
        try{
        
            return $this->getEntityManager()
                ->getRepository('KyoushuMediaBundle:MediaSource')
                ->createQueryBuilder('s')
                ->andWhere('s.encoderDestination = 1')
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();
            
        }
        catch(NoResultException $e){
            throw new \RuntimeException('No encoder destination available');
        }
        
    }
    
    /**
     * Get encoder manager
     * @return \Kyoushu\MediaBundle\MediaEncoder\Manager
     */
    private function getEncoderManager(){
        return $this->getContainer()->get('kyoushu_media.encoder_manager');
    }
    
    /**
     * Get encoder profile
     * @param string $name
     * @return \Kyoushu\MediaBundle\MediaEncoder\Profile
     */
    private function getEncoderProfile($name){
        return $this->getEncoderManager()->getProfile($name);
    }
    
    private function isMediaEncodeJobRunning(){
        
        return (bool)$this->getEntityManager()
            ->getRepository('KyoushuMediaBundle:MediaEncodeJob')
            ->createQueryBuilder('j')
            ->select('count(j.id)')
            ->andWhere('j.status = :status_encoding')
            ->setParameter('status_encoding', MediaEncodeJob::STATUS_ENCODING)
            ->getQuery()
            ->getSingleScalarResult();
        
    }
    
    private function getMediaEncodeJob($id){
        
        return $this->getEntityManager()
            ->getRepository('KyoushuMediaBundle:MediaEncodeJob')
            ->find($id);
        
    }
    
    private function getPendingMediaEncodeJob(){
        
        return $this->getEntityManager()
            ->getRepository('KyoushuMediaBundle:MediaEncodeJob')
            ->createQueryBuilder('j')
            ->andWhere('j.status = :status_pending')
            ->setParameter('status_pending', MediaEncodeJob::STATUS_PENDING)
            ->orderBy('j.created', 'DESC')
            ->getQuery()
            ->getSingleResult();
        
    }
    
    private function startEncoding(MediaEncodeJob $job, OutputInterface $output){
        
        $em = $this->getEntityManager();
        
        if($job->getStatus() !== MediaEncodeJob::STATUS_PENDING){
            throw new \RuntimeException(sprintf(
                'Cannot start encode job with status "%s"',
                $job->getStatus()
            ));
        }
        
        $output->writeln(sprintf(
            'Encoding <info>%s</info> with <info>%s</info> encoder profile',
            (string)$job->getSourceMedia(),
            $job->getEncoderProfileName()
        ));
        
        $sourceMedia = $job->getSourceMedia();
        $destinationMediaSource = $this->getDestinationMediaSource();
        $profile = $this->getEncoderProfile( $job->getEncoderProfileName() );
        
        $processor = $this->getContainer()->get('kyoushu_media.processor');
        
        if($sourceMedia->getProcessed() === null){
            $output->writeln('Processing source media');    
            $processor->processMedia($sourceMedia);
        }
        
        $encoder = $this->getEncoderManager()->getDefaultEncoder();
        $output->writeln(sprintf('Encoding media with %s', $encoder->getName()));
        
        $job->setStatus(MediaEncodeJob::STATUS_ENCODING);
        $em->persist($job);
        $em->flush();
        
        try{
        
            $destinationMedia = $this->getEncoderManager()->encodeMedia($sourceMedia, $destinationMediaSource, $profile, $encoder);
        
            $job->setDestinationMedia($destinationMedia);
            $job->setStatus(MediaEncodeJob::STATUS_DONE);
            
            $em->persist($destinationMedia);
            $em->persist($job);
            $em->flush();
            
            $output->writeln('Processing destination media');
            $processor->processMedia($destinationMedia);
            
        }
        catch (MediaEncoderException $e) {
            
            $job->setStatus(MediaEncodeJob::STATUS_FAILED);
            $job->setFailedReason($e->getMessage());
            $em->persist($job);
            $em->flush();
            
            throw $e;
            
        }
        
    }
    
    protected function execute(InputInterface $input, OutputInterface $output){
        
        $id = $input->getOption('id');
        $auto = $input->getOption('auto');
        
        if($id){
            $job = $this->getMediaEncodeJob($id);
            if(!$job) throw new \RuntimeException(sprintf('Encode job #%s could not be found', $id));
            $this->startEncoding($job, $output);
        }
        elseif($auto){
            
            if($this->isMediaEncodeJobRunning()){
                $output->writeln('Encode job already running');
                return;
            }
            
            $job = $this->getPendingMediaEncodeJob();
            if(!$job){
                $output->writeln('No encode jobs pending');
                return;
            }
            
            $this->startEncoding($job, $output);
            
        }
        else{
            throw new \RuntimeException('No options specified');
        }
        
    }
    
}
