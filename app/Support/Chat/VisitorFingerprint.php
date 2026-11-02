<?php

namespace App\Support\Chat;

/**
 * A keyed hash of the visitor's IP address. Used for rate limiting and chat logs,
 * so the raw IP is never stored. Keying with the app key stops anyone reversing it
 * by hashing every possible IPv4 address.
 */
class VisitorFingerprint
{
    public static function fromIp(?string $ip): string
    {
        return hash_hmac('sha256', (string) $ip, (string) config('app.key'));
    }
}
