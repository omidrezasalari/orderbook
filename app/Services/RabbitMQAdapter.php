<?php

namespace App\Services;

use App\Contracts\MessageBrokerInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQAdapter implements MessageBrokerInterface
{
    private AMQPStreamConnection $connection;
    private \PhpAmqpLib\Channel\AMQPChannel $channel;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $config = config('services.rabbitmq');

        $this->connection = new AMQPStreamConnection($config['host'], $config['port'],
            $config['username'], $config['password'], $config['vhost']
        );

        $this->channel = $this->connection->channel();;
    }

    public function sendMessage(string $queue, string $message): void
    {
        $this->channel->queue_declare($queue, false, true, false, false);
        $msg = new AMQPMessage($message, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
        $this->channel->basic_publish($msg, '', $queue);
    }

    public function consumeMessages(string $queue, callable $callback): void
    {
        $this->channel->queue_declare($queue, false, true, false, false);
        $this->channel->basic_consume($queue, '', false, true, false, false, $callback);

        while($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    public function acknowledgeMessage(string $deliveryTag): void
    {
        $this->channel->basic_ack($deliveryTag);
    }

    public function close(): void
    {
        $this->channel->close();
        $this->connection->close();
    }
}

