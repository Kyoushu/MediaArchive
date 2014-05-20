<?php

namespace Kyoushu\MediaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kyoushu_media');
        
        $rootNode
            ->children()
                ->scalarNode('web_root_dir')->isRequired()->end()
                ->scalarNode('screencap_root_dir')->isRequired()->end()
                ->integerNode('screencap_offset')->isRequired()->end()
                ->arrayNode('admin')->isRequired()
                    ->children()
                        ->arrayNode('entities')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('class')->isRequired()->end()
                                    ->scalarNode('list_table_class')->defaultNull()->end()
                                    ->arrayNode('list_context_forms')
                                        ->prototype('array')
                                            ->children()
                                                ->scalarNode('route')->isRequired()->end()
                                                ->scalarNode('button_label')->isRequired()->end()
                                                ->scalarNode('form_class')->isRequired()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                    ->arrayNode('list_finder_filters')
                                        ->prototype('array')
                                            ->children()
                                                ->scalarNode('class')->isRequired()->end()
                                                ->scalarNode('property')->isRequired()->end()
                                                ->scalarNode('form_type')->defaultValue('text')->end()
                                                ->arrayNode('form_options')
                                                    ->prototype('variable')->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                    ->scalarNode('list_view')->defaultNull()->end()
                                    ->scalarNode('name_property')->isRequired()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('symlink_assets')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('source')->isRequired()->end()
                            ->scalarNode('destination')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('encoder')->isRequired()
                    ->children()
                        ->scalarNode('temp_dir')->isRequired()->end()
                        ->scalarNode('default_encoder')->isRequired()->end()
                        ->arrayNode('profiles')
                            ->isRequired()
                            ->requiresAtLeastOneElement()
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('container')->isRequired()->end()
                                    ->scalarNode('video_codec')->isRequired()->end()
                                    ->scalarNode('audio_codec')->isRequired()->end()
                                    ->scalarNode('audio_bitrate')->defaultNull()->end()
                                    ->scalarNode('video_bitrate')->defaultNull()->end()
                                    ->integerNode('max_height')->defaultNull()->end()
                                ->end()
                            ->end()
                        ->end()
                        
                    ->end()
                ->end()
            ->end()
        ;

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
