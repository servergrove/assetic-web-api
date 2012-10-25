#!/usr/bin/env php
<?php

/*
 * This is the old client that now probably doesn't work anymore
 * 
 * You can use this Symfony2 Bundle | https://github.com/hpatoio/AsseticApiClientBundle 
 * that add an assetic filter that provide the API to connect 
 * to the new version of the application
 * 
 */


/*
 * This file is part of the assetic web api.
 *
 * (c) Pablo Godel <pablo@servergrove.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

function sendRequest($vars, $url)
{

    $ch = curl_init($url);
    $encoded = http_build_query($vars);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POSTFIELDS,  $encoded);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    
    syslog(LOG_ERR, "CURL - ".curl_error($ch));
    
    curl_close($ch);

    if (!$output) return false;

    $decoded = json_decode($output);
    
    syslog(LOG_ERR, "JSON -- ". print_r($decoded, true));

    if (!$decoded) return false;

    if ($decoded->result) return $decoded->content;

    return false;
    
}

//mail('pablo@servergrove.com', 'assetic', 'assetic: '.print_r($_SERVER['argv'], true));
if (strpos($_SERVER['argv'][1], '-cp') !== false) {
    $content = file_get_contents($_SERVER['argv'][2]);

    $url = "http://assetic.servergrove.com/coffeescript.json";

    $vars = array(
      'content' => $content,
    );

    if (null === $result = sendRequest($vars, $url)) {
        $result = $content;
    }

    echo $result;
    die(0);
}

syslog(LOG_ERR, "Start -- ". print_r($_SERVER['argv'], true));

if (strpos($_SERVER['argv'][2], 'yuicompressor') !== false) {
    
    $url = "http://assetic.servergrove.com/yuicompressor.json";
    $content = file_get_contents($_SERVER['argv'][9]);
        
    try {

        $vars = array(
          'charset' =>   $_SERVER['argv'][4],
          'in' =>   $_SERVER['argv'][9],
          'out' =>  $_SERVER['argv'][6],
          'type' =>   $_SERVER['argv'][8],
          'content' => $content,
        );

        $result = sendRequest($vars, $url);
        
        syslog(LOG_ERR, var_dump($result, true));

        if ($result === false)
            throw new \Exception('Problem calling web service | '.$result['err']);

    } catch (\Exception $e) {

        syslog(LOG_ERR, "ERRORE | ".$e->getMessage());
        $result = $content;

    }
        
    file_put_contents($_SERVER['argv'][6], $result);
    die(0);
}