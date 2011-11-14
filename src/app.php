<?php

/*
 * This file is part of the assetic web api.
 *
 * (c) Pablo Godel <pablo@servergrove.com>
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

$app->post('/yuicompressor.{format}', function(Request $request) use($app) {
        $format = $app['request']->get('format');
        $in = $request->get('in');
        $out = $request->get('out');
        $charset = $request->get('charset');
        $content = $request->get('content');

        $cmd = "echo %s | java -jar /Users/pgodel/Sites/sgcontrol2.local/app/Resources/java/yuicompressor-2.4.6.jar --type %s --charset %s";

        if (substr($in, -3) == 'css') {
            $type = 'css';
        } elseif (substr($in, -2) == 'js') {
            $type = 'js';
        } else {
            $type = '';
        }

        $cmd = sprintf($cmd, escapeshellarg($content), $type, escapeshellarg($charset));

//        error_log($cmd);
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

return $app;