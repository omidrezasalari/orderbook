<?php

namespace App\Contracts;

interface MessageBrokerInterface
{
    public function sendMessage(string $queue, string $message): void;

    public function consumeMessages(string $queue, callable $callback): void;

    public function acknowledgeMessage(string $deliveryTag): void;

    public function close(): void;
}
