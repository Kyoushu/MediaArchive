<?php

namespace Kyoushu\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Common\Persistence\ObjectRepository;

class TvShowType extends AbstractType
{
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        
        $resolver->setDefaults(array(
            'empty_value' => '',
            'label' => 'TV Show',
            'class' => 'Kyoushu\MediaBundle\Entity\TvShow',
            'query_builder' => function(ObjectRepository $repo){
                return $repo->createQueryBuilder('s')
                    ->orderBy('s.name', 'ASC');
            }
        ));
        
    }
    
    public function getParent(){
        return 'entity';
    }
    
    public function getName() {
        return 'tv_show';
    }
    
}