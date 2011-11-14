<?php

/*
 * This file is part of the assetic web api.
 *
 * (c) Pablo Godel <pablo@servergrove.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

require_once __DIR__.'/../silex.phar';

$app = require __DIR__.'/../src/app.php';
$app->run();
