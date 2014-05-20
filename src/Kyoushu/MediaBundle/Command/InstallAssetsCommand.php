<?php

namespace Kyoushu\MediaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallAssetsCommand extends ContainerAwareCommand{
    
    protected function configure(){
        $this
            ->setName('kyoushu:media:install-assets')
            ->setDescription('Install assets required by KyoushuMediaBundle')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output){
        
        $output->writeln('Installing assets for KyoushuMediaBundle');
        $installer = $this->getContainer()->get('kyoushu_media.asset.symlink_installer');
        $installer->installAssets($output);
        
    }
    
}
