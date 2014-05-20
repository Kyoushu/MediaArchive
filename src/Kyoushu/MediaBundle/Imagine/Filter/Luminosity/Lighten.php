<?php

namespace Kyoushu\MediaBundle\Imagine\Filter\Luminosity;

use Imagine\Filter\FilterInterface;
use Imagine\Image\Point;
use Imagine\Filter\Advanced\OnPixelBased;
use Imagine\Image\ImageInterface;
use Imagine\Image\Color;

class Lighten extends OnPixelBased implements FilterInterface{
    
    private $percentage;
    
    public function __construct($percentage){
        
        $multiplier = (0.01 * $percentage);
        
        parent::__construct(function (ImageInterface $image, Point $point) use ($multiplier){
            
            $color = $image->getColorAt($point);
            
            $red = $color->getRed() + ((255 - $color->getRed()) * $multiplier);
            $green = $color->getGreen() + ((255 - $color->getGreen()) * $multiplier);
            $blue = $color->getBlue() + ((255 - $color->getBlue()) * $multiplier);
            
            $image->draw()->dot($point, new Color(array(
                'red'   => (int)$red,
                'green' => (int)$green,
                'blue'  => (int)$blue
            )));
        });
        
    }
    
}
