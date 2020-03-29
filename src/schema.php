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

function listOf(Node $node, array $attributes = []): Node {
    return (new Node('list', ['node' => $node]))->withAddedAttributes($attributes);
}

function enum(array $values, array $attributes = []) {
    return (new Node('enum', ['values' => $values]))->withAddedAttributes($attributes);
}

/**
 * Represents an unbound homgoneous key/value pair collection
 * e.g. $usersByName = ['bob' => new User(), 'mary' => new User()]; // array<string, User>
 * Similar to a list but without indexed keys
 */
function dict(Node $node, array $attributes = []): Node {
    return (new Node('dict', ['node' => $node]))->withAddedAttributes($attributes);
}

/**
 * Represents an fixed heterogenous key/value pair collection
 * e.g. $user = ['name' => 'Bob', 'age' => 15] // array{name: string, age: int}
 * @param Node[] $nodes
 */
function struct(array $nodes, array $attributes = []): Node {
    return (new Node('struct', ['nodesByName' => $nodes]))->withAddedAttributes($attributes);
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
