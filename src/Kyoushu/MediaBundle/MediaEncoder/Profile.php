<?php

namespace Kyoushu\MediaBundle\MediaEncoder;

class Profile{
    
    private $name;
    
    private $container;
    private $videoCodec;
    private $videoBitrate;
    private $audioCodec;
    private $audioBitrate;
    private $maxHeight;
    
    public function __construct($name, array $config){
        $this->name = $name;
        $this->container = $config['container'];
        $this->videoCodec = $config['video_codec'];
        $this->audioCodec = $config['audio_codec'];
        $this->videoBitrate = $config['video_bitrate'];
        $this->audioBitrate = $config['audio_bitrate'];
        $this->maxHeight = $config['max_height'];
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getDescription(){
        return sprintf(
            '%s (%s %s %s)',
            strtoupper($this->getname()),
            $this->getContainer(),
            $this->getVideoCodec(),
            $this->getAudioCodec()
        );
    }
    
    public function getFilenameMetadata(){
        if($this->getMaxHeight()){
            return sprintf(' - %sp %s %s', $this->getMaxHeight(), $this->getVideoCodec(), $this->getAudioCodec());
        }
        else{
            return sprintf(' - %s %s', $this->getVideoCodec(), $this->getAudioCodec());
        }
        
    }
    
    public function getContainer() {
        return $this->container;
    }

    public function getVideoCodec() {
        return $this->videoCodec;
    }

    public function getVideoBitrate() {
        return $this->videoBitrate;
    }

    public function getAudioCodec() {
        return $this->audioCodec;
    }

    public function getAudioBitrate() {
        return $this->audioBitrate;
    }

    public function getMaxHeight() {
        return $this->maxHeight;
    }
    
    public function __toString(){
        return $this->getDescription();
    }
    
}
