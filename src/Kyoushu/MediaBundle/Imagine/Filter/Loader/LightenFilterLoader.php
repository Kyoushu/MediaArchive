<?php

namespace Kyoushu\MediaBundle\Imagine\Filter\Loader;

use Kyoushu\MediaBundle\Imagine\Filter\Luminosity\Lighten;
use Avalanche\Bundle\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;

class LightenFilterLoader implements LoaderInterface{
    
    public function load(array $options = array()) {
        $percentage = (isset($options['percentage']) ? (int)$options['percentage'] : 50);
        return new Lighten($percentage);
    }

}