<?php

namespace Kyoushu\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Kyoushu\MediaBundle\Form\Mapping as Form;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * MediaSource
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class MediaSource
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Form\Field(type="text")
     * @Assert\NotBlank()
     */
    protected $name;
    
    /**
     * @var string
     * 
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128, unique=false)
     */
    protected $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255)
     * @Form\Field(type="text", weight=10)
     * @Assert\NotBlank()
     */
    protected $path;
    
    /**
     * @var boolean
     * 
     * @ORM\Column(name="private", type="boolean", nullable=true)
     * @Form\Field(type="checkbox", weight=20)
     */
    protected $private;
    
    /**
     * @var boolean
     * 
     * @ORM\Column(name="encoderDestination", type="boolean", nullable=true)
     * @Form\Field(type="checkbox", weight=30)
     */
    protected $encoderDestination;
    
    /**
     *
     * @var \DateTime
     * @ORM\Column(name="lastScanned", type="datetime", nullable=true)
     */
    protected $lastScanned;
    
    /**
     *
     * @var integer
     * @ORM\Column(name="scanIntervalSeconds", type="integer", nullable=true)
     * @Form\Field(type="number", options={"label"="Scan interval (seconds)"}, weight=30)
     * @Assert\NotBlank()
     */
    protected $scanIntervalSeconds;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return MediaSource
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return MediaSource
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return MediaSource
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }
    
    /**
     * Get private
     * @return boolean
     */
    public function getPrivate() {
        return $this->private;
    }

    /**
     * Set private
     * @param boolean $private
     * @return \Kyoushu\MediaBundle\Entity\MediaSource
     */
    public function setPrivate($private) {
        $this->private = $private;
        return $this;
    }
    
    /**
     * Get encoderDestination
     * @return boolean
     */
    public function getEncoderDestination() {
        return $this->encoderDestination;
    }

    /**
     * Set encoderDestination
     * @param boolean $encoderDestination
     * @return \Kyoushu\MediaBundle\Entity\MediaSource
     */
    public function setEncoderDestination($encoderDestination) {
        $this->encoderDestination = $encoderDestination;
        return $this;
    }
    
    public function getLastScanned() {
        return $this->lastScanned;
    }

    public function setLastScanned(\DateTime $lastScanned) {
        $this->lastScanned = $lastScanned;
        return $this;
    }
    
    public function getScanIntervalSeconds() {
        return $this->scanIntervalSeconds;
    }

    public function setScanIntervalSeconds($scanIntervalSeconds) {
        $this->scanIntervalSeconds = $scanIntervalSeconds;
        return $this;
    }
    
    public function __toString(){
        return $this->getName();
    }

}
