<?php
    use \Interop\Container\ContainerInterface;
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    class MainController {
        protected $oContainer = null;
        
        private $sAuthorizedChar = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        private $iAuthCharMod    = 62;

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

        private function generateRandom(int $iLen) {
            $sRandomBytes = random_bytes($iLen);
            $sRandom = '';
            for($i = 0; $i < $iLen; $i++) {
                debug("Index : '{$i}' - Char : '" . bin2hex($sRandomBytes[$i]) . "' - Char Modulo : '" . dechex(ord($sRandomBytes[$i]) % $this->iAuthCharMod) . "'");
                $sRandom .= $this->sAuthorizedChar[(ord($sRandomBytes[$i]) % $this->iAuthCharMod)];
            }
            
            return $sRandom;
        }
        
        private function generatePublicUrl($sFileName) {
            $sUrl = ($_SERVER['HTTPS']) ? 'https' : 'http' . '://' . $_SERVER['SERVER_NAME'] . '/' . $sFileName ;
            
            return $sUrl;
        }
            
        public function upload(Request $oRequest, Response $oResponse, $arArgs) {
            $sFileName = $arArgs['filename'];
            
            do {
                $sDir      = $this->generateRandom(8);
                $sLocalDir = './data/' . $sDir;
                
                $sPath  = $sDir . '/' . $sFileName;
                $sLocalPath = $sLocalDir . '/' . $sFileName;
            } while(file_exists($sLocalPath));
            
            mkdir($sLocalDir, 0777, true);
            file_put_contents($sLocalPath, $oRequest->getBody());
            
            $sPublicUrl = $this->generatePublicUrl($sPath);
            $oBody = $oResponse->getBody();
            $oBody->write('Download URL : ' . $sPublicUrl . PHP_EOL);
            return $oResponse->withHeader('Content-type', 'text/plain');
        }
        
        public function download(Request $oRequest, Response $oResponse, $arArgs) {
            $sFile = './data/' . $arArgs['dir'] . '/' . $arArgs['filename'];
            $fh = fopen($sFile, 'rb');

            $oStream = new \Slim\Http\Stream($fh); // create a stream instance for the response body

            return $oResponse->withHeader('Content-Type', 'application/force-download')
                             ->withHeader('Content-Type', 'application/octet-stream')
                             ->withHeader('Content-Type', 'application/download')
                             ->withHeader('Content-Description', 'File Transfer')
                             ->withHeader('Content-Transfer-Encoding', 'binary')
                             ->withHeader('Content-Disposition', 'attachment; filename="' . $sFileName . '"')
                             ->withHeader('Expires', '0')
                             ->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
                             ->withHeader('Pragma', 'public')
                             ->withBody($oStream); // all stream contents will be sent to the response
        }
    }

    if ($oGlobalApp) {
        $oGlobalApp->get('/', \MainController::class . ':home');
        $oGlobalApp->put('/{filename}', \MainController::class . ':upload');
        $oGlobalApp->get('/{dir}/{filename}', \MainController::class . ':download');
    }