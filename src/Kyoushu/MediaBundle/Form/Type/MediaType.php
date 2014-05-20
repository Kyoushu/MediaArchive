<?php

namespace Kyoushu\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
//use Doctrine\ORM\EntityRepository;
//use Symfony\Component\OptionsResolver\Options;
//use Kyoushu\MediaBundle\Entity\MediaEncodeJob;

class MediaType extends AbstractType
{
   
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        
        $resolver->setDefaults(array(
            'exclude_encoded' => false,
            'empty_value' => '',
            'class' => 'KyoushuMediaBundle:Media',
            'search_properties' => array('shortDescription'),
            'property' => 'shortDescription'
            //'group_by' => 'formChoiceGroup'
        ));
        
        /*$resolver->setNormalizers(array(
            'query_builder' => function (Options $options, $configs){
                return function (EntityRepository $repo) use ($options){
                    
                    $qb = $repo->createQueryBuilder('m')
                        ->innerJoin('m.source', 's')
                        ->orderBy('s.name', 'asc')
                        ->orderBy('m.releaseDate', 'asc');
                    
                    if($options['exclude_encoded']){
                        
                        $sourceOrx = $qb->expr()->orx();
                        $sourceOrx->add( $qb->expr()->isNull('sej.status') );
                        $sourceOrx->add( $qb->expr()->neq('sej.status', ':status_done') );
                        
                        $destinationOrx = $qb->expr()->orx();
                        $destinationOrx->add( $qb->expr()->isNull('dej.status') );
                        $destinationOrx->add( $qb->expr()->neq('dej.status', ':status_done') );
                        
                        $qb->leftJoin('m.sourceEncodeJobs', 'sej');
                        $qb->leftJoin('m.destinationEncodeJobs', 'dej');
                        
                        $qb->setParameter('status_done', MediaEncodeJob::STATUS_DONE);
                        
                        $qb->andWhere($sourceOrx);
                        $qb->andWhere($destinationOrx);
                        
                    }
                    
                    return $qb;
                    
                };
            },
        ));*/
        
    }
    
    public function getParent(){
        return 'entity_autocomplete';
    }
    
    public function getName() {
        return 'media';
    }
    
}