<?php

namespace Kyoushu\MediaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessMediaCommand extends ContainerAwareCommand{
    
    protected function configure(){
        $this
            ->setName('kyoushu:media:process')
            ->setDescription('Process media files (show name, episode number, etc.)')
            ->addArgument('batch-size', InputArgument::REQUIRED, 'Max number of media files to process')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output){
        
        $output->writeln('Processing media sources');
        
        $batchSize = (int)$input->getArgument('batch-size');
        $processor = $this->getContainer()->get('kyoushu_media.processor');
        
        foreach($processor->getUnprocessedMedia($batchSize) as $media){
            $output->writeln(sprintf('Processing <info>%s</info>', $media->getFilename()));
            $processor->processMedia($media);
        }
        
        
    }
    
}
