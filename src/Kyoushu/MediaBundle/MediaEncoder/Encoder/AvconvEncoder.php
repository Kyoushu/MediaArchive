<?php

namespace Kyoushu\MediaBundle\MediaEncoder\Encoder;

use Kyoushu\MediaBundle\MediaEncoder\EncoderInterface;
use Kyoushu\MediaBundle\MediaEncoder\Profile;
use Symfony\Component\Process\Process;
use FFMpeg\FFProbe;
use Kyoushu\MediaBundle\MediaEncoder\Exception\MediaEncoderException;

class AvconvEncoder implements EncoderInterface{
    
    private $videoCodecMap = array(
        'h264' => 'libx264'
    );
    
    public function encode($sourcePath, $destinationPath, Profile $profile){
        
        $cmd = $this->createAvconvCommand($sourcePath, $destinationPath, $profile);
        
        try{
            $process = new Process($cmd);
            $process->setTimeout(null);
            $process->run();
        }
        catch(\Exception $e){
            throw new MediaEncoderException('avconv: ' . $e->getMessage . "\n\n" . $cmd);
        }
        
        if(!file_exists($destinationPath)){
            throw new MediaEncoderException(
                sprintf(
                    "avconv: no output file generated\n\n%s\n\n%s",
                    $cmd,
                    $process->getOutput()
                )
            );
        }
        
        if(!$process->isSuccessful()){
            throw new MediaEncoderException('avconv: ' . $process->getErrorOutput() . "\n\n" . $cmd);
        }
        
    }
    
    private function getVideoCodec($sourcePath, $preferredVideoCodec, $forcePreferred = false){
        
        $probe = FFProbe::create();
        $streams = $probe->streams($sourcePath);
        $video = $streams->videos()->first();
        
        if( $video->has('codec_name') && !$forcePreferred ){
            $sourceCodec = $video->get('codec_name');
            if($sourceCodec === $preferredVideoCodec){
                return 'copy';
            }
        }
        
        if(isset($this->videoCodecMap[$preferredVideoCodec])){
            return $this->videoCodecMap[$preferredVideoCodec];
        }
        
        return $preferredVideoCodec;
        
    }
    
    private function getAudioCodec($sourcePath, $preferredAudioCodec){
        
        $probe = FFProbe::create();
        $streams = $probe->streams($sourcePath);
        $audio = $streams->audios()->first();
        
        if( $audio->has('codec_name') ){
            
            $sourceCodec = $audio->get('codec_name');
            
            if($sourceCodec === $preferredAudioCodec){
                return 'copy';
            }
            
        }
        
        return $preferredAudioCodec;
        
    }
    
    private function roundMultiplier($number, $multiplier = 8){
        return round($number / $multiplier) * $multiplier;
    }
    
    private function getDimensions($sourcePath, $maxHeight){
        
        $probe = FFProbe::create();
        $streams = $probe->streams($sourcePath);
        $video = $streams->videos()->first();
        
        $sourceDimensions = $video->getDimensions();
        
        $sourceHeight = (int)$sourceDimensions->getHeight();
        if($sourceHeight <= $maxHeight) return null;
        
        $sourceRatio = (float)$sourceDimensions->getRatio()->getValue();
        
        if($sourceHeight <= 0) return null;
        if($sourceRatio <= 0) return null;
        
        if($maxHeight && $maxHeight > 0 && $sourceHeight > $maxHeight){
            $height = $maxHeight;
            $width = $height * $sourceRatio;    
            return array( $this->roundMultiplier($width), $this->roundMultiplier($height) );
        }
            
        return null;
        
    }    
    
    public function createAvconvCommand($sourcePath, $destinationPath, Profile $profile){
        
        $dimensions = $this->getDimensions( $sourcePath, $profile->getMaxHeight() );
        
        if($dimensions !== null){
            $videoCodec = $this->getVideoCodec($sourcePath, $profile->getVideoCodec(), true);
        }
        else{
            $videoCodec = $this->getVideoCodec($sourcePath, $profile->getVideoCodec());
        }
        
        
        $audioCodec = $this->getaudioCodec($sourcePath, $profile->getAudioCodec());
        $audioBitrate = $profile->getAudioBitrate();
        $videoBitrate = $profile->getVideoBitrate();
        
        return sprintf(
            'avconv -y -i %s -c:v %s -c:a %s -ac 2 %s%s%s-strict experimental %s',
            escapeshellarg($sourcePath),
            $videoCodec,
            $audioCodec,
            ($audioBitrate !== null ? sprintf('-b:a %s ', $audioBitrate) : ''),
            ($videoBitrate !== null && $dimensions !== null ? sprintf('-b:v %s ', $videoBitrate) : ''),
            ($dimensions !== null ? sprintf('-filter:v scale=%s:%s ', $dimensions[0], $dimensions[1]) : ''),
            escapeshellarg($destinationPath)
        );
        
    }

    public function getName(){
        return 'avconv';
    }

}
