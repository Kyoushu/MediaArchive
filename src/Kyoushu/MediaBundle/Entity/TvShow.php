<?php

namespace Kyoushu\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Kyoushu\MediaBundle\Entity\Media;
use Kyoushu\MediaBundle\Form\Mapping as Form;

/**
 * TvShow
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Kyoushu\MediaBundle\Entity\TvShowRepository")
 */
class TvShow
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
     * @Form\Field(type="text", weight=-100)
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
     * @ORM\Column(name="text", nullable=true)
     * @Form\Field(type="textarea", weight=-90)
     */
    protected $description;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="tvDbId", type="integer", nullable=true)
     * @Form\Field(type="text", options={"label"="TVDB ID"})
     */
    protected $tvDbId;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="processed", type="datetime", nullable=true)
     * @Form\Field(type="datetime")
     */
    protected $processed;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="Media", mappedBy="tvShow", cascade={"all"})
     * @ORM\OrderBy({"seasonNumber" = "ASC", "episodeNumber" = "ASC", "releaseDate" = "ASC"})
     */
    protected $episodes;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="TvShowAlias", mappedBy="tvShow", cascade={"all"})
     */
    protected $aliases;
    
    /**
     * @var string
     *
     * @ORM\Column(name="fanArtWebPath", type="string", length=255, nullable=true)
     * @Form\Field(type="text", weight=50)
     */
    protected $fanArtWebPath;
    
    /**
     * @var string
     *
     * @ORM\Column(name="posterWebPath", type="string", length=255, nullable=true)
     * @Form\Field(type="text", weight=60)
     */
    protected $posterWebPath;
    
    /**
     * @var string
     *
     * @ORM\Column(name="bannerWebPath", type="string", length=255, nullable=true)
     * @Form\Field(type="text", weight=70)
     */
    protected $bannerWebPath;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->episodes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->aliases = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return TvShow
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
     * Set processed
     *
     * @param \DateTime $processed
     * @return TvShow
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
     * Set slug
     *
     * @param string $slug
     * @return TvShow
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
     * Add episodes
     *
     * @param \Kyoushu\MediaBundle\Entity\Media $episodes
     * @return TvShow
     */
    public function addEpisode(\Kyoushu\MediaBundle\Entity\Media $episodes)
    {
        $this->episodes[] = $episodes;
        $episodes->setTvShow($this);
        return $this;
    }

    /**
     * Remove episodes
     *
     * @param \Kyoushu\MediaBundle\Entity\Media $episodes
     */
    public function removeEpisode(\Kyoushu\MediaBundle\Entity\Media $episodes)
    {
        $episodes->setTvShow(null);
        $this->episodes->removeElement($episodes);
    }

    /**
     * Get episodes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEpisodes()
    {
        return $this->episodes;
    }
    
    public function getEpisodesBySeason($seasonNumber){
        return $this->getEpisodes()->filter(function(Media $episode) use ($seasonNumber){
            return $episode->getSeasonNumber() == $seasonNumber;
        });
    }
    
    public function getAvailableSeasonNumbers(){
        $seasonNumbers = array();
        foreach($this->getEpisodes() as $episode){
            $seasonNumber = $episode->getSeasonNumber();
            if(in_array($seasonNumber, $seasonNumbers)) continue;
            $seasonNumbers[] = $seasonNumber;
        }
        return $seasonNumbers;
    }

    /**
     * Set tvDbId
     *
     * @param string $tvDbId
     * @return TvShow
     */
    public function setTvDbId($tvDbId)
    {
        $this->tvDbId = $tvDbId;

        return $this;
    }

    /**
     * Get tvDbId
     *
     * @return string 
     */
    public function getTvDbId()
    {
        return $this->tvDbId;
    }

    /**
     * Add aliases
     *
     * @param \Kyoushu\MediaBundle\Entity\TvShowAlias $aliases
     * @return TvShow
     */
    public function addAlias(\Kyoushu\MediaBundle\Entity\TvShowAlias $aliases)
    {
        $this->aliases[] = $aliases;
        $aliases->setTvShow($this);
        return $this;
    }

    /**
     * Remove aliases
     *
     * @param \Kyoushu\MediaBundle\Entity\TvShowAlias $aliases
     */
    public function removeAlias(\Kyoushu\MediaBundle\Entity\TvShowAlias $aliases)
    {
        $aliases->setTvShow(null);
        $this->aliases->removeElement($aliases);
    }

    /**
     * Get aliases
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return TvShow
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Get fanArtWebPath
     * @return string
     */
    public function getFanArtWebPath() {
        return $this->fanArtWebPath;
    }

    /**
     * Get posterWebpath
     * @return string
     */
    public function getPosterWebPath() {
        return $this->posterWebPath;
    }

    /**
     * Get bannerWebPath
     * @return string
     */
    public function getBannerWebPath() {
        return $this->bannerWebPath;
    }

    /**
     * Set fanArtWebPath
     * @param string $fanArtWebPath
     * @return \Kyoushu\MediaBundle\Entity\TvShow
     */
    public function setFanArtWebPath($fanArtWebPath) {
        $this->fanArtWebPath = $fanArtWebPath;
        return $this;
    }

    /**
     * Set posterWebPath
     * @param string $posterWebPath
     * @return \Kyoushu\MediaBundle\Entity\TvShow
     */
    public function setPosterWebPath($posterWebPath) {
        $this->posterWebPath = $posterWebPath;
        return $this;
    }

    /**
     * Set bannerWebPath
     * @param type $bannerWebPath
     * @return \Kyoushu\MediaBundle\Entity\TvShow
     */
    public function setBannerWebPath($bannerWebPath) {
        $this->bannerWebPath = $bannerWebPath;
        return $this;
    }

        
    public function __toString(){
        return $this->getName();
    }
    
}
