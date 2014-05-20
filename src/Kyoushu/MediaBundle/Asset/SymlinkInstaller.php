<?php

namespace Kyoushu\MediaBundle\Asset;

use Symfony\Component\Console\Output\OutputInterface;
use Kyoushu\MediaBundle\Asset\Exception\AssetException;

class SymlinkInstaller {
    
    private $configSymlinkAssets;

    public function __construct(array $configSymlinkAssets){
        $this->configSymlinkAssets = $configSymlinkAssets;
    }
    
    public function installAssets(OutputInterface $output = null){
        
        foreach($this->configSymlinkAssets as $assetName => $assetConfig){
            
            if($output !== null){
                $output->writeln(sprintf('    Installing <info>%s</info>', $assetName));
            }
            
            $source = $assetConfig['source'];
            $destination = $assetConfig['destination'];
            $destinationParent = dirname($destination);
            
            if(!file_exists($source)){
                throw new AssetException(sprintf(
                    'The source "%s" does not exist',
                    $source
                ));
            }
            
            if(file_exists($destination) && is_link($destination)){
                unlink($destination);
            }
            
            if(file_exists($destination) && !is_link($destination)){
                throw new AssetException(sprintf(
                    'The destination "%s" already exists and is not a symbolic link',
                    $destination
                ));
            }
            
            if(!file_exists($destinationParent)){
                mkdir($destinationParent, 0777, true);
            }
            
            symlink($source, $destination);
            
        }
        
    }
    
}
