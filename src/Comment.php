<?php

namespace Spatie\SqlCommenter;

class Comment
{
    public function __construct(
        public string $key,
        public ?string $value,
    ) {
    }
}
