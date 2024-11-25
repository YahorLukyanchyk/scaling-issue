<?php

require 'vendor/autoload.php';

$service = new Server\BracketsServer;

$service->start(9999);