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
    $max_log = 100;
    getLog()->error(sprintf('Error (%s) message: %s',  $ex->getCode(), $ex->getMessage()));
    getLog()->error($ex->getTraceAsString());
    $m = $ex->getMessage();
    if (strlen($m) > $max_log) {
        $m = substr($m, 0, $max_log) . '...';
    }
    $msg = sprintf('<h1>500 Internal Server Error</h1><h3>%s (%s)</h3>', $m, $ex->getCode());
    try {
        if (Flight::request()->ajax) {
            \Flight::json(['error'=>['message' => T($m), 'code'=>$ex->getCode()]]);
        } else {
            getView()->render('error/500', ['message'=>$m, 'code'=>$ex->getCode(), 'title'=>'Error']);
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
        getView()->render('error/404', ['title'=>'page not found']);
    }
});

/**
 * Other routes
 */
// test
Flight::route('/error', function() {
    throw new Exception('error test', 1);
});

Flight::route('/comp', function() {
    getAssets()->addLibrary(['icheck', 'bootstrap-switch', 'buttons', 'fa']);
    $steps = new \Components\Comp\StepView([['title'=>'step 1', 'descr'=>'step 1 descr'], ['title'=>'step 2', 'descr'=>'step 2 descr'], ['title'=>'step 3'], ['title'=>'step 4']]);
    $data['steps'] = $steps;
    $listview1 = new \Components\Comp\ListView();
    $data['listview1'] = $listview1;
    $listview2 = new \Components\Comp\ListView(2, 'right');
    $data['listview2'] = $listview2;
    $listview3 = new \Components\Comp\ListView(3, 'center');
    $data['listview3'] = $listview3;
    $v = getView();
    $v->render('components', $data);
});

