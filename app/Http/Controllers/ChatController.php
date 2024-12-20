<?php

namespace App\Http\Controllers;
use App\Services\StreamChatService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\{UserId,Chat};

class ChatController extends Controller
{
    private $streamChat;

    public function __construct(StreamChatService $streamChat)
    {
        $this->streamChat = $streamChat;
    }

    public function index(Request $request)
    {
        if (!Auth()->check()) {
            return redirect()->route('vehicle_list.index');
        }

        $unread_count = Chat::where('user_id', Auth()->user()->id)->select('unread_count')->first()->unread_count;

        $userId = strval(Auth::id());
        $userName = Auth::user()->name;
        $userEmail = Auth::user()->email;

        if($request->action) {
            $autoload = true;
            $owner = UserId::where('id', $request->owner_id)->first();
            $profilePath = 'storage/images/profile_photos/' . $request->owner_id . '_' . $owner->name . '.png';
            $defaultImage = asset('images/not_found.jpg');
            if($request->owner_id) {
                $checkChannelFrom = $this->streamChat->getClient()->queryChannels([
                    'type' => 'messaging',
                    'id' => $request->owner_id.'-'.$userId,
                ]);

                if(empty($channels)) {
                    $channelId = $this->createDirectChannel($userId, $request->owner_id);
                } else {
                    $channelId = $request->owner_id.'-'.$userId;
                }
            }
            $data = [
                'user_id' => $request->owner_id,
                'user_name' => $owner->name,
                'channelId' => $channelId,
                'profilePicture' => file_exists(public_path($profilePath)) ? asset($profilePath) : $defaultImage
            ];
        } else {
            $autoload = false;
            $data = [];
        }

        $userToken = $this->streamChat->getClient()->createToken($userId);

        $channels = $this->streamChat->getClient()->queryChannels(
            ['members' => ['$in' => [$userId]]], // Ensure user ID is a string
            ['last_message_at' => -1] // Sort by most recent message
        );

        $message = [];
        $totalUnread = 0;
        foreach ($channels as $channel) {
            foreach ((array) $channel as $val) {
                $channelData = $val['channel'] ?? [];
                $lastMessageAt = $channelData['last_message_at'] ?? null;
                $unreadCount = 0;
                // Ensure 'read' key exists in $channel
                foreach ($val['read'] ?? [] as $readData) {
                    if ($readData['user']['id'] == $userId && $lastMessageAt) {
                        $lastReadAt = $readData['last_read'] ?? null;
                        if ($lastReadAt && strtotime($lastMessageAt) > strtotime($lastReadAt)) {
                            $unreadCount = $readData['unread_messages'];
                        } else {
                            $totalUnread += $readData['unread_messages'];
                        }
                    }
                }

                // Safely access 'id' from $channelData
                $channelId = $channelData['id'] ?? null;
                if (!$channelId) {
                    continue; // Skip if 'id' is not set
                }

                $userIdFromChannel = str_replace([$userId, '-'], '', $channelId);
                $userName = UserId::where('id', $userIdFromChannel)->value('name');

                $message[] = (object) [
                    'user_id' => $userIdFromChannel,
                    'user_name' => $userName,
                    'channelId' => $channelId,
                    'last_message_at' => $lastMessageAt,
                    'last_message' => isset($val['messages']) && count($val['messages']) > 0
                        ? end($val['messages'])['text']
                        : '',
                    'unread_count' => $unreadCount,
                ];
            }
        }

        $chatExist = Chat::where('user_id', $userId)
                        ->where('user_name', Auth::user()->name)->first();

        if($chatExist) {
            Chat::where('id', $chatExist->id)->update([
                'unread_count' => $totalUnread
            ]);
        } else {
            Chat::Create([
                'user_id' => $userId,
                'user_name' => Auth::user()->name,
                'channel_id' => $userId,
                'unread_count' => $totalUnread
            ]);
        }

        return view('chat.index', compact('message','userToken','autoload','data','unread_count'));
    }

    private function createDirectChannel($userId, $ownerId)
    {
        $userA = $userId; // Sender (string)
        $userB = $ownerId; // Recipient (string)

        // Generate a unique channel ID (consistent for both users)
        $channelId = implode('-', [min($userA, $userB), max($userA, $userB)]);

        // Create or fetch the channel
        $channel = $this->streamChat->getClient()->channel('messaging', $channelId, [
            'members' => [$userA, $userB],
        ]);

        // Create the channel (if it doesn't already exist)
        $channel->create($userA);

        return $channelId; // Return the channel ID for further use
    }

    private function deleteChannel($channelId)
    {
        try {
            // Access the channel
            $channel = $this->streamChat->getClient()->channel('messaging', $channelId);

            // Delete the channel
            $response = $channel->delete();

            return response()->json([
                'success' => true,
                'message' => 'Channel deleted successfully',
                'response' => $response,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function fetchChannels()
    {
        if (!Auth()->check()) {
            return redirect()->route('vehicle_list.index');
        }

        $userId = strval(Auth::id());
        $channels = $this->streamChat->getClient()->queryChannels(
            ['members' => ['$in' => [$userId]]], // Ensure user ID is a string
            ['last_message_at' => -1] // Sort by most recent message
        );

        $message = [];
        $totalUnread = 0;
        foreach ($channels as $channel) {
            foreach ((array) $channel as $val) {
                $channelData = $val['channel'] ?? [];
                $lastMessageAt = $channelData['last_message_at'] ?? null;
                $unreadCount = 0;
                // Ensure 'read' key exists in $channel
                foreach ($val['read'] ?? [] as $readData) {
                    if ($readData['user']['id'] == $userId && $lastMessageAt) {
                        $lastReadAt = $readData['last_read'] ?? null;
                        if ($lastReadAt && strtotime($lastMessageAt) > strtotime($lastReadAt)) {
                            $unreadCount = $readData['unread_messages'];
                            $totalUnread += $readData['unread_messages'];
                        } else {
                            $totalUnread += $readData['unread_messages'];
                        }
                    }
                }

                // Safely access 'id' from $channelData
                $channelId = $channelData['id'] ?? null;
                if (!$channelId) {
                    continue; // Skip if 'id' is not set
                }

                $userIdFromChannel = str_replace([$userId, '-'], '', $channelId);
                $userName = UserId::where('id', $userIdFromChannel)->value('name');
                $profilePath = 'storage/images/profile_photos/' . $userIdFromChannel. '_' . $userName . '.png';
                $defaultImage = asset('images/not_found.jpg');

                $message[] = (object) [
                    'user_id' => $userIdFromChannel,
                    'user_name' => $userName,
                    'channelId' => $channelId,
                    'last_message_at' => $lastMessageAt,
                    'last_message' => isset($val['messages']) && count($val['messages']) > 0
                        ? end($val['messages'])['text']
                        : '',
                    'unread_count' => $unreadCount,
                    'profilePicture' => file_exists(public_path($profilePath)) ? asset($profilePath) : $defaultImage
                ];
            }
        }

        $chatExist = Chat::where('user_id', $userId)
                        ->where('user_name', Auth::user()->name)->first();

        if($chatExist) {
            Chat::where('id', $chatExist->id)->update([
                'unread_count' => $totalUnread
            ]);
        } else {
            Chat::Create([
                'user_id' => $userId,
                'user_name' => Auth::user()->name,
                'channel_id' => $userId,
                'unread_count' => $totalUnread
            ]);
        }

        return response()->json($message);
    }
}
?>
