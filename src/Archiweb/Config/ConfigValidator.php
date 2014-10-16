<?php

namespace Archiweb\Config;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class ConfigValidator implements ConfigurationInterface {

    /**
     * @var TreeBuilder
     */
    private $treeBuilder;

    /**
     * @var ArrayNodeDefinition
     */
    private $rootNode;

    function __construct () {

        $this->treeBuilder = new TreeBuilder();
        $this->rootNode = $this->treeBuilder->root('blog');

    }

    public function setConfigValidations() {

      /*  $this->rootNode
            ->children()
            ->scalarNode('title')
            ->isRequired()
            ->end()
            ->scalarNode('description')
            ->defaultValue('')
            ->end()
            ->booleanNode('rss')
            ->defaultValue(false)
            ->end()
            ->integerNode('posts_main_page')
            ->min(1)
            ->max(10)
            ->defaultValue(5)
            ->end()
            ->arrayNode('social')
            ->prototype('array')
            ->children()
            ->scalarNode('url')->end()
            ->scalarNode('icon')->end()
            ->end()
            ->end()
            ->end()
            ->end()
        ;

        $this->rootNode
            ->children()
            ->scalarNode('bli')->end()
            ->scalarNode('blu')->end()
            ->end()
        ;*/

    }

    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder() {

        return $this->treeBuilder;

    }


}

?>