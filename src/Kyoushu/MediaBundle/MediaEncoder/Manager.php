<?php

namespace Kyoushu\MediaBundle\MediaEncoder;

use Kyoushu\MediaBundle\MediaEncoder\EncoderInterface;
use Kyoushu\MediaBundle\MediaEncoder\Exception\MediaEncoderException;
use Kyoushu\MediaBundle\MediaEncoder\Profile;
use Kyoushu\MediaBundle\Entity\Media;
use Kyoushu\MediaBundle\Entity\MediaSource;
use Kyoushu\MediaBundle\Entity\MediaEncodeJob;
use Kyoushu\MediaBundle\Command\StartEncodeJobCommand;
use Symfony\Component\Process\Process;

class Manager {
    
    private $encoders;
    private $profiles;
    private $tempDir;
    private $defaultEncoderAlias;
    private $environment;
    private $kernelRootDir;
    
    public function __construct($kernelRootDir, $environment){
        $this->kernelRootDir = $kernelRootDir;
        $this->environment = $environment;
        $this->encoders = array();
        $this->profiles = array();
        $this->defaultEncoderAlias;
    }
    
    public function setTempDir($tempDir){
        $this->tempDir = $tempDir;
        return $this;
    }
    
    public function getTempDir(){
        return $this->tempDir;
    }
    
    /**
     * Set defaultEncoderAlias
     * @param type $defaultEncoderAlias
     * @return \Kyoushu\MediaBundle\MediaEncoder\Manager
     */
    public function setDefaultEncoderAlias($defaultEncoderAlias){
        $this->defaultEncoderAlias = $defaultEncoderAlias;
        return $this;
    }
    
    /**
     * Get defaultEncoderAlias
     * @return string
     */
    public function getDefaultEncoderAlias() {
        return $this->defaultEncoderAlias;
    }
    
    /**
     * Get default encoder
     * @return Kyoushu\MediaBundle\MediaEncoder\EncoderInterface
     */
    public function getDefaultEncoder(){
        $alias = $this->getDefaultEncoderAlias();
        return $this->getEncoder($alias);
    }
    
    /**
     * Add profile
     * @param \Kyoushu\MediaBundle\MediaEncoder\Profile $profile
     * @return \Kyoushu\MediaBundle\MediaEncoder\Manager
     */
    public function addProfile(Profile $profile, $alias = null){
        if(!$alias) $alias = $profile->getName();
        $this->profiles[$alias] = $profile;
        return $this;
    }
    
    /**
     * Get profile
     * @param string $name
     * @return Kyoushu\MediaBundle\MediaEncoder\Profile
     * @throws Kyoushu\MediaBundle\MediaEncoder\Exception\MediaEncoderException
     */
    public function getProfile($name){
        if(!isset($this->profiles[$name])){
            throw new MediaEncoderException(sprintf(
                'The encoder profile %s does not exist',
                $name
            ));
        }
        return $this->profiles[$name];
    }
    
    /**
     * Get profiles
     * @return array
     */
    public function getProfiles(){
        return $this->profiles;
    }
    
    /**
     * Add encoder
     * @param \Kyoushu\MediaBundle\MediaEncoder\EncoderInterface $encoder
     * @param string $alias
     * @return \Kyoushu\MediaBundle\MediaEncoder\Manager
     */
    public function addEncoder(EncoderInterface $encoder, $alias = null){
        if(!$alias) $alias = $encoder->getName();
        $this->encoders[$alias] = $encoder;
        return $this;
    }
    
    /**
     * Get encoder
     * @param string $alias
     * @return \Kyoushu\MediaBundle\MediaEncoder\EncoderInterface
     * @throws Kyoushu\MediaBundle\MediaEncoder\Exception\MediaEncoderException
     */
    public function getEncoder($alias){
        if(!isset($this->encoders[$alias])){
            throw new MediaEncoderException(sprintf(
                'The encoder %s does not exist',
                $alias
            ));
        }
        return $this->encoders[$alias];
    }
    
    /**
     * Get encoders
     * @return array
     */
    public function getEncoders(){
        return $this->encoders;
    }
    
    public function encodeMedia(Media $media, MediaSource $destinationMediaSource, Profile $profile, EncoderInterface $encoder = null){
        
        if($encoder === null) $encoder = $this->getDefaultEncoder();
        
        $destinationRelPath = $media->createRelPath( $profile->getContainer(), $profile->getFilenameMetadata() );
        
        $sourcePath = $media->getAbsPath();
        $tempPath = sprintf('%s/%s.%s', $this->getTempDir(), md5(serialize($media)), $profile->getContainer());
        $destinationPath = sprintf('%s/%s', $destinationMediaSource->getPath(), $destinationRelPath);
        
        $destinationDir = dirname($destinationPath);
        if(!file_exists($destinationDir)) mkdir($destinationDir, 0777, true);
        
        $tempDir = dirname($tempPath);
        if(!file_exists($tempDir)) mkdir($tempDir, 0777, true);
        
        if(!file_exists($tempPath)){
            $encoder->encode($sourcePath, $tempPath, $profile);
        }
        
        copy($tempPath, $destinationPath);
        chmod($destinationPath, 0666);
        
        if(!file_exists($destinationPath)){
            throw new MediaEncoderException("Encoded file could not be copied to destination");
        }
        
        unlink($tempPath);

        $destinationMedia = new Media;
        $destinationMedia->setSource($destinationMediaSource);
        $destinationMedia->setRelPath($destinationRelPath);
        $destinationMedia->setScanned( new \DateTime('now') );

        return $destinationMedia;
        
    }
    
    public function startMediaEncodeJob(MediaEncodeJob $job){
        
        if($job->getStatus() !== MediaEncodeJob::STATUS_PENDING){
            throw new MediaEncoderException('Encode job has already been started');
        }
        
        $cmd = sprintf(
            '%s/console --env=%s %s %s',
            $this->kernelRootDir,
            $this->environment,
            StartEncodeJobCommand::NAME,
            $job->getId()
        );
        
        $process = new Process($cmd);
        $process->setTimeout(null);
        $process->start();
        
        sleep(1);
        if($process->isTerminated()){
            if(!$process->isSuccessful()){
                $error = $process->getErrorOutput();
                throw new MediaEncoderException($error);
            }
        }
        
    }
    
}
