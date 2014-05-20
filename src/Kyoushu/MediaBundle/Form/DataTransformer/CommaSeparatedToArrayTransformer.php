<?php

namespace Kyoushu\MediaBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CommaSeparatedToArrayTransformer implements DataTransformerInterface
{
    
    public function transform($array)
    {
        if($array === null) return '';
        return implode(',', $array);
    }

    public function reverseTransform($commaSeparated)
    {
        if(!$commaSeparated) return null;
        return explode(',', $commaSeparated);
    }
}