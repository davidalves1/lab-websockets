<?php

require __DIR__ . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class ChatServer implements MessageComponentInterface
{
    const PORT = 4242;

	protected $clients;

    protected $port = self::PORT;

    public function __construct() {
        echo 'Server ONLINE na porta ', $this->port;
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ID: {$conn->resourceId}\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;
        echo sprintf('%d enviou uma mensagem para %d other connection%s' . "\n" , 
            $from->resourceId, $numRecv, $numRecv == 1 ? '' : 's');

        foreach ($this->clients as $client) {
            if ($from !== $client) {
            	$msg = $from->resourceId . ': ' . $msg;
                // The sender is not the receiver, send to each client connected
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "{$conn->resourceId} se desconectou\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: ", $e->getMessage();

        $conn->close();
    }
}

$server = IoServer::factory(
    new HttpServer(
        new WsServer(new ChatServer())
    ), 4242, '0.0.0.0');
$server->run();