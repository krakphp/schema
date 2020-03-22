<?php

namespace Krak\Schema;

function string(array $attributes = []): Node {
    return (new Node('string'))->withAddedAttributes($attributes);
}

function int(array $attributes = []): Node {
    return (new Node('int'))->withAddedAttributes($attributes);
}

function float(array $attributes = []): Node {
    return (new Node('float'))->withAddedAttributes($attributes);
}

function mixed(array $attributes = []): Node {
    return (new Node('mixed'))->withAddedAttributes($attributes);
}

function bool(array $attributes = []): Node {
    return (new Node('bool'))->withAddedAttributes($attributes);
}

function listOf(Node $node): Node { return new Node('list', ['node' => $node]); }

/** @param Node[] $nodes */
function dict(array $nodes, array $attributes = []): Node {
    return (new Node('dict', ['nodesByName' => $nodes]))->withAddedAttributes($attributes);
}

/**
 * @param Node[] $nodes
 * @psalm-param list<Node> $nodes
 */
function tuple(array $nodes, array $attributes = []): Node {
    return (new Node('tuple', ['nodes' => $nodes]))->withAddedAttributes($attributes);
}

function optional(Node $node): Node {
    return $node->withAddedAttributes(['optional' => true]);
}
