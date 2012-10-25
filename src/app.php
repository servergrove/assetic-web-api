<?php

/*
 * This file is part of the assetic web api.
 *
 * (c) Pablo Godel <pablo@servergrove.com>
 * (c) Simone Fumagalli <simone@iliveinperego.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

$app->get('/', function() use($app) {
    return 'Assetic Web API';
}); 

$app->post('/coffeescript.{format}', function(Request $request) use($app) {
    $format = $app['request']->get('format');
    $content = $request->get('content');

    $cmd = "echo %s | /usr/local/bin/node /usr/local/bin/coffee -cps";

    $cmd = sprintf($cmd, escapeshellarg($content));

    $ll = exec($cmd, $output, $retval);

    if ($retval == 0) {
        $content = implode("", $output);
        $result = array(
            'result' => true,
            'content' => $content,
        );
    } else {
        error_log($retval.': '.$ll.' - '.print_r($output, true));
        $result = array(
            'result' => false,
            'err' => 'Command failed',
        );
    }

    switch($format) {
        case 'json':
            return new Response(
                json_encode($result), $result['result'] ? 200 : 500,
                array('Content-Type' => 'application/json')
            );
            break;
        default:
        case 'raw':
            return $content;
            break;
    }
});


$app->post('/yuicompressor.{format}', function(Request $request) use($app) {
    
    define ("YUI_CMD","java -jar ".__DIR__."/../java/yuicompressor-2.4.6.jar --type %s --charset %s %s");
    
    $format	= $app['request']->get('format');
    $type	= $request->get('type');    // JS or CSS compression
    $charset	= $request->get('charset');
    $content 	= $request->get('content');
    $client_uid	= $request->get('client_uid');
    $tmp_file_name = "/tmp/yc-".$client_uid;

    try {
        
        file_put_contents($tmp_file_name, $content);

        $cmd = sprintf(YUI_CMD, $type, escapeshellarg($charset), $tmp_file_name);

        $ll = exec($cmd, $output, $retval);
        
        syslog(LOG_ERR, time().$cmd);

        unlink('/tmp/yc-'-$client_uid);

        if ($retval !== 0)
            throw new \Exception('Command failed | '.$cmd);

        $result = array(
            'result' => true,
            'content' => implode("", $output),
        );
        
    } catch (\Exception $e) {
        
        syslog(LOG_ERR, time().$retval.': '.$ll.' - '.$e->getMessage());

        $result = array(
            'result' => false,
            'err' => $e->getMessage(),
        );
        
    }

    switch($format) {
        case 'json':
            return new Response(
                json_encode($result), 
                $result['result'] ? 200 : 500,
                array('Content-Type' => 'application/json')
            );
            break;
        default:
        case 'raw':
            return $content;
            break;
    }
        
});

return $app;
