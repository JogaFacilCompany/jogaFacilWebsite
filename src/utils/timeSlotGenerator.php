<?php
// utils/timeSlotGenerator.php – camelCase enforced

function generateRelativeTimeSlots(string $operatingHours): array {
    $timeSlots = ['Manhã' => [], 'Tarde' => [], 'Noite' => []];

    if (!preg_match('/(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})/', $operatingHours, $matches)) {
        $startHour = 8;
        $endHour   = 22;
    } else {
        $startHour = (int)explode(':', $matches[1])[0];
        $endHour   = (int)explode(':', $matches[2])[0];
    }

    for ($hour = $startHour; $hour < $endHour; $hour++) {
        $hourLabel = sprintf('%02d:00', $hour);
        if ($hour < 12) {
            $timeSlots['Manhã'][] = $hourLabel;
        } elseif ($hour < 18) {
            $timeSlots['Tarde'][] = $hourLabel;
        } else {
            $timeSlots['Noite'][] = $hourLabel;
        }
    }

    return $timeSlots;
}
