<?php

namespace Krak\Schema;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Dumper\YamlReferenceDumper;
use Symfony\Component\Config\Definition\NodeInterface;
use function Krak\Schema\ProcessSchema\SymfonyConfig\configTree;

final class SymfonyConfigTest extends \PHPUnit\Framework\TestCase
{
    /** @dataProvider provide_config_trees */
    public function test_processing_of_schema_to_config_tree(TreeBuilder $expected, TreeBuilder $actual) {
        $dumper = new YamlReferenceDumper();
        $this->assertEquals(
            $dumper->dumpNode($expected->buildTree()),
            $dumper->dumpNode($actual->buildTree())
        );
    }

    public function provide_config_trees() {
        yield 'empty tree' => [
            (new TreeBuilder('root')),
            configTree('root', dict([])),
        ];

        yield 'tree with atomic values' => [
            (new TreeBuilder('root'))->getRootNode()
                ->children()
                    ->scalarNode('string')->end()
                    ->integerNode('int')->end()
                    ->booleanNode('bool')->end()
                    ->floatNode('float')->end()
                    ->scalarNode('mixed')->end()
                ->end()
            ->end(),
            configTree('root', dict([
                'string' => string(),
                'int' => int(),
                'bool' => bool(),
                'float' => float(),
                'mixed' => mixed(),
            ])),
        ];

        yield 'tree with nested dicts' => [
            (new TreeBuilder('root'))->getRootNode()
                ->children()
                    ->arrayNode('dict')
                        ->children()
                            ->arrayNode('nested')
                                ->children()
                                    ->integerNode('int')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end(),
            configTree('root', dict([
                'dict' => dict([
                    'nested' => dict([
                        'int' => int(),
                    ])
                ])
            ])),
        ];

        yield 'tree with lists' => [
            (new TreeBuilder('root'))->getRootNode()
                ->children()
                    ->arrayNode('dict_of_list')
                        ->children()
                            ->arrayNode('strings')
                                ->scalarPrototype()->end()
                            ->end()
                            ->arrayNode('ints')
                                ->integerPrototype()->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('list_of_dict')
                        ->arrayPrototype()
                            ->children()
                                ->scalarNode('string')->end()
                                ->integerNode('int')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end(),
            configTree('root', dict([
                'dict_of_list' => dict([
                    'strings' => listOf(string()),
                    'ints' => listOf(int()),
                ]),
                'list_of_dict' => listOf(dict([
                    'string' => string(),
                    'int' => int(),
                ])),
            ])),
        ];

        yield 'tree with ignoreExtraKeys' => [
            (new TreeBuilder('root'))->getRootNode()
                ->ignoreExtraKeys()
                ->children()
                    ->integerNode('int')->end()
                ->end()
            ->end(),
            configTree('root', dict([
                'int' => int(),
            ], [ 'configure' => function($def) {  $def->ignoreExtraKeys(); } ]))
        ];

        yield 'tree with configured array nodes' => [
            (new TreeBuilder('root'))->getRootNode()
                ->children()
                    ->scalarNode('string')->end()
                    ->integerNode('int')->end()
                ->end()
            ->end(),
            configTree('root', dict([
                'string' => string(),
            ], [
                'configure' => function(ArrayNodeDefinition $def) {
                    $def->children()
                        ->integerNode('int')->end()
                    ->end();
                }
            ])),
        ];
    }
}
