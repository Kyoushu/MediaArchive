<?php

namespace Kyoushu\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction(){
        
        /* @var $finder \Kyoushu\MediaBundle\Finder\MediaFinder */
        $finder = $this->get('kyoushu_media.finder_factory')->createMediaFinder();
        
        $finder->sortByReleaseDate('DESC')->setLimit(12);
        
        $mediaSet = $finder->getResult();
        
        return $this->render('KyoushuMediaBundle:Default:index.html.twig', array(
            'media_set' => $mediaSet
        ));
        
    }
    
}
