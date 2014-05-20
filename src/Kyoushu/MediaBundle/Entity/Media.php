<?php

namespace Kyoushu\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kyoushu\MediaBundle\Form\Mapping as Form;

/**
 * Media
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Media
{
    
    const CATEGORY_MOVIE = 'movie';
    const CATEGORY_TV = 'tv';
    const CATEGORY_UNKNOWN = 'unknown';
    
    const REGEX_FILE_EXTENSION = '/\.(?P<extension>[0-9a-z]+)$/i';
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var Kyoushu\MediaBundle\Entity\MediaSource
     * 
     * @ORM\ManyToOne(targetEntity="MediaSource")
     * @ORM\JoinColumn(name="mediaSourceId", referencedColumnName="id")
     * @Form\Field(type="entity", options={"class"="Kyoushu\MediaBundle\Entity\MediaSource"}, weight=10)
     */
    protected $source;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     * @Form\Field(type="text", weight=0)
     */
    protected $name;
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Form\Field(type="textarea", weight=5)
     */
    
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="relPath", type="string", length=255)
     * @Form\Field(type="text", options={"label"="Relative Path"}, weight=20)
     */
    protected $relPath;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="scanned", type="datetime")
     * @Form\Field(type="datetime", options={"empty_value" = ""}, weight=30)
     */
    protected $scanned;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="processed", type="datetime", nullable=true)
     * @Form\Field(type="datetime", options={"empty_value" = ""}, weight=40)
     */
    protected $processed;
    
    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=30, nullable=true)
     * @Form\Field(type="media_category", options={"empty_value" = ""}, weight=24)
     */
    protected $category;

    /**
     * @var integer
     *
     * @ORM\Column(name="duration", type="integer", nullable=true)
     * @Form\Field(type="number", options={"label"="Duration (seconds)"}, weight=25)
     */
    protected $duration;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="releaseDate", type="date", nullable=true)
     * @Form\Field(type="birthday", options={"empty_value" = "", }, weight=80)
     */
    protected $releaseDate;

    /**
     * @var string
     *
     * @ORM\Column(name="videoCodec", type="string", length=30, nullable=true)
     */
    protected $videoCodec;
    
    /**
     * @var string
     *
     * @ORM\Column(name="audioCodec", type="string", length=30, nullable=true)
     */
    protected $audioCodec;
    
    /**
     * @ORM\ManyToOne(targetEntity="TvShow")
     * @ORM\JoinColumn(name="tvShowId", referencedColumnName="id")
     * @var Kyoushu\MediaBundle\Entity\TvShow
     * @Form\Field(type="tv_show", options={"empty_value"="", "label"="TV Show"}, weight=90)
     */
    protected $tvShow;

    /**
     * @var integer
     *
     * @ORM\Column(name="seasonNumber", type="integer", nullable=true)
     * @Form\Field(type="text", weight=100)
     */
    protected $seasonNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="episodeNumber", type="integer", nullable=true)
     * @Form\Field(type="text", weight=110)
     */
    protected $episodeNumber;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="width", type="integer", nullable=true)
     * @Form\Field(type="number", weight=120)
     */
    protected $width;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="height", type="integer", nullable=true)
     * @Form\Field(type="number", weight=130)
     */
    protected $height;
    
    /**
     * @var string
     *
     * @ORM\Column(name="screencapWebPath", type="string", length=255, nullable=true)
     * @Form\Field(type="text", weight=140)
     */
    protected $screencapWebPath;
    
    /**
     * @var \Kyoushu\MediaBundle\Entity\MediaEncodeJob
     * @ORM\OneToMany(targetEntity="MediaEncodeJob", mappedBy="sourceMedia")
     */
    protected $sourceEncodeJobs;
    
    /**
     * @var \Kyoushu\MediaBundle\Entity\MediaEncodeJob
     * @ORM\OneToMany(targetEntity="MediaEncodeJob", mappedBy="destinationMedia")
     */
    protected $destinationEncodeJobs;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sourceEncodeJobs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->destinationEncodeJobs = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Media
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
    
    public function getNameSlug(){
        return preg_replace(
            '/[^a-z0-9]+/i',
            '-',
            strtolower( $this->getName() )
        );
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
     * Get description
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set description
     * @param string $description
     * @return \Kyoushu\MediaBundle\Entity\Media
     */
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    
    /**
     * Set relPath
     *
     * @param string $relPath
     * @return Media
     */
    public function setRelPath($relPath)
    {
        $this->relPath = $relPath;

        return $this;
    }

    /**
     * Get relPath
     *
     * @return string 
     */
    public function getRelPath()
    {
        return $this->relPath;
    }
    
    public function getAbsPath(){
        return sprintf('%s/%s', $this->getSource()->getPath(), $this->getRelPath());
    }
    
    public function getWebPath(){
        
        return sprintf(
            '/media-sources/%s/%s',
            $this->getSource()->getSlug(),
            $this->getRelPath()
        );
        
    }
    
    /**
     * Extract filename from relPath
     * @return string
     */
    public function getFilename(){
        return basename($this->getRelPath());
    }
    
    public function getFileExtension(){
        $match = null;
        if(!preg_match(self::REGEX_FILE_EXTENSION, $this->getRelPath(), $match)){
            return null;
        }
        return $match['extension'];
    }

    /**
     * Set scanned
     *
     * @param \DateTime $scanned
     * @return Media
     */
    public function setScanned($scanned)
    {
        $this->scanned = $scanned;

        return $this;
    }

    /**
     * Get scanned
     *
     * @return \DateTime 
     */
    public function getScanned()
    {
        return $this->scanned;
    }

    /**
     * Set category
     *
     * @param string $category
     * @return Media
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     * @return Media
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer 
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set releaseDate
     *
     * @param \DateTime $releaseDate
     * @return Media
     */
    public function setReleaseDate($releaseDate)
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    /**
     * Get releaseDate
     *
     * @return \DateTime 
     */
    public function getReleaseDate()
    {
        return $this->releaseDate;
    }
    
    public function getReleaseYear(){
        $releaseDate = $this->getReleaseDate();
        if($releaseDate === null) return null;
        return $releaseDate->format('Y');
    }

    /**
     * Set seasonNumber
     *
     * @param integer $seasonNumber
     * @return Media
     */
    public function setSeasonNumber($seasonNumber)
    {
        $this->seasonNumber = $seasonNumber;

        return $this;
    }

    /**
     * Get seasonNumber
     *
     * @return integer 
     */
    public function getSeasonNumber($zeroFill = false, $zeroFillLength = 2)
    {
        if($this->seasonNumber === null) return null;
        if($zeroFill === true){
            return str_pad($this->seasonNumber, $zeroFillLength, '0', STR_PAD_LEFT);
        }
        return $this->seasonNumber;
    }

    /**
     * Set episodeNumber
     *
     * @param integer $episodeNumber
     * @return Media
     */
    public function setEpisodeNumber($episodeNumber)
    {
        $this->episodeNumber = $episodeNumber;

        return $this;
    }

    /**
     * Get episodeNumber
     *
     * @return integer 
     */
    public function getEpisodeNumber($zeroFill = false, $zeroFillLength = 2)
    {
        if($this->episodeNumber === null) return null;
        if($zeroFill === true){
            return str_pad($this->episodeNumber, $zeroFillLength, '0', STR_PAD_LEFT);
        }
        return $this->episodeNumber;
    }

    /**
     * Set source
     *
     * @param \Kyoushu\MediaBundle\Entity\MediaSource $source
     * @return Media
     */
    public function setSource(\Kyoushu\MediaBundle\Entity\MediaSource $source = null)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return \Kyoushu\MediaBundle\Entity\MediaSource 
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set processed
     *
     * @param \DateTime $processed
     * @return Media
     */
    public function setProcessed($processed)
    {
        $this->processed = $processed;

        return $this;
    }

    /**
     * Get processed
     *
     * @return \DateTime 
     */
    public function getProcessed()
    {
        return $this->processed;
    }

    /**
     * Set tvShow
     *
     * @param \Kyoushu\MediaBundle\Entity\TvShow $tvShow
     * @return Media
     */
    public function setTvShow(\Kyoushu\MediaBundle\Entity\TvShow $tvShow = null)
    {
        $this->tvShow = $tvShow;

        return $this;
    }

    /**
     * Get tvShow
     *
     * @return \Kyoushu\MediaBundle\Entity\TvShow 
     */
    public function getTvShow()
    {
        return $this->tvShow;
    }

    /**
     * Set width
     *
     * @param integer $width
     * @return Media
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return integer 
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param integer $height
     * @return Media
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return integer 
     */
    public function getHeight()
    {
        return $this->height;
    }
    

    /**
     * Set screencapWebPath
     *
     * @param string $screencapWebPath
     * @return Media
     */
    public function setScreencapWebPath($screencapWebPath)
    {
        $this->screencapWebPath = $screencapWebPath;

        return $this;
    }

    /**
     * Get screencapWebPath
     *
     * @return string 
     */
    public function getScreencapWebPath()
    {
        return $this->screencapWebPath;
    }
    
    /**
     * Alias for getScreencapWebPath
     * @return string
     */
    public function getScreencapPath(){
        return $this->getScreencapWebPath();
    }
    
    public function isAvailable(){
        return file_exists($this->getAbsPath());
    }
    
    /**
     * Get videoCodec
     * @return string
     */
    public function getVideoCodec() {
        return $this->videoCodec;
    }

    /**
     * Get audioCodec
     * @return string
     */
    public function getAudioCodec() {
        return $this->audioCodec;
    }

    /**
     * Set videoCodec
     * @param string $videoCodec
     * @return \Kyoushu\MediaBundle\Entity\Media
     */
    public function setVideoCodec($videoCodec) {
        $this->videoCodec = $videoCodec;
        return $this;
    }

    /**
     * set audioCodec
     * @param string $audioCodec
     * @return \Kyoushu\MediaBundle\Entity\Media
     */
    public function setAudioCodec($audioCodec) {
        $this->audioCodec = $audioCodec;
        return $this;
    }

        
    public function createRelPath($extension = null, $suffix = null){
        
        $category = $this->getCategory();
        $tvShow = $this->getTvShow();
        $seasonNumber = $this->getSeasonNumber();
        $tvShowName = ($tvShow ? $tvShow->getName() : null);
        
        $path = null;
        
        if($category === self::CATEGORY_TV){
            
            if(!$tvShowName) throw new \RuntimeException('Cannot create TV episode path without associated TV show');
            
            if($seasonNumber){
                $path = sprintf('Movies/Episodes/%s/Season %s', $tvShowName, $seasonNumber);
            }
            else{
                $path = sprintf('Movies/Episodes/%s', $tvShowName);
            }
            
        }
        elseif($category === self::CATEGORY_MOVIE){
            $path = 'Movies/Movies';
        }
        else{
            $path = 'Movies/Unknown';
        }
        
        $filename = $this->createFilename($extension, $suffix);
        return sprintf('%s/%s', $path, $filename);
        
    }
    
    public function createFilename($extension = null, $suffix = null){
        
        $category = $this->getCategory();
        $tvShow = $this->getTvShow();
        $tvShowName = ($tvShow ? $tvShow->getName() : null);
        $name = $this->getName();
        $seasonNumber = $this->getSeasonNumber(true);
        $episodeNumber = $this->getEpisodeNumber(true);
        $releaseDate = $this->getReleaseDate();
        
        $filename = null;
        
        if($category === self::CATEGORY_TV && $tvShow){
            
            if($seasonNumber !== null && $episodeNumber !== null){
                $filename = sprintf('%s - S%sE%s', $tvShowName, $seasonNumber, $episodeNumber);
            }
            elseif($releaseDate){
                $filename = sprintf('%s - %s', $tvShowName, $releaseDate->format('Y-m-d'));
            }
            
        }
        elseif($category === self::CATEGORY_MOVIE && $releaseDate && $name){
            $filename = sprintf('%s (%s)', $name, $releaseDate->format('Y'));
        }
        
        if($filename === null){
            $filename = preg_replace('/\.[^\.]+$/', '', $this->getFilename());
        }
        
        return $filename .
            ($suffix ? $suffix : '') .
            ($extension ? '.' . $extension : '');
        
    }
    
    public function getShortDescription(){
        
        $category = $this->getCategory();
        if(!$category) $category = self::CATEGORY_UNKNOWN;
        
        $source = $this->getSource();
        $sourceName = ($source ? $source->getName() : 'Unknown Source');
        
        return sprintf(
            '#%s: %s (%sp %s %s)',
            $this->getId(),
            $this->createFilename(),
            $this->getHeight(),
            $this->getVideoCodec(),
            $this->getAudioCodec()
        );
        
    }
    
    public function getFormChoiceGroup(){
        
        $category = $this->getCategory();
        
        if($category === self::CATEGORY_TV){
            $tvShow = $this->getTvShow();
            if($tvShow){
                return sprintf('TV: %s', $tvShow->getName());
            }
            else{
                return 'TV';
            }
        }
        elseif($category === self::CATEGORY_MOVIE){
            return 'Movies';
        }
        else{
            return 'Unknown';
        }
        
    }
    
    public function __toString(){
        return $this->getShortDescription();
    }

    /**
     * Add sourceEncodeJobs
     *
     * @param \Kyoushu\MediaBundle\Entity\MediaEncodeJob $sourceEncodeJobs
     * @return Media
     */
    public function addSourceEncodeJob(\Kyoushu\MediaBundle\Entity\MediaEncodeJob $sourceEncodeJobs)
    {
        $this->sourceEncodeJobs[] = $sourceEncodeJobs;
        $sourceEncodeJobs->setSourceMedia($this);
        return $this;
    }

    /**
     * Remove sourceEncodeJobs
     *
     * @param \Kyoushu\MediaBundle\Entity\MediaEncodeJob $sourceEncodeJobs
     */
    public function removeSourceEncodeJob(\Kyoushu\MediaBundle\Entity\MediaEncodeJob $sourceEncodeJobs)
    {
        $this->sourceEncodeJobs->removeElement($sourceEncodeJobs);
    }

    /**
     * Get sourceEncodeJobs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSourceEncodeJobs()
    {
        return $this->sourceEncodeJobs;
    }

    /**
     * Add destinationEncodeJobs
     *
     * @param \Kyoushu\MediaBundle\Entity\MediaEncodeJob $destinationEncodeJobs
     * @return Media
     */
    public function addDestinationEncodeJob(\Kyoushu\MediaBundle\Entity\MediaEncodeJob $destinationEncodeJobs)
    {
        $this->destinationEncodeJobs[] = $destinationEncodeJobs;

        return $this;
    }

    /**
     * Remove destinationEncodeJobs
     *
     * @param \Kyoushu\MediaBundle\Entity\MediaEncodeJob $destinationEncodeJobs
     */
    public function removeDestinationEncodeJob(\Kyoushu\MediaBundle\Entity\MediaEncodeJob $destinationEncodeJobs)
    {
        $this->destinationEncodeJobs->removeElement($destinationEncodeJobs);
    }

    /**
     * Get destinationEncodeJobs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDestinationEncodeJobs()
    {
        return $this->destinationEncodeJobs;
    }
}
