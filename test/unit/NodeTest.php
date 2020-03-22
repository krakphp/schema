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

    // public function test_configure() {
    //     $wasCalled = null;
    //     $node = (new Node('name'))->configure(function(...$args) use (&$wasCalled) {
    //         $wasCalled = $args;
    //     });

    //     $node->maybeConfigure(1, 2, 3);

    //     $this->assertEquals([1,2,3], $wasCalled);
    // }

    // public function test_configure_does_nothing_if_nothing_was_configured() {
    //     $node = new Node('name');
    //     $node->maybeConfigure(1, 2, 3);
    //     $this->assertTrue(true); // no errors occured :)
    // }
}
