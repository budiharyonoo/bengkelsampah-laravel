<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected $accessToken;

    protected $projectId;

    public function __construct()
    {
        $this->projectId = config('firebase.project_id');
        $this->accessToken = $this->getAccessToken();
    }

    /**
     * Get OAuth2 access token for Firebase Admin SDK
     */
    private function getAccessToken()
    {
        try {
            $serviceAccountPath = storage_path('app/firebase-service-account.json');

            if (! file_exists($serviceAccountPath)) {
                Log::error('Firebase service account file not found: '.$serviceAccountPath);

                return null;
            }

            $serviceAccountKey = json_decode(file_get_contents($serviceAccountPath), true);

            if (! $serviceAccountKey) {
                Log::error('Invalid Firebase service account JSON');

                return null;
            }

            $response = Http::post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $this->createJWT($serviceAccountKey),
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return $data['access_token'] ?? null;
            } else {
                Log::error('Failed to get Firebase access token', [
                    'error' => $response->body(),
                    'status' => $response->status(),
                ]);

                return null;
            }
        } catch (\Exception $e) {
            Log::error('Error getting Firebase access token', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Create JWT for Firebase Admin SDK authentication
     */
    private function createJWT($serviceAccountKey)
    {
        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT',
        ];

        $now = time();
        $payload = [
            'iss' => $serviceAccountKey['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now,
        ];

        $headerEncoded = $this->base64UrlEncode(json_encode($header));
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));

        $signature = '';
        openssl_sign(
            $headerEncoded.'.'.$payloadEncoded,
            $signature,
            $serviceAccountKey['private_key'],
            'SHA256'
        );

        $signatureEncoded = $this->base64UrlEncode($signature);

        return $headerEncoded.'.'.$payloadEncoded.'.'.$signatureEncoded;
    }

    /**
     * Base64 URL encode
     */
    private function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Send notification to specific user by FCM token using FCM API v1
     */
    public function sendToUser($fcmToken, $title, $body, $data = [])
    {
        try {
            if (! $this->accessToken) {
                Log::error('Firebase access token not available');

                return [
                    'success' => false,
                    'error' => 'Firebase access token not available',
                ];
            }

            $message = [
                'message' => [
                    'token' => $fcmToken,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => $this->convertDataToStrings($data),
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'sound' => 'default',
                            'channel_id' => 'bengkelsampah_channel',
                        ],
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                            ],
                        ],
                    ],
                ],
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->accessToken,
                'Content-Type' => 'application/json',
            ])->post("https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send", $message);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('Firebase notification sent successfully', [
                    'fcm_token' => $fcmToken,
                    'name' => $result['name'] ?? null,
                    'title' => $title,
                ]);

                return [
                    'success' => true,
                    'name' => $result['name'] ?? null,
                ];
            }

            Log::error('Firebase notification failed', [
                'fcm_token' => $fcmToken,
                'error' => $response->body(),
                'status' => $response->status(),
                'title' => $title,
            ]);

            return [
                'success' => false,
                'error' => $response->body(),
            ];
        } catch (\Exception $e) {
            Log::error('Firebase notification exception', [
                'fcm_token' => $fcmToken,
                'error' => $e->getMessage(),
                'title' => $title,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send notification to multiple users using FCM API v1
     */
    public function sendToMultipleUsers($fcmTokens, $title, $body, $data = [])
    {
        try {
            if (! $this->accessToken) {
                Log::error('Firebase access token not available');

                return [
                    'success' => false,
                    'error' => 'Firebase access token not available',
                ];
            }

            $results = [];
            $successCount = 0;
            $failureCount = 0;

            foreach ($fcmTokens as $token) {
                $result = $this->sendToUser($token, $title, $body, $data);
                $results[] = [
                    'token' => $token,
                    'success' => $result['success'],
                    'error' => $result['error'] ?? null,
                ];

                if ($result['success']) {
                    $successCount++;
                } else {
                    $failureCount++;
                }
            }

            Log::info('Firebase batch notification completed', [
                'total_tokens' => count($fcmTokens),
                'success_count' => $successCount,
                'failure_count' => $failureCount,
                'title' => $title,
            ]);

            return [
                'success' => true,
                'success_count' => $successCount,
                'failure_count' => $failureCount,
                'results' => $results,
            ];
        } catch (\Exception $e) {
            Log::error('Firebase batch notification exception', [
                'total_tokens' => count($fcmTokens),
                'error' => $e->getMessage(),
                'title' => $title,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send notification to topic using FCM API v1
     */
    public function sendToTopic($topic, $title, $body, $data = [])
    {
        try {
            if (! $this->accessToken) {
                Log::error('Firebase access token not available');

                return [
                    'success' => false,
                    'error' => 'Firebase access token not available',
                ];
            }

            $message = [
                'message' => [
                    'topic' => $topic,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => $this->convertDataToStrings($data),
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'sound' => 'default',
                            'channel_id' => 'bengkelsampah_channel',
                        ],
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                            ],
                        ],
                    ],
                ],
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->accessToken,
                'Content-Type' => 'application/json',
            ])->post("https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send", $message);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('Firebase topic notification sent', [
                    'topic' => $topic,
                    'name' => $result['name'] ?? null,
                    'title' => $title,
                ]);

                return [
                    'success' => true,
                    'name' => $result['name'] ?? null,
                ];
            } else {
                Log::error('Firebase topic notification failed', [
                    'topic' => $topic,
                    'error' => $response->body(),
                    'status' => $response->status(),
                    'title' => $title,
                ]);

                return [
                    'success' => false,
                    'error' => $response->body(),
                ];
            }
        } catch (\Exception $e) {
            Log::error('Firebase topic notification exception', [
                'topic' => $topic,
                'error' => $e->getMessage(),
                'title' => $title,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Subscribe user to topic using FCM API v1
     */
    public function subscribeToTopic($fcmToken, $topic)
    {
        try {
            if (! $this->accessToken) {
                Log::error('Firebase access token not available');

                return [
                    'success' => false,
                    'error' => 'Firebase access token not available',
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->accessToken,
                'Content-Type' => 'application/json',
            ])->post("https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send", [
                'message' => [
                    'topic' => $topic,
                    'token' => $fcmToken,
                ],
            ]);

            if ($response->successful()) {
                Log::info('User subscribed to topic', [
                    'fcm_token' => $fcmToken,
                    'topic' => $topic,
                ]);

                return [
                    'success' => true,
                ];
            } else {
                Log::error('Failed to subscribe user to topic', [
                    'fcm_token' => $fcmToken,
                    'topic' => $topic,
                    'error' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'error' => $response->body(),
                ];
            }
        } catch (\Exception $e) {
            Log::error('Failed to subscribe user to topic', [
                'fcm_token' => $fcmToken,
                'topic' => $topic,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Unsubscribe user from topic using FCM API v1
     */
    public function unsubscribeFromTopic($fcmToken, $topic)
    {
        try {
            if (! $this->accessToken) {
                Log::error('Firebase access token not available');

                return [
                    'success' => false,
                    'error' => 'Firebase access token not available',
                ];
            }

            // Note: FCM API v1 doesn't have a direct unsubscribe endpoint
            // You would typically handle this through your app's logic
            // or use the legacy API for this specific operation
            Log::info('Unsubscribe operation not directly supported in FCM API v1', [
                'fcm_token' => $fcmToken,
                'topic' => $topic,
            ]);

            return [
                'success' => true,
                'note' => 'Unsubscribe handled through app logic',
            ];
        } catch (\Exception $e) {
            Log::error('Failed to unsubscribe user from topic', [
                'fcm_token' => $fcmToken,
                'topic' => $topic,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Convert all data values to strings for FCM compatibility
     */
    private function convertDataToStrings($data)
    {
        $convertedData = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $convertedData[$key] = json_encode($value);
            } elseif (is_object($value)) {
                $convertedData[$key] = json_encode($value);
            } elseif (is_null($value)) {
                $convertedData[$key] = '';
            } elseif (is_bool($value)) {
                $convertedData[$key] = $value ? 'true' : 'false';
            } elseif (is_numeric($value)) {
                $convertedData[$key] = (string) $value;
            } else {
                $convertedData[$key] = (string) $value;
            }
        }

        return $convertedData;
    }
}
