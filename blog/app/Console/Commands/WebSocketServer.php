<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\Http\Controllers\WebSocketController;


class WebSocketServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initializing Websocket server to receive and manage connections';

    protected $wbController;

    /**
     * Create a new command instance.
     * WebSocketController
     *
     * @return void
     */
    public function __construct(WebSocketController $wbController)
    {
        $this->wbController = $wbController;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    $this->wbController
                )
            ),
            8090
        );
        $server->run();
    }
}
