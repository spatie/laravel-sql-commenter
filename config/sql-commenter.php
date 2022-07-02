<?php

use Spatie\SqlCommenter\Commenters\ControllerCommenter;
use Spatie\SqlCommenter\Commenters\DbDriverCommenter;
use Spatie\SqlCommenter\Commenters\FrameworkVersionCommenter;
use Spatie\SqlCommenter\Commenters\JobCommenter;
use Spatie\SqlCommenter\Commenters\RouteCommenter;
use Spatie\SqlCommenter\SqlCommenter;

return [
    /*
     * These classes add comments to an executed query.
     */
    'commenters' => [
        new FrameworkVersionCommenter(),
        new ControllerCommenter(includeNamespace: false),
        new RouteCommenter(),
        new JobCommenter(includeNamespace: false),
        new DbDriverCommenter(),
        // new FileCommenter(backtraceLimit: 20),
    ],

    /*
     * If you need fine-grained control over the logging, you can extend
     * the SqlCommenter class and specify your custom class here
     */
    'commenter_class' => SqlCommenter::class,
];
