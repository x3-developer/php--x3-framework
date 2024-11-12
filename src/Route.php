<?php

namespace App;

/**
 * @Annotation
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Route
{
    /**
     * @param string $path
     * @param array $methods
     */
    public function __construct(
        public string $path,
        public array  $methods = ['GET']
    )
    {
    }
}
