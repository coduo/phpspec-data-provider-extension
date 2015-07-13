<?php

namespace spec\Coduo\PhpSpec\DataProvider\Annotation;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ParserSpec extends ObjectBehavior
{
    function it_return_data_provider_method_name(\ReflectionMethod $reflectionMethod)
    {
        $reflectionMethod->getDocComment()->willReturn(<<<ANNOTATION
/**
 * @dataProvider positiveExample
 */
ANNOTATION
        );

        $this->getDataProvider($reflectionMethod)->shouldReturn('positiveExample');
    }
}
