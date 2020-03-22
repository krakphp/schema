# Schema

The schema library provides the ability to define schemas with a declarative API and in turn using any of the processors to act on that schema.

We separate the concepts of the schema definition and the processors to allow us to build an AST describing a schema, and then allow different processors handle that structure for things like validation, generate a symfony config tree, building valid json schema, etc etc.

## Installation

Install with composer at `krak/schema`

## Usage

### Defining a Schema

```php
<?php
use function Krak\Schema\{dict, listOf, string, bool, int};

$schema = dict([
    'name' => string(),
    'isAdmin' => bool(),
    'age' => int(),
    'tags' => listOf(string()),
    'photos' => listOf(dict([
        'url' => string(),
        'width' => int(),
        'height' => int(),
    ]))
]);
```

### Validation (Coming Soon)

Eventually we'll support the ability to take a schema and validate array structures against them.

### Symfony Config Tree Processor

Declare and build symfony config tree builders declaratively with the `configTree` schema processor.

```php
<?php

use Symfony\Component\Config\Definition\{ConfigurationInterface, TreeBuilder};
use function Krak\Schema\ProcessSchema\SymfonyConfig\configTree;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder() {
        return configTree('aws', dict([
            'version' => string(),
            'region' => string(),
            'credentials' => dict([
                'key' => string(),
                'secret' => string(),
            ])
        ]));
    }
}
```

#### Comparison of Declarative vs Builder Syntax

Here's a seemingly simple config file that we'd want to validate the schema of:

```yaml
my_package:
  string_key: 'abc'
  int_key: 1
  dict_key:
    a: 1
    b: 2
  list_key: [1, 2, 3]
  list_of_dict_key:
    - a: 1
      b: 2
  dict_of_list:
    a: ['', '']
    b: [0, 0]
```

Here is the builder syntax:

```php
return (new TreeBuilder('my_package'))->getRootNode();
    ->children()
        ->scalarNode('string_key')->end()
        ->integerNode('int_key')->end()
        ->arrayNode('dict_key')
            ->children()
                ->scalarNode('a')->end()
                ->integerNode('b')->end()
            ->end()
        ->end()
        ->arrayNode('list_key')
            ->integerPrototype()->end()
        ->end()
        ->arrayNode('list_of_dict_key')
            ->arrayPrototype()
                ->children()
                    ->integerNode('a')->end()
                    ->integerNode('b')->end()
                ->end()
            ->end()
        ->end()
        ->arrayNode('dict_of_list_key')
            ->children()
                ->arrayNode('a')
                    ->scalarPrototype()->end()
                ->end()
                ->arrayNode('b')
                    ->integerPrototype()->end()
                ->end()
            ->end()
        ->end()
    ->end()
->end();
```

Here is the declarative syntax for the same definition:

```php
return configTree('my_package', dict([
    'string_key' => string(),
    'int_key' => int(),
    'dict_key' => dict([
        'a' => int(),
        'b' => int(),
    ]),
    'list_key' => listOf(int()),
    'list_of_dict_key' => listOf(dict([
        'a' => int(),
        'b' => int(),
    ])),
    'dict_of_list_key' => dict([
        'a' => listOf(string()),
        'b' => listOf(int()),
    ])
]));
```

#### References

Original RFC Pull Request to Symfony: https://github.com/symfony/symfony/issues/35127

## Documentation

No formal API documentation is setup, but the src dir is under 200loc at this point. Also the tests directory gives a good overview of the various features as well.

## Testing

Run `composer test` to run the test suite.

## Roadmap

- Api Documentation
- Additional schema fns to support more string/numeric constraints (regex, min, max, etc)
- JSON Schema ProcessSchema
  - Create the ability to export a schema definition to valid json schema json
- Validation ProcessSchema
  - Create a function validation library for basic schemas
  - Support custom validators and schema fns
