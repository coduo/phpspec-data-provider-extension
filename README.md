#PhpSpec data provider extension

[![Build Status](https://travis-ci.org/coduo/phpspec-data-provider-extension.svg?branch=master)](https://travis-ci.org/coduo/phpspec-data-provider-extension)

This extension allows you to create data providers for examples in specs.

## Installation

```shell
composer require coduo/phpspec-data-provider-extension
```

## Usage

Enable extension in phpspec.yml file

```
extensions:
  - Coduo\PhpSpec\DataProvider\DataProviderExtension
```

Write a spec:

```php
<?php

namespace spec\Coduo\ToString;

use PhpSpec\ObjectBehavior;

class StringSpec extends ObjectBehavior
{
    /**
     *  @dataProvider positiveConversionExamples
     */
    function it_convert_input_value_into_string($inputValue, $expectedValue)
    {
        $this->beConstructedWith($inputValue);
        $this->__toString()->shouldReturn($expectedValue);
    }

    public function positiveConversionExamples()
    {
        return array(
            array(1, '1'),
            array(1.1, '1.1'),
            array(new \DateTime, '\DateTime'),
            array(array('foo', 'bar'), 'Array(2)')
        );
    }
}
```

Write class for spec:

```php
<?php

namespace Coduo\ToString;

class String
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        $type = gettype($this->value);
        switch ($type) {
            case 'array':
                return sprintf('Array(%d)', count($this->value));
            case 'object':
                return sprintf("\\%s", get_class($this->value));
            default:
                return (string) $this->value;
        }
    }
}
```

Run php spec

```
$ console bin/phpspec run -f pretty
```

You should get following output:

```
Coduo\ToString\String

  12  ✔ convert input value into string
  12  ✔ 1) it convert input value into string
  12  ✔ 2) it convert input value into string
  12  ✔ 3) it convert input value into string
  12  ✔ 4) it convert input value into string


1 specs
5 examples (5 passed)
13ms
```
