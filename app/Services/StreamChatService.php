<?php

namespace App\Services;

use GetStream\StreamChat\Client;

class StreamChatService
{
    private $client;

    public function __construct()
    {
        $this->client = new Client(config('stream.api_key'), config('stream.api_secret'));
    }

    public function getClient()
    {
        return $this->client;
    }

    public function createUser($userId, $name, $email)
    {
        return $this->client->upsertUser([
            'id' => $userId,
            'email' => $email,
            'name' => $name,
        ]);
    }

    public function createChannel($channelType, $channelId, $members)
    {
        return $this->client->channel($channelType, $channelId, ['members' => $members]);
    }

    public function sendMessage($channelType, $channelId, $message, $userId)
    {
        $channel = $this->client->channel($channelType, $channelId);
        return $channel->sendMessage([
            'text' => $message,
        ], $userId);
    }

    public function fetchMessages($channelType, $channelId)
    {
        $channel = $this->client->channel($channelType, $channelId);
        return $channel->query(['messages' => ['limit' => 50]]);
    }
}
?>
