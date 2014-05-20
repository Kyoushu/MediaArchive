<?php

namespace Kyoushu\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TvShowController extends Controller
{
    
    public function indexAction($tvShowSlug, $seasonNumber){
        
        $show = $this->getDoctrine()->getManager()
                ->getRepository('KyoushuMediaBundle:TvShow')
                ->findOneBy(array('slug' => $tvShowSlug));
        
        if(!$show) throw $this->createNotFoundException('The specified TV show could not be found');
        
        /* @var $mediaFinder \Kyoushu\MediaBundle\Finder\MediaFinder */
        $mediaFinder = $this->get('kyoushu_media.finder_factory')->createMediaFinder();
        $mediaFinder->setTvShow($show);
        $mediaFinder->sortByReleaseDate('ASC');
        
        if($seasonNumber){
            $allEpisodes = $mediaFinder->getResult();
        }
        else{
            $mediaFinder->setLimit(1);
            $episodes = $mediaFinder->getResult();
            
            if(count($episodes) === 0) throw new $this->createNotFoundException('No episodes could be found for this TV show');
            
            $firstEpisode = reset($episodes);
            if($firstEpisode->getSeasonNumber()){
            
                $url = $this->generateUrl('kyoushu_media_tvshow', array(
                    'tvShowSlug' => $show->getSlug(),
                    'seasonNumber' => $firstEpisode->getSeasonNumber()
                ));

                return $this->redirect($url);
            }
        }
        
        $seasonNumbers = array();
        array_walk($allEpisodes, function($episode) use (&$seasonNumbers){
           $seasonNumber = $episode->getSeasonNumber();
           if(in_array($seasonNumber, $seasonNumbers)) return;
           $seasonNumbers[] = $seasonNumber;
        });
        sort($seasonNumbers);
        
        $episodes = array_filter($allEpisodes, function($episode) use ($seasonNumber){
            return $episode->getSeasonNumber() == $seasonNumber;
        });
        
        return $this->render('KyoushuMediaBundle:TvShow:index.html.twig', array(
            'show' => $show,
            'seasonNumber' => $seasonNumber,
            'episodes' => $episodes,
            'seasonNumbers' => $seasonNumbers,
            'seasonNumber' => $seasonNumber
        ));
        
    }
    
    public function watchAction($tvShowSlug, $mediaId){
        
        $episode = $this->getDoctrine()->getManager()
            ->getRepository('KyoushuMediaBundle:Media')
            ->createQueryBuilder('m')
            ->andWhere('m.id = :media_id')
            ->innerJoin('m.tvShow', 't')
            ->andWhere('t.slug = :tv_show_slug')
            ->setParameter('tv_show_slug', $tvShowSlug)
            ->setParameter('media_id', $mediaId)
            ->getQuery()
            ->getSingleResult();
            
        if(!$episode) throw $this->createNotFoundException('The specified episode could not be found');
        
        $allEpisodes = $this->get('kyoushu_media.finder_factory')
            ->createMediaFinder()
            ->setTvShow( $episode->getTvShow() )
            ->sortByReleaseDate('ASC')
            ->getResult();
        
        $prevEpisodes = array_filter($allEpisodes, function($otherEpisode) use ($episode){
            if($otherEpisode->getReleaseDate() < $episode->getReleaseDate()) return true;
            return false;
        });
        
        $nextEpisodes = array_filter($allEpisodes, function($otherEpisode) use ($episode){
            if($otherEpisode->getReleaseDate() > $episode->getReleaseDate()) return true;
            return false;
        });
        
        $moreEpisodes = array_merge(
            array_slice($prevEpisodes, -1, 1),
            array_slice($nextEpisodes, 0, 1)
        );
        
        return $this->render('KyoushuMediaBundle:TvShow:watch.html.twig', array(
            'episode' => $episode,
            'show' => $episode->getTvShow(),
            'more_episodes' => $moreEpisodes
        ));
        
    }
    
}