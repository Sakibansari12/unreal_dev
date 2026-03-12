<?php

namespace App\Http\Controllers\PMS\Messages;

use App\Http\Controllers\Controller;
use App\Services\RentalsUnitedService;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\PropertyBooking;

class OTAMessagingController extends Controller
{
    protected $ruService;
    protected $xmlUrl;
    protected $username;
    protected $password;
    protected $apiBase;

    public function __construct(RentalsUnitedService $ruService)
    {
        $this->ruService = $ruService;

        // env se load karo
        $this->xmlUrl   = env('RU_URL');
        $this->username = env('RU_USER_NAME');
        $this->password = env('RU_PASSWORD');
        $this->apiBase  = env('RU_API_BASE');
    }

    // Inbox (list all threads)
    public function index(Request $request)
    {
        // $parentIds = [145097840, 145077888];
        $parentIds = PropertyBooking::pluck('booking_id')->toArray();
        $threads = collect();
        foreach ($parentIds as $parentId) {
            $result = $this->ruService->getThreads($parentId);
            $threads = $threads->merge($result);
        }

        $threadId = $request->thread_id;
        $messages = [];
        $limit = $request->get('limit', 16);
        if ($threadId) {
            $this->ruService->markAsRead($threadId);
            cache()->forget('ru_unread_count');
            $messages = $this->ruService->getMessages($threadId, $limit);
        }
        
        return view('pms.messages.index', compact('threads', 'messages', 'threadId', 'limit'));
    }

    // Show all messages of a thread
    // public function show($id)
    // {
    //     $messages = $this->ruService->getMessages($id);

    //     return view('pms.messages.show', [
    //         'messages' => $messages,
    //         'threadId' => $id
    //     ]);
    // }

    // Send new message in a thread
    public function send(Request $request, $id)
    {
        // dd($request->file('attachment'));
        $message = $request->message;
        $payload = [
            "Body" => $message,
        ];
        if ($request->hasFile('attachment')) {
            $attachments = [];
            foreach ($request->file('attachment') as $file) {
                $attachments[] = [
                    "Name" => $file->getClientOriginalName(),
                    "Content" => base64_encode(file_get_contents($file->getRealPath()))
                ];
            }
            $payload["AddAttachmentsPayload"] = $attachments;
        }
        // dd($payload);
        $result = $this->ruService->sendMessage($id, $payload);
        if ($result['status']) {
            return redirect()->route('pms.unified-inbox', ['thread_id' => $id])->with('success', $result['message']);
        }

        return redirect()->route('pms.unified-inbox', ['thread_id' => $id])->with('error', $result['message']);
        // return redirect()->route('pms.unified-inbox', ['thread_id' => $id]);
    }

    public function loadMore(Request $request, $threadId)
    {
        $limit = $request->get('limit', 16);
        $messages = $this->ruService->getMessages($threadId, $limit);
        return response()->json($messages);
    }

    public function viewAttachment(Request $request)
    {
        $base64 = preg_replace('/\s+/', '', $request->data);
        $mime = $request->mime ?? 'image/jpeg';

        return response(base64_decode($base64))
            ->header('Content-Type', $mime)
            ->header('Content-Disposition', 'inline; filename="attachment"');
    }

    public function unreadMessagesCount()
    {
        $count = cache()->remember('ru_unread_count', 60, function () {
            $parentIds = PropertyBooking::where('channel','Airbnb')->pluck('booking_id')->toArray();
            $count = 0;
            foreach ($parentIds as $parentId) {
                $threads = $this->ruService->getThreads($parentId);
                if (is_array($threads)) {
                    foreach ($threads as $thread) {
                        if(is_array($thread) && isset($thread['CommunicationChannel']) && in_array($thread['CommunicationChannel'], ['Airbnb'])){
                            if (isset($thread['NumberOfUnreadMessages'])) {
                                $count += $thread['NumberOfUnreadMessages'];
                            }
                        }
                    }
                }
            }
            return $count;
        });

        return response()->json(['notifycount' => $count]);
    }
}

