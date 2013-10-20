<?php
Route::set('jpgraph', 'graph(/<controller>)')
    ->defaults(array(
        'directory' => 'Jpgraph',
        'action' => 'index'
    ));

spl_autoload_register(function($class) {
    switch($class) {
        case 'Graph':
            $jpgraph = 'jpgraph';
            break;
        case 'LinePlot':
            $jpgraph = 'jpgraph_line';
            break;
        case 'DateScale':
            $jpgraph = 'jpgraph_date';
            break;
        default:
            $jpgraph = '';
            break;
    }
    $file = realpath(dirname(__FILE__) . "/vendor/jpgraph/src/{$jpgraph}.php");
    if(file_exists($file)) {
        include_once $file;
        return true;
    }
    return false;
});