<?php

namespace Kyoushu\MediaBundle\Table\Type;

use Kyoushu\MediaBundle\Table\Table;
use Kyoushu\MediaBundle\Table\Column\BasicColumn;
use Kyoushu\MediaBundle\Table\Column\DateTimeColumn;
use Kyoushu\MediaBundle\Table\Column\ControlColumn;

use Kyoushu\MediaBundle\Entity\Media;

class MediaFinderTvShowType extends Table{
    
    public function build(){
            
        $this->addColumn('show', new BasicColumn(
            'Show',
            'tvShow.name'
        ));
        
        $this->addColumn('season', new BasicColumn(
            'Season',
            'seasonNumber'
        ));
        
        $this->addColumn('episode', new BasicColumn(
            'Episode',
            'episodeNumber'
        ));
        
        $this->addColumn('name', new BasicColumn(
            'Episode Title',
            'name'
        ));
        
        $this->addColumn('releaseDate', new DateTimeColumn(
            'Air Date',
            'releaseDate'
        ));
        
        $this->addColumn('watch', new ControlColumn(
            'View',
            'kyoushu_media_tvshow_watch',
            function(Media $media){
                return array(
                    'tvShowSlug' => $media->getTvShow()->getSlug(),
                    'mediaId' => $media->getId(),
                    'mediaNameSlug' => $media->getNameSlug()
                );
            }
        ));
        
    }
    
}
