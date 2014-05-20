<?php

namespace Kyoushu\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * TvShowAlias
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class TvShowAlias
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * 
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128, unique=false)
     */
    private $slug;
    
    /**
     * @var 
     *
     * @ORM\ManyToOne(targetEntity="TvShow", inversedBy="aliases")
     * @ORM\JoinColumn(name="tvShowId", referencedColumnName="id")
     **/
    private $tvShow;


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
     * @return TvShowAlias
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
     * Set slug
     *
     * @param string $slug
     * @return TvShowAlias
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
     * Set tvShow
     *
     * @param \Kyoushu\MediaBundle\Entity\TvShow $tvShow
     * @return TvShowAlias
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
}
