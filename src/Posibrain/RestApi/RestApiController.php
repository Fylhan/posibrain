<?php
namespace Posibrain\RestApi;

use Posibrain\TchatBot;
use Posibrain\Positroner;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

/**
 *
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @license LGPL-2.1+
 */
class RestApiController
{

    private $logger;

    private $loggerHandler;

    public function __construct()
    {
        $this->logger = new Logger('PosibrainRestApi');
        if (! is_dir(__DIR__ . '/../../../logs/')) {
            mkdir(__DIR__ . '/../../../logs/');
            chmod(__DIR__ . '/../../../logs/', '755');
        }
        $this->loggerHandler = new RotatingFileHandler(__DIR__ . '/../../../logs/restapi.log', 2, Logger::DEBUG);
        $this->logger->pushHandler($this->loggerHandler);
    }

    public function actionGetBots()
    {
        $response = array(
            'response' => false
        );
        try {
            $brainPath = __DIR__ . '/../../../app/brains/';
            $files = glob($brainPath . '*', GLOB_ONLYDIR);
            if (empty($files)) {
                return $this->render(array(
                    'response' => true,
                    'data' => '',
                    'message' => 'No bot avaible'
                ));
            }
            
            $response = array(
                'response' => true
            );
            $data = array();
            foreach ($files as $file) {
                $bot = array();
                $identityFile = $file . '/identity.json';
                if (is_file($identityFile) && (null != ($identity = loadJsonFile($identityFile, 'UTF-8')))) {
                    $bot['name'] = $identity->name;
                    $bot['pseudo'] = (! empty($identity->pseudo) ? $identity->pseudo : ucfirst($identity->name));
                    $bot['conceptorName'] = (! empty($identity->conceptorName) ? $identity->conceptorName : 'the Ancients');
                    $bot['birth'] = new \DateTime($identity->birthday);
                    $bot['timezone'] = @$identity->timezone;
                    $bot['desc'] = $bot['name'] . (! empty($identity->pseudo) ? ' alias ' . $identity->pseudo : '') . ': made the ' . $bot['birth']->format('jS \o\f F Y') . ' by ' . $bot['conceptorName'];
                }
                else {
                    $bot['name'] = $file;
                    $bot['message'] = 'No identity description for this bot...';
                }
                $data[] = $bot;
            }
            $response['data'] = $data;
        } catch (\Exception $e) {
            $response['message'] = 'Error during process: ' . $e->getMessage();
        }
        return $this->render($response);
    }

    public function actionGetPositrons()
    {
        $response = array(
            'response' => false
        );
        try {
            $positroner = new Positroner();
            $positrons = $positroner->listPositrons();
            $response = array(
                'response' => true,
                'data' => $positrons
            );
            if (empty($positrons)) {
                $response['message'] = 'No positron available';
            }
        } catch (\Exception $e) {
            $response['message'] = 'Error during process: ' . $e->getMessage();
        }
        return $this->render($response);
    }

    public function actionSubmit($username, $message, $date)
    {
        $response = array(
            'response' => false
        );
        try {
            $bot = new TchatBot('', '', array(
                'loggerHandler' => $this->loggerHandler
            ));
            $answer = $bot->generateAnswer($message, $username, $date);
            $data = array(
                'pseudo' => @$answer[1],
                'message' => @$answer[0]
            );
            $response = array(
                'response' => true,
                'data' => $data
            );
        } catch (\Exception $e) {
            $response['message'] = 'Error during process: ' . $e->getMessage();
        }
        return $this->render($response);
    }

    public function render($response)
    {
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Sat, 29 Oct 2011 00:00:00 GMT');
        header('Content-type: application/json');
        echo json_encode($response);
        return true;
    }
}