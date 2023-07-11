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
        Spatie\SqlCommenter\Commenters\ControllerCommenter::class => ['includeNamespace' => false],
        Spatie\SqlCommenter\Commenters\RouteCommenter::class,
        Spatie\SqlCommenter\Commenters\JobCommenter::class => ['includeNamespace' => false],
        Spatie\SqlCommenter\Commenters\FileCommenter::class => [
            'backtraceLimit' => 20,
            'excludePathSegments' => [],
            'useRelativePath' => false,
        ],
        Spatie\SqlCommenter\Commenters\CurrentUserCommenter::class,
        // Spatie\SqlCommenter\Commenters\FrameworkVersionCommenter::class,
        // Spatie\SqlCommenter\Commenters\DbDriverCommenter::class,
    ],

    /*
     * If you need fine-grained control over the logging, you can extend
     * the SqlCommenter class and specify your custom class here
     */
    'commenter_class' => Spatie\SqlCommenter\SqlCommenter::class,

    /**
     * The database connections for which you want to add comments.
     * Defaults to the default connection defined in config/database.php
     */
    'connections' => [
        // 'mysql',
    ],
];
