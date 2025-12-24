<?php
class StatusHandler
{
    public static function handle($status)
    {
        $statusLower = strtolower($status);

        if (str_contains($statusLower, 'inactive')) {
            return 'status-inactive';
        } elseif (str_contains($statusLower, 'active')) {
            return 'status-active';
        } elseif (str_contains($statusLower, 'pending')) {
            return 'status-pending';
        } elseif (str_contains($statusLower, 'review')) {
            return 'status-pending';
        }

        return 'status-unknown';
    }
}
