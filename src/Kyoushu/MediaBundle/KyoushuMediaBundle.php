<?php

namespace Kyoushu\MediaBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Kyoushu\MediaBundle\DependencyInjection\Compiler\EncoderCompilerPass;

class KyoushuMediaBundle extends Bundle
{
    
    public function build(ContainerBuilder $container) {
        parent::build($container);
        $container->addCompilerPass( new EncoderCompilerPass() );
    }
    
}
