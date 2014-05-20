<?php

namespace Kyoushu\MediaBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KyoushuMediaExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        
        $symlinkInstallerDefinition = new Definition('Kyoushu\MediaBundle\Asset\SymlinkInstaller');
        $symlinkInstallerDefinition->setArguments(array( $config['symlink_assets'] ));
        $container->addDefinitions(array(
            'kyoushu_media.asset.symlink_installer' => $symlinkInstallerDefinition
        ));
        
        $processorServiceDefinition = $container->getDefinition('kyoushu_media.processor');
        $processorServiceDefinition->addMethodCall('setWebRootDir', array( $config['web_root_dir'] ));
        $processorServiceDefinition->addMethodCall('setScreencapRootDir', array( $config['screencap_root_dir'] ));
        $processorServiceDefinition->addMethodCall('setScreencapOffset', array( $config['screencap_offset'] ));
        
        $adminEntityRegistryDefinition = $container->getDefinition('kyoushu_media.admin.entity_registry');
        $entityDefinitions = array();
        foreach($config['admin']['entities'] as $name => $entityConfig){
            
            $entityDefinitionName = sprintf('kyoushu_media.admin.entity_definition.%s', $name);
            $entityDefinitions[$entityDefinitionName] = new Definition('Kyoushu\MediaBundle\Admin\EntityDefinition');
            $entityDefinitions[$entityDefinitionName]->setArguments(array($name, $entityConfig));
            
            $adminEntityRegistryDefinition->addMethodCall('addDefinition', array(
                new Reference($entityDefinitionName)
            ));
            
        }
        $container->addDefinitions($entityDefinitions);
        
        $encoderManagerServiceDefinition = $container->getDefinition('kyoushu_media.encoder_manager');
        $encoderManagerServiceDefinition->addMethodCall('setDefaultEncoderAlias', array( $config['encoder']['default_encoder'] ));
        $encoderManagerServiceDefinition->addMethodCall('setTempDir', array( $config['encoder']['temp_dir'] ));
        $profileDefinitions = array();
        foreach($config['encoder']['profiles'] as $profileName => $profileConfig){
            
            $profileDefinitionName = sprintf('kyoushu_media.encoder_profile.%s', $profileName);
            $profileDefinitions[$profileDefinitionName] = new Definition('Kyoushu\MediaBundle\MediaEncoder\Profile');
            $profileDefinitions[$profileDefinitionName]->setArguments(array($profileName, $profileConfig));
            $profileDefinitions[$profileDefinitionName]->addTag('kyoushu_media.encoder_profile', array(
                'alias' => $profileName
            ));
            
            $encoderManagerServiceDefinition->addMethodCall('addProfile', array(
                new Reference($profileDefinitionName),
                $profileName
            ));
            
        }        
        $container->addDefinitions($profileDefinitions);
        
    }
}
