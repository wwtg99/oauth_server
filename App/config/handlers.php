<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/12/10
 * Time: 16:21
 */

/**
 * Other configurations before Flight starts.
 */

$logger = getLog();
// debug bar
if (isDebug()) {
    $debugbar = new \DebugBar\StandardDebugBar();
    if ($logger instanceof \Flight2wwu\Component\Log\Monolog) {
        $collector = new \DebugBar\Bridge\MonologCollector($logger->getLogger('main'));
        foreach ($logger->getLogger(null) as $name => $l) {
            if ($name != 'main') {
                $collector->addLogger($l);
            }
        }
        $debugbar['messages']->aggregate($collector);
    }
    Flight::set('debugbar', $debugbar);
}

// handle error
\Flight::map('error', function(\Exception $ex) {
    getLog()->error(sprintf('Error (%s) message: %s',  $ex->getCode(), $ex->getMessage()));
    getLog()->error($ex->getTraceAsString());
    $m = $ex->getMessage();
    if (strlen($m) > 50) {
        $m = substr($m, 0, 50) . '...';
    }
    $msg = sprintf('<h1>500 Internal Server Error</h1><h3>%s (%s)</h3>', $m, $ex->getCode());
    try {
        if (Flight::request()->ajax) {
            \Flight::json(['error'=>['message' => T($m), 'code'=>$ex->getCode()]]);
        } else {
            getView()->render('error/500', ['message'=>T($m), 'code'=>$ex->getCode()]);
        }
    } catch (\Exception $e) {
        exit($msg);
    }
});

// not found
\Flight::map('notFound', function() {
    if (Flight::request()->ajax) {
        Flight::json(['error'=>['message'=>'page not found', 'code'=>404]]);
    } else {
        getView()->render('error/404');
    }
});