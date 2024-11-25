<?php

namespace Server;

use Interfaces\BracketsServerInterface;
use BracketsChecker\BracketsChecker;
use Exception;

class BracketsServer implements BracketsServerInterface
{

    private $address = '127.0.0.1';

    public function start(int $port): void
    {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if (!$socket) {
            echo "socket_create() failed. Error: " . socket_strerror(socket_last_error()) . "\n";
        }

        if (!socket_bind($socket, $this->address, $port)) {
            echo "socket_bind() failed. Error: " . socket_strerror(socket_last_error($socket)) . "\n";
        }

        if (!socket_listen($socket, 5)) {
            echo "socket_listen() failed. Error: " . socket_strerror(socket_last_error($socket)) . "\n";
        }

        while (true) {
            $client = socket_accept($socket);

            if (!$client) {
                echo "Failed to accept client connection. Error: " . socket_strerror(socket_last_error($socket)) . "\n";
            }

            $data = socket_read($client, 1024);
            $data = json_decode($data);

            try {
                $service = new BracketsChecker;

                $result = $service->check($data->bracketsSequence, $data->filePath);
            } catch (Exception $e){
                socket_write($client, $e);
            }

            if (!$result) {
                $resultMessage = "Rows are different!\n";
            } else {
                $resultMessage = "Rows are correct!\n";
            }

            socket_write($client, $resultMessage);
        }

        socket_close($socker);
    }
}
