<?php
    /***** Configuration - Begin *****/
    $bDevMode = true;
    /*****  Configuration - End  *****/

    use \Interop\Container\ContainerInterface;
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    require 'vendor/autoload.php';

    class MainController {
        protected $oContainer;

        public function __construct(ContainerInterface $oContainer) {
            $this->oContainer = $oContainer;
        }
        
        public function home(Request $oRequest, Response $oResponse, $arArgs) {
            // your code
            // to access items in the container... $this->oContainer->get('');
            
            $oBody = $oResponse->getBody();
            $oBody->write('Hello');
            return $oResponse->withHeader('Content-type', 'text/plain');
        }
    }
    
    $oContainer = new \Slim\Container();

    $oContainer['view'] = function ($c) {
        return new \Thepozer\View\SimpleView('views/');
    };

    $oContainer['log'] = new \Thepozer\Log\SimpleLog(fopen('logs/application-' .date('Y-m-d') . '.log', 'a+'), \Thepozer\Log\SimpleLog::ERROR);

    if ($bDevMode) {
        $oContainer['log']->setLogLevel(\Thepozer\Log\SimpleLog::DEBUG);
        $oContainer['settings']['displayErrorDetails'] = true;
    }

    $oGlobalApp = new \Slim\App($oContainer);

    $oGlobalApp->get('/', \MainController::class . ':home');

    $oGlobalApp->run();
