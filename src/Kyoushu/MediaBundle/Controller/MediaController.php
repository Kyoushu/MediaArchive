<?php

namespace Kyoushu\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kyoushu\MediaBundle\Entity\Media;

class MediaController extends Controller
{
    
    public function watchAction($mediaId){
        
        $media = $this->getDoctrine()->getManager()
            ->getRepository('KyoushuMediaBundle:Media')
            ->find($mediaId);
            
        if(!$media) throw $this->createNotFoundException('The specified media could not be found');
        
        if($media->getCategory() === Media::CATEGORY_TV){
            
            $url = $this->generateUrl('kyoushu_media_tvshow_watch', array(
                'tvShowSlug' => $media->getTvShow()->getSlug(),
                'mediaId' => $media->getId(),
                'mediaNameSlug' => $media->getNameSlug()
            ));
            
            return $this->redirect($url);
            
        }
        else{
            throw new \Exception(sprintf(
                'No controller available for %s media',
                $media->getCategory()
            ));
        }
        
    }
    
}