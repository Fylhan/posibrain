<?php
namespace Posibrain\RestApi;

use Posibrain\TchatBot;
use Posibrain\Positroner;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 *
 * @author Fylhan (http://fylhan.la-bnbox.fr)
 * @license LGPL-2.1+
 */
class RestApiController
{
    public function actionGetBots(Request $request)
    {
        $response = array(
            'status' => false
        );
        try {
            $brainPath = __DIR__ . '/../../../app/brains/';
            $files = glob($brainPath . '*', GLOB_ONLYDIR);
            if (empty($files)) {
                return $this->render(array(
                    'status' => true,
                    'data' => '',
                    'message' => 'No bot avaible'
                ));
            }
            
            $response = array(
                'status' => true
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

    public function actionGetPositrons(Request $request)
    {
        $response = array(
            'status' => false
        );
        try {
            $positroner = new Positroner();
            $positrons = $positroner->listPositrons();
            $response = array(
                'status' => true,
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

    public function actionSubmit(Request $request, $botId, $botLang)
    {
        if (!$request->query->has('msg')) {
            return $this->render(array('status' => false, 'message' => 'The field "msg" is required. You need to ask a question to the bot!'));
        }
        $response = array(
            'status' => false
        );
        try {
            $pseudo = $request->query->get('pseudo', 'Anonymous');
            $message = $request->query->get('msg');
            $logger = new Logger('PosibrainRestApi');
            $loggerHandler = new RotatingFileHandler(__DIR__ . '/../../../logs/restapi.log', 2, Logger::DEBUG);
            $logger->pushHandler($loggerHandler);
            $bot = new TchatBot($botId, $botLang, array('loggerHandler' => $loggerHandler));
            $answer = $bot->generateAnswer($message, $pseudo, time());
            $data = array(
                'pseudo' => @$answer[1],
                'message' => @$answer[0]
            );
            $response = array(
                'status' => true,
                'data' => $data
            );
        } catch (\Exception $e) {
            $response['message'] = 'Error during process: ' . $e->getMessage();
        }
        return $this->render($response);
    }

    public function render($response)
    {
        $header = array(
            'Cache-Control' => 'no-cache, must-revalidate',
            'Expires' => 'Sat, 29 Oct 2011 00:00:00 GMT',
            'Content-type' => 'application/json'
        );
        return new JsonResponse($response, 200, $header);
    }
}