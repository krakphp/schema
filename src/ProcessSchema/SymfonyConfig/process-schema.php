<?php

namespace Krak\Schema\ProcessSchema\SymfonyConfig;

use Krak\Schema\Node;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeParentInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

function configTree(string $rootName, Node $collectionNode): TreeBuilder {
    if (!$collectionNode->is(['list', 'dict', 'struct'])) {
        throw new \RuntimeException('configTree expects a collection node for configuration. Received ' . $collectionNode->type());
    }
    $treeBuilder = new TreeBuilder($rootName);
    // support symfony 4 and 5.
    $rootNode = method_exists($treeBuilder, 'root') ? $treeBuilder->root($rootName) : $treeBuilder->getRootNode();
    configureNode($rootNode, $collectionNode);
    return $treeBuilder;
}

/** @internal */
function configureNode(NodeParentInterface $configNode, Node $node): void {
    if ($node->is('struct')) {
        if ($configNode instanceof ArrayNodeDefinition) {
            $resNode = $configNode;
        } else if ($configNode instanceof NodeBuilder) {
            $resNode = $configNode->arrayNode($node->attribute('name'));
        } else {
            throw new \RuntimeException('Unexpected dict node type.');
        }

        if ($node->attribute('allowExtraKeys')) {
            $resNode->ignoreExtraKeys(false);
        }

        foreach ($node->attribute('nodesByName') ?? [] as $name => $childConfigNode) {
            configureNode($resNode->children(), $childConfigNode->withAddedAttributes(['name' => $name]));
        }
    } else if ($node->is(['string', 'mixed'])) {
        /** @var NodeBuilder $configNode */
        $resNode = $configNode->scalarNode($node->attribute('name'));
    } else if ($node->is("int")) {
        /** @var NodeBuilder $configNode */
        $resNode = $configNode->integerNode($node->attribute('name'));
    } else if ($node->is("float")) {
        /** @var NodeBuilder $configNode */
        $resNode = $configNode->floatNode($node->attribute('name'));
    } else if ($node->is("bool")) {
        /** @var NodeBuilder $configNode */
        $resNode = $configNode->booleanNode($node->attribute('name'));
    } else if ($node->is('enum')) {
        /** @var NodeBuilder $configNode */
        $resNode = $configNode->enumNode($node->attribute('name'));
        $resNode->values($node->attribute('values'));
    } else if ($node->is(['list', 'dict'])) {
        if ($configNode instanceof ArrayNodeDefinition) {
            $resNode = $configNode;
        } else if ($configNode instanceof NodeBuilder) {
            $resNode = $configNode->arrayNode($node->attribute('name'));
        } else {
            throw new \RuntimeException('Unexpected dict node type.');
        }
        /** @var ArrayNodeDefinition $resNode */
        if ($node->is('dict')) {
            $resNode->useAttributeAsKey($node->attribute('attribute_key') ?? 'key');
        }
        configureArrayNode($resNode, $node->attribute('node'));
    } else {
        throw new \RuntimeException('Unhandled node type: ' . $node->type());
    }

    $configure = $node->attribute('configure');
    if ($configure && is_callable($configure)) {
        $configure($resNode);
    }
}

/** @internal */
function configureArrayNode(ArrayNodeDefinition $arrayNode, Node $node): void {
    if ($node->is(['dict', 'struct', 'list'])) {
        configureNode($arrayNode->arrayPrototype(), $node);
    } else if ($node->is(['string', 'mixed'])) {
        $arrayNode->scalarPrototype();
    } else if ($node->is("int")) {
        $arrayNode->integerPrototype();
    } else if ($node->is("float")) {
        $arrayNode->floatPrototype();
    } else if ($node->is("bool")) {
        $arrayNode->booleanPrototype();
    } else if ($node->is('enum')) {
        $arrayNode->enumPrototype()->values($node->attribute('values'));
    } else {
        throw new \RuntimeException('Unhandled node types.');
    }
}
