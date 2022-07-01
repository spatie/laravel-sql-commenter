<?php

use Spatie\SqlCommenter\SqlCommenter;

return [
    /*
     * Log the Laravel framework's version
     */
    'framework' => true,

    /*
     * Log which controller & action the query originated in
     * you can also enable logging of the full namespace
     * of the controller
     */
    'controller' => true,
    'controller_namespace' => false,

    /*
     * Log which route the query originated in
     */
    'route' => true,

    /*
     * Log which job the query originated in
     */
    'job' => true,
    'job_namespace' => false,

    /*
     * Log the db driver
     */
    'driver' => true,

    /*
     * Log the file and line number of the call
     */
    'file' => false,
    'backtrace_limit' => 20,

    /*
     * If you need fine-grained control over the logging, you can extend the
     * SqlCommenter class and specify your custom class here.
     */
    'commenter_class' => SqlCommenter::class,
];
