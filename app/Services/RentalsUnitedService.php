<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
class RentalsUnitedService
{
    protected $client;

    public function __construct()
    {
        // dd(config('rentalsunited.username'));
        $this->client = new Client([
            'base_uri' => config('rentalsunited.base_url'),
            'auth' => [config('rentalsunited.username'), config('rentalsunited.password')],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    // Get all threads
    public function getThreads($parentId)
    {   
        // $response = $this->client->get('/api/messaging/threads', [
        //     'query' => [
        //         'parentType' => 'RUReservation',
        //         'parentId' => $parentId,
        //     ]
        // ]);

        // return json_decode($response->getBody(), true);
        try {
            if (empty($parentId)) {
                return [
                    'status' => false,
                    'message' => 'Parent ID is required'
                ];
            }
            $response = $this->client->get('/api/messaging/threads', [
                'query' => [
                    'parentType' => 'RUReservation',
                    'parentId'   => $parentId,
                ]
            ]);
            return json_decode($response->getBody(), true);

        } catch (ConnectException $e) {
            return [
                'status' => false,
                'message' => 'Connection error: ' . $e->getMessage()
            ];
        } catch (RequestException $e) {
            return [
                'status' => false,
                'message' => 'Request error: ' . $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ];
        }
    }

    // Get messages of thread
    public function getMessages($threadId, $limit)
    {
        $response = $this->client->get(
            "/api/messaging/threads/{$threadId}/messages",
            [
                'query' => [
                    'messageLimit' => $limit
                ]
            ]
        );
        $messages = json_decode($response->getBody(), true);
        return array_reverse($messages);
    }

    // Send message
    public function sendMessage($threadId, $payload)
    {
        try {
            $response = $this->client->post(
                "/api/messaging/threads/{$threadId}/messages",
                [
                    'json' => $payload
                ]
            );
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $decoded = json_decode($body, true);
            // API logical error check
            if (isset($decoded['ErrorCode']) && $decoded['ErrorCode'] != 0) {
                return [
                    'status' => false,
                    'message' => $decoded['ErrorMessage'] ?? 'API Error'
                ];
            }
            return [
                'status' => true,
                'message' => 'Message sent successfully'
            ];
        } catch (RequestException $e) {
            return [
                'status' => false,
                'message' => 'Server error while sending message'
            ];
        }
    }
    // public function sendMessage($threadId, $payload)
    // {
    //     try {
    //         $response =$this->client->post("/api/messaging/threads/{$threadId}/messages", [
    //             'json' => $payload
    //         ]);
    //         // dd(
    //         //     $response->getStatusCode(),
    //         //     $response->getBody()->getContents()
    //         // );
    //         return back()->with('success', 'Message sent successfully');
    //     } catch (RequestException $e){
    //         $errorMessage = 'Something went wrong';

    //         if ($e->hasResponse()) {
    //             $responseBody = $e->getResponse()->getBody()->getContents();
    //             $decoded = json_decode($responseBody, true);

    //             // agar API specific error bhej rahi ho
    //             if (isset($decoded['Message'])) {
    //                 $errorMessage = $decoded['Message'];
    //             } elseif (isset($decoded['error'])) {
    //                 $errorMessage = $decoded['error'];
    //             } else {
    //                 $errorMessage = $responseBody;
    //             }
    //         }

    //         return [
    //             'status' => false,
    //             'message' => $errorMessage
    //         ];
    //     }

    // }

    public function markAsRead($threadId)
    {
        try {
            $response = $this->client->put(
                "/api/messaging/threads/{$threadId}/messages/mark-as-read"
            );
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}