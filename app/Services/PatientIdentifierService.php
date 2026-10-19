<?php

namespace App\Services;

use App\Models\Person;

class PatientIdentifierService
{
    public function generateTempNumber(): string
    {
        $prefix = config('diagnosis.temp_prefix', 'TEMP');
        $date = now()->format('Ymd');
        $last = Person::where('temp_number', 'like', $prefix . $date . '%')->latest('id')->first();
        $sequence = $last ? (int) substr($last->temp_number, -4) + 1 : 1;

        return $prefix . $date . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    public function generatePid(): string
    {
        $prefix = config('diagnosis.pid_prefix', 'PID');
        $year = now()->format('Y');
        $last = Person::where('pid', 'like', $prefix . $year . '%')->latest('id')->first();
        $sequence = $last ? (int) substr($last->pid, -6) + 1 : 1;

        return $prefix . $year . str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
    }

    public function createTempPatient(array $data): Person
    {
        return Person::create(array_merge($data, [
            'temp_number' => $this->generateTempNumber(),
            'registration_status' => 'temp',
            'pid' => null,
        ]));
    }

    public function assignPid(Person $person): Person
    {
        if ($person->registration_status === 'registered' && $person->pid) {
            return $person;
        }

        $person->update([
            'pid' => $this->generatePid(),
            'registration_status' => 'registered',
            'pid_generated_at' => now(),
        ]);

        return $person->fresh();
    }

    public function findByPid(string $pid): ?Person
    {
        return Person::where('pid', $pid)
            ->where('registration_status', 'registered')
            ->first();
    }

    public function findByTempNumber(string $tempNumber): ?Person
    {
        return Person::where('temp_number', $tempNumber)
            ->where('registration_status', 'temp')
            ->first();
    }
}
