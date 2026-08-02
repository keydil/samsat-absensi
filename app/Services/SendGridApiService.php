<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SendGridApiService
{
    public static function sendHtmlEmail(string $toEmail, string $subject, string $htmlBody): bool
    {
        $apiKey = env('SENDGRID_API_KEY') ?: env('MAIL_PASSWORD');
        $fromEmail = env('MAIL_FROM_ADDRESS', 'fadhilfirdausadha493@gmail.com');
        $fromName = env('MAIL_FROM_NAME', 'SAMSAT Absensi');

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->post('https://api.sendgrid.com/v3/mail/send', [
                'personalizations' => [
                    [
                        'to' => [
                            ['email' => $toEmail]
                        ]
                    ]
                ],
                'from' => [
                    'email' => $fromEmail,
                    'name'  => $fromName
                ],
                'subject' => $subject,
                'content' => [
                    [
                        'type'  => 'text/html',
                        'value' => $htmlBody
                    ]
                ]
            ]);

        if ($response->successful() || $response->status() === 202) {
            return true;
        }

        $errorMsg = $response->json('errors.0.message') ?: $response->body();
        throw new \Exception('SendGrid HTTP API Error (' . $response->status() . '): ' . $errorMsg);
    }
}
