<?php
    use \Thepozer\Log\SimpleLog as LogDateWriter;

    // ***** Fonctions de logs
    $oLog = new \Thepozer\Log\SimpleLog(fopen('logs/application-' . date('Y-m-d') . '.log', 'a+'), \Thepozer\Log\SimpleLog::ERROR);

    /**
        * Fonction de logging gloable - niveau debug
        * 
        * @global Object $oGlobalApp Objet Application Slim
        * @param String $sMessage Message à enregistrer
        */
    function debug ($sMessage) {
        global $oLog, $bDebugEcho;

        $oLog->debug($sMessage);
        if ($bDebugEcho) {
            echo "Debug : {$sMessage}\n";
        }
    }
    /**
        * Fonction de logging gloable - niveau error
        * 
        * @global Object $oGlobalApp Objet Application Slim
        * @param String $sMessage Message à enregistrer
        */
    function error ($sMessage) {
        global $oLog, $bDebugEcho;

        $oLog->error($sMessage);
        if ($bDebugEcho) {
            echo "Error : {$sMessage}\n";
        }
    }