<?php

namespace Krak\Schema;

final class NodeTest extends \PHPUnit\Framework\TestCase
{
    public function test_construction() {
        $node = new Node('name', ['a' => 'b']);
        $this->assertEquals('name', $node->type());
        $this->assertEquals('b', $node->attribute('a'));
    }

    public function test_added_attributes() {
        $node = (new Node('name', ['a' => 'b']))->withAddedAttributes([
            'b' => 'c',
        ]);
        $this->assertEquals(['a' => 'b', 'b' => 'c'], $node->attributes());
    }

    public function test_is_will_compare_on_strings() {
        $node = new Node('type');
        $this->assertEquals(true, $node->is('type'));
        $this->assertEquals(false, $node->is('not-type'));
    }

    public function test_is_will_compare_on_array() {
        $node = new Node('type');
        $this->assertEquals(true, $node->is(['type', 'not-type']));
        $this->assertEquals(false, $node->is(['not-type', 'not-type-2']));
    }

    public function test_is_will_fail_on_invalid_argument() {
        $this->expectException(\InvalidArgumentException::class);
        $node = new Node('type');
        $node->is(1);
    }
}
