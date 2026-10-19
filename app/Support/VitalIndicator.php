<?php

namespace App\Support;

use App\Models\EncounterVitalsDetail;

class VitalIndicator
{
    /**
     * Build display items from the latest vital record.
     *
     * @return array<int, array{key: string, label: string, value: string, status: ?string, icon: string}>
     */
    public static function summary(EncounterVitalsDetail $vital): array
    {
        $items = [];

        if ($vital->blood_pressure) {
            $items[] = [
                'key' => 'blood_pressure',
                'label' => 'BP',
                'value' => $vital->blood_pressure,
                'status' => self::assessBloodPressure($vital->blood_pressure),
                'icon' => 'fa-heart-pulse',
            ];
        }

        if ($vital->pulse !== null) {
            $items[] = [
                'key' => 'pulse',
                'label' => 'Pulse',
                'value' => $vital->pulse,
                'status' => self::assessPulse((int) $vital->pulse),
                'icon' => 'fa-heartbeat',
            ];
        }

        if ($vital->temperature !== null) {
            $items[] = [
                'key' => 'temperature',
                'label' => 'Temp',
                'value' => rtrim(rtrim(number_format((float) $vital->temperature, 1, '.', ''), '0'), '.') . '°',
                'status' => self::assessTemperature((float) $vital->temperature),
                'icon' => 'fa-thermometer-half',
            ];
        }

        if ($vital->respiratory_rate !== null) {
            $items[] = [
                'key' => 'respiratory_rate',
                'label' => 'RR',
                'value' => $vital->respiratory_rate,
                'status' => self::assessRespiratoryRate((int) $vital->respiratory_rate),
                'icon' => 'fa-lungs',
            ];
        }

        if ($vital->oxygen_saturation !== null) {
            $items[] = [
                'key' => 'oxygen_saturation',
                'label' => 'SpO₂',
                'value' => rtrim(rtrim(number_format((float) $vital->oxygen_saturation, 1, '.', ''), '0'), '.') . '%',
                'status' => self::assessOxygenSaturation((float) $vital->oxygen_saturation),
                'icon' => 'fa-wind',
            ];
        }

        if ($vital->weight !== null) {
            $items[] = [
                'key' => 'weight',
                'label' => 'Wt',
                'value' => rtrim(rtrim(number_format((float) $vital->weight, 1, '.', ''), '0'), '.') . 'kg',
                'status' => null,
                'icon' => 'fa-weight',
            ];
        }

        if ($vital->height !== null) {
            $items[] = [
                'key' => 'height',
                'label' => 'Ht',
                'value' => rtrim(rtrim(number_format((float) $vital->height, 1, '.', ''), '0'), '.') . 'cm',
                'status' => null,
                'icon' => 'fa-ruler-vertical',
            ];
        }

        return $items;
    }

    public static function assessBloodPressure(?string $bloodPressure): ?string
    {
        if (!$bloodPressure || !preg_match('/^(\d+)\s*\/\s*(\d+)/', trim($bloodPressure), $matches)) {
            return null;
        }

        $systolic = (int) $matches[1];
        $diastolic = (int) $matches[2];

        if ($systolic < 90 || $diastolic < 60) {
            return 'low';
        }

        if ($systolic >= 130 || $diastolic >= 85) {
            return 'high';
        }

        return 'normal';
    }

    public static function assessPulse(?int $pulse): ?string
    {
        if ($pulse === null) {
            return null;
        }

        if ($pulse < 60) {
            return 'low';
        }

        if ($pulse > 100) {
            return 'high';
        }

        return 'normal';
    }

    public static function assessTemperature(?float $temperature): ?string
    {
        if ($temperature === null) {
            return null;
        }

        if ($temperature < 97.0) {
            return 'low';
        }

        if ($temperature > 99.5) {
            return 'high';
        }

        return 'normal';
    }

    public static function assessRespiratoryRate(?int $rate): ?string
    {
        if ($rate === null) {
            return null;
        }

        if ($rate < 12) {
            return 'low';
        }

        if ($rate > 20) {
            return 'high';
        }

        return 'normal';
    }

    public static function assessOxygenSaturation(?float $saturation): ?string
    {
        if ($saturation === null) {
            return null;
        }

        if ($saturation < 95) {
            return 'low';
        }

        return 'normal';
    }
}
