<?php

namespace App\Services;

use App\Models\Person;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PatientBiometricService
{
    public function hashFingerprint(string $template): string
    {
        return hash('sha256', trim($template));
    }

    public function buildSimulationTemplate(Person $person): string
    {
        $seed = implode('|', [
            $person->first_name,
            $person->last_name,
            $person->phone ?: '0000',
        ]);

        return base64_encode('SIMULATED:' . $seed);
    }

    public function findByFingerprintTemplate(string $template, ?int $excludePersonId = null): ?Person
    {
        $hash = $this->hashFingerprint($template);

        return Person::query()
            ->where('fingerprint_hash', $hash)
            ->when($excludePersonId, fn ($query) => $query->where('id', '!=', $excludePersonId))
            ->first();
    }

    public function findPotentialDuplicates(array $data, ?int $excludePersonId = null): array
    {
        $matches = [];

        if (!empty($data['fingerprint_template'])) {
            $fingerprintMatch = $this->findByFingerprintTemplate(
                $data['fingerprint_template'],
                $excludePersonId
            );

            if ($fingerprintMatch) {
                $matches[] = [
                    'type' => 'fingerprint',
                    'patient' => $fingerprintMatch,
                    'message' => 'This fingerprint is already registered to another patient.',
                ];
            }
        }

        if (!empty($data['phone'])) {
            $phoneMatch = Person::query()
                ->where('phone', $data['phone'])
                ->when($excludePersonId, fn ($query) => $query->where('id', '!=', $excludePersonId))
                ->first();

            if ($phoneMatch) {
                $matches[] = [
                    'type' => 'phone',
                    'patient' => $phoneMatch,
                    'message' => 'A patient with this phone number already exists.',
                ];
            }
        }

        if (!empty($data['first_name']) && !empty($data['last_name']) && !empty($data['date_of_birth'])) {
            $identityMatch = Person::query()
                ->where('first_name', $data['first_name'])
                ->where('last_name', $data['last_name'])
                ->whereDate('date_of_birth', $data['date_of_birth'])
                ->when($excludePersonId, fn ($query) => $query->where('id', '!=', $excludePersonId))
                ->first();

            if ($identityMatch) {
                $matches[] = [
                    'type' => 'identity',
                    'patient' => $identityMatch,
                    'message' => 'A patient with the same name and date of birth already exists.',
                ];
            }
        }

        return $matches;
    }

    public function savePhotoFromBase64(Person $person, string $base64Data): string
    {
        if (str_contains($base64Data, ',')) {
            $base64Data = explode(',', $base64Data, 2)[1];
        }

        $binary = base64_decode($base64Data, true);

        if ($binary === false) {
            throw new \InvalidArgumentException('Invalid photo data provided.');
        }

        if ($person->photo_path) {
            Storage::disk('public')->delete($person->photo_path);
        }

        $path = 'patients/photos/' . $person->id . '_' . Str::uuid() . '.jpg';
        Storage::disk('public')->put($path, $binary);

        $person->update(['photo_path' => $path]);

        return $path;
    }

    public function saveFingerprint(Person $person, string $template): Person
    {
        $person->update([
            'fingerprint_hash' => $this->hashFingerprint($template),
            'fingerprint_captured_at' => now(),
        ]);

        return $person->fresh();
    }

    public function photoUrl(?string $photoPath): ?string
    {
        if (!$photoPath || !Storage::disk('public')->exists($photoPath)) {
            return null;
        }

        return '/storage/' . ltrim($photoPath, '/');
    }

    public function formatDuplicateMatch(array $match): array
    {
        /** @var Person $patient */
        $patient = $match['patient'];

        return [
            'type' => $match['type'],
            'message' => $match['message'],
            'patient' => [
                'id' => $patient->id,
                'pid' => $patient->display_id,
                'name' => $patient->full_name,
                'phone' => $patient->phone,
                'date_of_birth' => optional($patient->date_of_birth)->format('Y-m-d'),
                'photo_url' => $this->photoUrl($patient->photo_path),
                'profile_url' => route('patients.show', $patient->id),
            ],
        ];
    }
}
