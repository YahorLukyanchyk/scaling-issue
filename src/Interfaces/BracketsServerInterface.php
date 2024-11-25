<?php

namespace Interfaces;

interface BracketsServerInterface {
    public function start(int $port): void;
}