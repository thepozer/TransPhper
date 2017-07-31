<?php
    /***** Configuration - Begin *****/
    $bDevMode = false;
    /*****  Configuration - End  *****/

    define('APP_DIR', dirname(__FILE__));
    chdir(APP_DIR);

    require_once 'vendor/autoload.php';
    require_once 'models/_logs.php';

    $oContainer = new \Slim\Container();

    $oContainer['view'] = function ($c) {
        return new \Thepozer\View\SimpleView('views/');
    };

    $oContainer['log'] = $oLog;

    if ($bDevMode) {
        $oContainer['log']->setLogLevel(\Thepozer\Log\SimpleLog::DEBUG);
        $oContainer['settings']['displayErrorDetails'] = true;
    }

    $oGlobalApp = new \Slim\App($oContainer);

    require_once('controllers/main.php');

    $oGlobalApp->run();
