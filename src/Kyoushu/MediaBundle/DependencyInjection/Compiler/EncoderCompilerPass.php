<?php

namespace Kyoushu\MediaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class EncoderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        
        $managerServiceDefinition = $container->getDefinition('kyoushu_media.encoder_manager');
        
        $encoderServices = $container->findTaggedServiceIds('kyoushu_media.encoder');
        
        foreach ($encoderServices as $id => $attributes) {
            $managerServiceDefinition->addMethodCall(
                'addEncoder',
                array(
                    new Reference($id),
                    $attributes[0]['alias']
                )
            );
        }
        
    }
}