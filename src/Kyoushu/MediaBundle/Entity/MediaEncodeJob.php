<?php

namespace Kyoushu\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kyoushu\MediaBundle\Entity\Media;
use Kyoushu\MediaBundle\Form\Mapping as Form;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * MediaEncodeJob
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class MediaEncodeJob
{
    
    const STATUS_PENDING = 'pending';
    const STATUS_ENCODING = 'encoding';
    const STATUS_DONE = 'done';
    const STATUS_FAILED = 'failed';
    
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var \Kyoushu\MediaBundle\Entity\Media
     * 
     * @ORM\ManyToOne(targetEntity="Media", inversedBy="sourceEncodeJobs")
     * @ORM\JoinColumn(name="sourceMediaId", referencedColumnName="id", onDelete="SET NULL")
     * @Form\Field(type="media", options={"empty_value"=""}, weight=30)
     * @Assert\NotBlank
     */
    protected $sourceMedia;
    
    /**
     * @var \Kyoushu\MediaBundle\Entity\Media
     * 
     * @ORM\ManyToOne(targetEntity="Media", inversedBy="destinationEncodeJobs")
     * @ORM\JoinColumn(name="destinationMediaId", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $destinationMedia;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $created;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=20)
     * @Form\Field(type="media_encode_job_status", weight=50)
     * @Assert\NotBlank
     */
    protected $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="statusChanged", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    protected $statusChanged;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="failedReason", type="text", nullable=true)
     * @Form\Field(type="textarea", weight=60)
     */
    protected $failedReason;

    /**
     * @var string
     *
     * @ORM\Column(name="encoderProfileName", type="string", length=20)
     * @Form\Field(type="encoder_profile", weight=40)
     * @Assert\NotBlank
     */
    protected $encoderProfileName;
    
    public function __construct(){
        $this->setStatus(self::STATUS_PENDING);
    }

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
     * Set created
     *
     * @param \DateTime $created
     * @return MediaEncodeJob
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return MediaEncodeJob
     */
    public function setStatus($status)
    {
        if($status !== $this->status){
            $this->setStatusChanged( new \DateTime('now') );
        }
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set statusChanged
     *
     * @param \DateTime $statusChanged
     * @return MediaEncodeJob
     */
    public function setStatusChanged($statusChanged)
    {
        $this->statusChanged = $statusChanged;

        return $this;
    }

    /**
     * Get statusChanged
     *
     * @return \DateTime 
     */
    public function getStatusChanged()
    {
        return $this->statusChanged;
    }

    /**
     * Set encoderProfileName
     *
     * @param string $encoderProfileName
     * @return MediaEncodeJob
     */
    public function setEncoderProfileName($encoderProfileName)
    {
        $this->encoderProfileName = $encoderProfileName;

        return $this;
    }

    /**
     * Get encoderProfileName
     *
     * @return string 
     */
    public function getEncoderProfileName()
    {
        return $this->encoderProfileName;
    }
    
    /**
     * get sourceMedia
     * @return Kyoushu\MediaBundle\Entity\Media
     */
    public function getSourceMedia() {
        return $this->sourceMedia;
    }

    /**
     * Get destinationMedia
     * @return Kyoushu\MediaBundle\Entity\Media
     */
    public function getDestinationMedia() {
        return $this->destinationMedia;
    }

    /**
     * Set sourceMedia
     * @param \Kyoushu\MediaBundle\Entity\Media $sourceMedia
     * @return \Kyoushu\MediaBundle\Entity\MediaEncodeJob
     */
    public function setSourceMedia(Media $sourceMedia) {
        $this->sourceMedia = $sourceMedia;
        return $this;
    }

    /**
     * Set destinationMedia
     * @param \Kyoushu\MediaBundle\Entity\Media $destinationMedia
     * @return \Kyoushu\MediaBundle\Entity\MediaEncodeJob
     */
    public function setDestinationMedia(Media $destinationMedia = null) {
        $this->destinationMedia = $destinationMedia;
        return $this;
    }
    
    public function getDescription(){
        return sprintf(
            'Encode "%s" with %s profile',
            $this->getSourceMedia(),
            strtoupper($this->getEncoderProfileName())
        );
    }
    
    /**
     * Get failedReason
     * @return string
     */
    public function getFailedReason() {
        return $this->failedReason;
    }

    /**
     * Set failedReason
     * @param string $failedReason
     * @return \Kyoushu\MediaBundle\Entity\MediaEncodeJob
     */
    public function setFailedReason($failedReason) {
        $this->failedReason = $failedReason;
        return $this;
    }



}
