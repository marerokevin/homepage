<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\VehicleRequest;
use App\Models\User;
use Carbon\Carbon;

class PollTripEmails extends Command
{
    protected $signature   = 'trips:poll-emails';
    protected $description = 'Poll Microsoft 365 inbox for trip request emails and schedule them';

    // Microsoft Graph token cache
    private ?string $accessToken = null;

    public function handle(): void
    {
        $this->info('Polling trip emails...');

        $token    = $this->getAccessToken();
        $messages = $this->fetchUnreadMessages($token);

        if (empty($messages)) {
            $this->info('No new trip emails found.');
            return;
        }

        foreach ($messages as $message) {
            $this->processMessage($token, $message);
        }

        $this->info('Done.');
    }

    // ---------------------------------------------------------------
    // Microsoft Graph: get OAuth2 token
    // ---------------------------------------------------------------
    private function getAccessToken(): string
    {
        if ($this->accessToken) return $this->accessToken;

        $tenantId = config('services.microsoft.tenant_id');
        $response = Http::asForm()->post(
            "https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token",
            [
                'grant_type'    => 'client_credentials',
                'client_id'     => config('services.microsoft.client_id'),
                'client_secret' => config('services.microsoft.client_secret'),
                'scope'         => 'https://graph.microsoft.com/.default',
            ]
        );

        if ($response->failed()) {
            Log::error('Microsoft Graph auth failed', $response->json());
            $this->error('Failed to authenticate with Microsoft Graph.');
            exit(1);
        }

        $this->accessToken = $response->json('access_token');
        return $this->accessToken;
    }

    // ---------------------------------------------------------------
    // Microsoft Graph: fetch unread messages from dedicated mailbox
    // ---------------------------------------------------------------
    private function fetchUnreadMessages(string $token): array
    {
        $mailbox = config('services.microsoft.mailbox');

        $response = Http::withToken($token)->get(
            "https://graph.microsoft.com/v1.0/users/{$mailbox}/mailFolders/inbox/messages",
            [
                '$filter'  => 'isRead eq false',
                '$top'     => 20,
                '$select'  => 'id,subject,body,from,receivedDateTime',
                '$orderby' => 'receivedDateTime asc',
            ]
        );

        if ($response->failed()) {
            Log::error('Failed to fetch emails', $response->json());
            return [];
        }

        return $response->json('value', []);
    }

    // ---------------------------------------------------------------
    // Process a single message
    // ---------------------------------------------------------------
    private function processMessage(string $token, array $message): void
    {
        $subject = $message['subject'] ?? '';
        $body    = strip_tags($message['body']['content'] ?? '');
        $from    = $message['from']['emailAddress']['address'] ?? '';
        $msgId   = $message['id'];

        $this->info("Processing: {$subject}");
        $this->line("  From: {$from}");
        $this->line("  Body preview: " . substr($body, 0, 300));

        // Parse trip details from email body
        $trip = $this->parseTripDetails($subject, $body);

        if (!$trip) {
            $this->warn("  Could not parse trip details — skipping.");
            $this->markAsRead($token, $msgId);
            return;
        }

        // Find user by email, fallback to first admin
        $user = User::where('email', $from)->first()
            ?? User::where('is_admin', true)->first();

        if (!$user) {
            $this->warn("  No matching user for {$from} — skipping.");
            return;
        }

        // Compute return_date
        $returnDate = null;
        if (!empty($trip['return_time'])) {
            $returnDate = ($trip['return_time'] < $trip['departure'])
                ? Carbon::parse($trip['trip_date'])->addDay()->toDateString()
                : $trip['trip_date'];
        } else {
            $returnDate = $trip['trip_date'];
        }

        // Auto-increment trip number for month
        $lastTrip   = VehicleRequest::whereYear('trip_date', Carbon::parse($trip['trip_date'])->year)
            ->whereMonth('trip_date', Carbon::parse($trip['trip_date'])->month)
            ->max('trip_number');
        $tripNumber = ($lastTrip ?? 0) + 1;

        // Create vehicle request
        VehicleRequest::create([
            'user_id'     => $user->id,
            'trip_number' => $tripNumber,
            'pickup'      => $trip['pickup'] ?? 'Crestec Philippines, Inc., Lima Technology Center, Lipa City, Batangas',
            'destination' => $trip['destination'],
            'vehicle'     => $trip['vehicle'] ?? 'TBD',
            'plate'       => $trip['plate'] ?? 'TBD',
            'trip_date'   => $trip['trip_date'],
            'departure'   => $trip['departure'],
            'eta'         => $trip['eta'] ?? null,
            'return_time' => $trip['return_time'] ?? null,
            'return_date' => $returnDate,
        ]);

        $this->info("  ✓ Trip scheduled: {$trip['destination']} on {$trip['trip_date']}");

        // Mark email as read so it won't be processed again
        $this->markAsRead($token, $msgId);
    }

    // ---------------------------------------------------------------
    // Parse trip details from email subject + body
    // ---------------------------------------------------------------
    private function parseTripDetails(string $subject, string $body): ?array
    {
        $text = strtolower($subject . ' ' . $body);

        // Must look like a trip request
        $keywords = ['trip', 'vehicle', 'request', 'destination', 'departure', 'schedule', 'booking'];
        $matched  = 0;
        foreach ($keywords as $kw) {
            if (str_contains($text, $kw)) $matched++;
        }
        if ($matched < 1) return null;

        $trip = [];

        // --- Trip Date ---
        // Formats: 2026-04-01 / April 1 2026 / April 1, 2026 / 04/01/2026
        if (preg_match('/trip\s*date[:\s]+(\d{4}-\d{2}-\d{2})/i', $body, $m)) {
            $trip['trip_date'] = $m[1];
        } elseif (preg_match('/trip\s*date[:\s]+(january|february|march|april|may|june|july|august|september|october|november|december)\s+(\d{1,2}),?\s*(\d{4})/i', $body, $m)) {
            $trip['trip_date'] = Carbon::parse("{$m[1]} {$m[2]} {$m[3]}")->toDateString();
        } elseif (preg_match('/trip\s*date[:\s]+(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/i', $body, $m)) {
            $trip['trip_date'] = Carbon::createFromFormat('m/d/Y', "{$m[1]}/{$m[2]}/{$m[3]}")->toDateString();
        } elseif (preg_match('/\b(\d{4}-\d{2}-\d{2})\b/', $body, $m)) {
            $trip['trip_date'] = $m[1];
        } elseif (preg_match('/\b(january|february|march|april|may|june|july|august|september|october|november|december)\s+(\d{1,2}),?\s+(\d{4})/i', $body, $m)) {
            $trip['trip_date'] = Carbon::parse("{$m[1]} {$m[2]} {$m[3]}")->toDateString();
        } elseif (preg_match('/\b(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})\b/', $body, $m)) {
            $trip['trip_date'] = Carbon::createFromFormat('m/d/Y', "{$m[1]}/{$m[2]}/{$m[3]}")->toDateString();
        } else {
            $this->warn("  Could not find a date in email body — skipping.");
            return null; // Can't schedule without a date
        }

        // --- Departure Time ---
        if (preg_match('/departure[:\s]+(\d{1,2}:\d{2}\s*(?:am|pm)?)/i', $body, $m)) {
            $trip['departure'] = Carbon::parse($m[1])->format('H:i:s');
        } elseif (preg_match('/\bleave[s]?\s+(?:at\s+)?(\d{1,2}:\d{2}\s*(?:am|pm)?)/i', $body, $m)) {
            $trip['departure'] = Carbon::parse($m[1])->format('H:i:s');
        } else {
            $trip['departure'] = '08:00:00';
        }

        // --- Return Time ---
        if (preg_match('/return\s*time[:\s]+(\d{1,2}:\d{2}\s*(?:am|pm)?)/i', $body, $m)) {
            $trip['return_time'] = Carbon::parse($m[1])->format('H:i:s');
        } elseif (preg_match('/return[:\s]+(\d{1,2}:\d{2}\s*(?:am|pm)?)/i', $body, $m)) {
            $trip['return_time'] = Carbon::parse($m[1])->format('H:i:s');
        }

        // --- ETA ---
        if (preg_match('/eta[:\s]+(\d{1,2}:\d{2}\s*(?:am|pm)?)/i', $body, $m)) {
            $trip['eta'] = Carbon::parse($m[1])->format('H:i:s');
        }

        // --- Destination ---
        if (preg_match('/destination[:\s]+([^,\r\n]+?)(?=Trip|Vehicle|Plate|Departure|Return|ETA|$)/i', $body, $m)) {
            $trip['destination'] = trim($m[1]);
        } else {
            $trip['destination'] = 'See email: ' . substr($subject, 0, 50);
        }

        // --- Vehicle ---
        if (preg_match('/vehicle[:\s]+([^,\r\n]+?)(?=Plate|Departure|Return|ETA|Destination|Trip|$)/i', $body, $m)) {
            $trip['vehicle'] = trim($m[1]);
        }

        // --- Plate ---
        if (preg_match('/plate(?:\s*number)?[:\s]+([A-Za-z0-9\s]+?)(?=Vehicle|Departure|Return|ETA|Destination|Trip|$)/i', $body, $m)) {
            $trip['plate'] = trim($m[1]);
        }

        // --- Pickup ---
        if (preg_match('/pickup[:\s]+([^\r\n]+?)(?=Destination|Vehicle|Plate|Departure|Return|ETA|Trip|$)/i', $body, $m)) {
            $trip['pickup'] = trim($m[1]);
        }

        return $trip;
    }

    // ---------------------------------------------------------------
    // Mark email as read
    // ---------------------------------------------------------------
    private function markAsRead(string $token, string $messageId): void
    {
        $mailbox = config('services.microsoft.mailbox');

        Http::withToken($token)->patch(
            "https://graph.microsoft.com/v1.0/users/{$mailbox}/messages/{$messageId}",
            ['isRead' => true]
        );
    }
}
