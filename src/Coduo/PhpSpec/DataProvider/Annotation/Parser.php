<?php

namespace Coduo\PhpSpec\DataProvider\Annotation;

class Parser
{
    const DATA_PROVIDER_PATTERN = '/@dataProvider ([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/';

    /**
     * @param \ReflectionMethod $reflection
     * @return bool
     */
    public function getDataProvider(\ReflectionMethod $reflection)
    {
        if (false === ($docComment = $reflection->getDocComment())) {
            return null;
        }

        if (0 === preg_match(self::DATA_PROVIDER_PATTERN, $docComment, $matches)) {
            return null;
        }

        return $matches[1];
    }
}
