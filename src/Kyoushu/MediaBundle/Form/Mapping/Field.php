<?php

namespace Kyoushu\MediaBundle\Form\Mapping;

/**
 * @Annotation
 */
class Field
{
    
    /**
     * @var string
     */
    public $type;

    /**
     * @var array
     */
    public $options = array();
    
    /**
     *
     * @var integer
     */
    public $weight = 0;

}
