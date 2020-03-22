<?php

namespace Krak\Schema;

final class Node {
    private $type;
    private $attributes;

    /**
     * @psalm-param array<string, mixed> $attributes
     */
    public function __construct(string $type, array $attributes = []) {
        $this->type = $type;
        $this->attributes = $attributes;
    }

    public function type(): string {
        return $this->type;
    }

    public function attributes(): array {
        return $this->attributes;
    }

    public function attribute(string $key) {
        return $this->attributes[$key] ?? null;
    }

    public function withAttributes(array $attributes): self {
        $self = clone $this;
        $self->attributes = $attributes;
        return $self;
    }

    public function withAddedAttributes(array $addedAttributes): self {
        return $this->withAttributes(array_merge($this->attributes, $addedAttributes));
    }
}
