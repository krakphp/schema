<?php

namespace Krak\Schema;

final class SchemasTest extends \PHPUnit\Framework\TestCase
{
    public function test_string() {
        $this->assertEquals('string', string()->type());
    }

    public function test_int() {
        $this->assertEquals('int', int()->type());
    }

    public function test_float() {
        $this->assertEquals('float', float()->type());
    }

    public function test_mixed() {
        $this->assertEquals('mixed', mixed()->type());
    }

    public function test_bool() {
        $this->assertEquals('bool', bool()->type());
    }

    public function test_list_of() {
        $node = listOf(string());
        $this->assertEquals('list', $node->type());
        $this->assertEquals('string', $node->attribute('node')->type());
    }

    public function test_dict() {
        $node = dict(['a' => string(), 'b' => int()]);
        $this->assertEquals('dict', $node->type());
        $this->assertEquals('string', $node->attribute('nodesByName')['a']->type());
        $this->assertEquals('int', $node->attribute('nodesByName')['b']->type());
    }

    public function test_tuple() {
        $node = tuple([string(), int()]);
        $this->assertEquals('tuple', $node->type());
        $this->assertEquals('string', $node->attribute('nodes')[0]->type());
        $this->assertEquals('int', $node->attribute('nodes')[1]->type());
    }

    public function test_optional() {
        $node = optional(string());
        $this->assertEquals('string', $node->type());
        $this->assertEquals(true, $node->attribute('optional'));
    }
}
