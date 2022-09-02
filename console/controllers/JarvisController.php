<?php


namespace console\controllers;

use consik\yii2websocket\WebSocketServer;
use yii\console\Controller;
use Yii;

class JarvisController extends Controller
{
    private $currentPort = 8880;      //This port must be busy by WebServer and we handle an error

    public function init()
    {
        parent::init();

        $server = new WebSocketServer();
        $server->port = $this->currentPort;
        //$server->port = Yii::$app->devSet->getDevSet('swooleSocketPort');

        $server->on(WebSocketServer::EVENT_WEBSOCKET_OPEN_ERROR, function($e) use($server) {
            echo "Error opening port " . $server->port . "\n";
            $server->port += 1;     //Try next port to open
            $this->currentPort = $server->port;

            $server->start();
        });

        $server->on(WebSocketServer::EVENT_WEBSOCKET_OPEN, function($e) use($server) {
            echo "Server started at port " . $server->port . "\n";
            $server->stop();
        });

        $server->start();
    }


    public function actionConversation($port = null)
    {
        if($port === null) {
            echo 'Founded port: ' . $this->currentPort . "\n";
            $port = $this->currentPort;
        }

        $server = new ConversationServer();

        if ($port) {
            $server->port = $port;
            Yii::$app->devSet->setDevSet('swooleSocketPort', $port);
        }

        $server->start();
    }

}