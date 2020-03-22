<?php

namespace Krak\Schema\ProcessSchema\SymfonyConfig;

use Krak\Schema\Node;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeParentInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

function configTree(string $rootName, Node $dictNode): TreeBuilder {
    if ($dictNode->type() !== "dict") {
        throw new \RuntimeException('configTree expects a dict node for configuration. Received ' . $dictNode->type());
    }
    $treeBuilder = new TreeBuilder($rootName);
    configureNode($treeBuilder->getRootNode(), $dictNode);
    return $treeBuilder;
}

/** @internal */
function configureNode(NodeParentInterface $configNode, Node $node): void {
    if ($node->type() === "dict") {
        if ($configNode instanceof ArrayNodeDefinition) {
            $resNode = $configNode;
        } else if ($configNode instanceof NodeBuilder) {
            $resNode = $configNode->arrayNode($node->attribute('name'));
        } else {
            throw new \RuntimeException('Unexpected dict node type.');
        }

        /** @var ArrayNodeDefinition $configNode */
        foreach ($node->attribute('nodesByName', []) as $name => $childConfigNode) {
            configureNode($resNode->children(), $childConfigNode->withAddedAttributes(['name' => $name]));
        }
    } else if ($node->type() === "string" || $node->type() === "mixed") {
        /** @var NodeBuilder $configNode */
        $resNode = $configNode->scalarNode($node->attribute('name'));
    } else if ($node->type() === "int") {
        /** @var NodeBuilder $configNode */
        $resNode = $configNode->integerNode($node->attribute('name'));
    } else if ($node->type() === "float") {
        /** @var NodeBuilder $configNode */
        $resNode = $configNode->floatNode($node->attribute('name'));
    } else if ($node->type() === "bool") {
        /** @var NodeBuilder $configNode */
        $resNode = $configNode->booleanNode($node->attribute('name'));
    } else if ($node->type() === "list") {
        if ($configNode instanceof ArrayNodeDefinition) {
            $resNode = $configNode;
        } else if ($configNode instanceof NodeBuilder) {
            $resNode = $configNode->arrayNode($node->attribute('name'));
        } else {
            throw new \RuntimeException('Unexpected dict node type.');
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
    if ($node->type() === "dict") {
        configureNode($arrayNode->arrayPrototype(), $node);
    } else if ($node->type() === "string" || $node->type() === "mixed") {
        $arrayNode->scalarPrototype();
    } else if ($node->type() === "int") {
        $arrayNode->integerPrototype();
    } else if ($node->type() === "float") {
        $arrayNode->floatPrototype();
    } else if ($node->type() === "bool") {
        $arrayNode->booleanPrototype();
    } else if ($node->type() === "list") {
        configureNode($arrayNode->arrayPrototype(), $node);
    } else {
        throw new \RuntimeException('Unhandled node types.');
    }
}
