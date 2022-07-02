<?php

return [
    /*
     * When set to true, comments will be added to all your queries
     */
    'enabled' => true,

    /*
     * These classes add comments to an executed query.
     */
    'commenters' => [
        new Spatie\SqlCommenter\Commenters\FrameworkVersionCommenter(),
        new Spatie\SqlCommenter\Commenters\ControllerCommenter(includeNamespace: false),
        new Spatie\SqlCommenter\Commenters\RouteCommenter(),
        new Spatie\SqlCommenter\Commenters\JobCommenter(includeNamespace: false),
        new Spatie\SqlCommenter\Commenters\DbDriverCommenter(),
        // new Spatie\SqlCommenter\Commenters\FileCommenter(backtraceLimit: 20),
    ],

    /*
     * If you need fine-grained control over the logging, you can extend
     * the SqlCommenter class and specify your custom class here
     */
    'commenter_class' => Spatie\SqlCommenter\SqlCommenter::class,
];
