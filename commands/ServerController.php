<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\daemons\CommandsServer;
use consik\yii2websocket\WebSocketServer;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ServerController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionStart($port = null)
    {
        $server = new CommandsServer();
        if ($port) {
            $server->port = $port;
        }
        $server->on(WebSocketServer::EVENT_WEBSOCKET_OPEN, function($e) use($server) {
            echo "Server started at port " . $server->port;
        });

        // $server->on(WebSocketServer::EVENT_CLIENT_CONNECTED, function($e) use($server) {
        //     echo "Client connected <pre>" . print_r($e,true). '</pre>';
        // });

        // $server->on(WebSocketServer::EVENT_CLIENT_MESSAGE, function($e) use($server) {
        //     echo "Client message <pre>" . print_r($e,true). '</pre>';
        // });

        // $server->on(WebSocketServer::EVENT_CLIENT_RUN_COMMAND, function($e) use($server) {
        //     echo "Client run command  <pre>" . print_r($e,true). '</pre>';
        // });


        $server->start();
    }
}
