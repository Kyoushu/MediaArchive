<?php

namespace Kyoushu\MediaBundle\MediaEncoder;

interface EncoderInterface {
    
    public function encode($sourcePath, $destinationPath, Profile $profile);
    
    public function getName();
    
}
