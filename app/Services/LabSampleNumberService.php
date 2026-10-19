<?php

namespace App\Services;

use App\Models\LabRequest;

class LabSampleNumberService
{
    public function generate(string $department, string $sampleType): string
    {
        $deptPrefix = config("diagnosis.lab_departments.{$department}.prefix", strtoupper(substr($department, 0, 3)));
        $sampleCode = config("diagnosis.sample_type_codes.{$sampleType}", 'OTH');
        $year = now()->format('Y');
        $pattern = "{$deptPrefix}-{$sampleCode}-{$year}%";

        $last = LabRequest::where('sample_number', 'like', $pattern)
            ->where('lab_department', $department)
            ->where('sample_type', $sampleType)
            ->latest('id')
            ->first();

        $sequence = $last ? (int) substr($last->sample_number, -6) + 1 : 1;

        return "{$deptPrefix}-{$sampleCode}-{$year}" . str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
    }
}
