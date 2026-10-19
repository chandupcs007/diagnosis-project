<?php

namespace App\Services;

use App\Models\HospitalSetting;
use Illuminate\Support\Facades\Cache;

class HospitalSettingService
{
    protected const CACHE_KEY = 'hospital_setting.current';

    public function current(): HospitalSetting
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $setting = HospitalSetting::query()
                ->where('is_active', true)
                ->latest('id')
                ->first();

            if ($setting) {
                return $setting;
            }

            return new HospitalSetting($this->defaults());
        });
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public function update(array $data, ?string $logoPath = null): HospitalSetting
    {
        $setting = HospitalSetting::query()
            ->where('is_active', true)
            ->latest('id')
            ->first();

        $payload = [
            'hospital_name' => $data['hospital_name'],
            'department_name' => $data['department_name'] ?? null,
            'contact_phone' => $data['contact_phone'] ?? null,
            'contact_email' => $data['contact_email'] ?? null,
            'address' => $data['address'] ?? null,
            'website' => $data['website'] ?? null,
            'return_message' => $data['return_message'] ?? null,
            'is_active' => true,
        ];

        if ($logoPath) {
            $payload['logo_path'] = $logoPath;
        }

        if ($setting) {
            $setting->update($payload);
        } else {
            $setting = HospitalSetting::create($payload);
        }

        $this->clearCache();

        return $setting->fresh();
    }

    public function logoUrl(): ?string
    {
        $setting = $this->current();

        if (!$setting->logo_path || !\Illuminate\Support\Facades\Storage::disk('public')->exists($setting->logo_path)) {
            return null;
        }

        return '/storage/' . ltrim($setting->logo_path, '/');
    }

    public function defaults(): array
    {
        return [
            'hospital_name' => config('diagnosis.hospital'),
            'department_name' => config('diagnosis.department'),
            'contact_phone' => config('diagnosis.id_card.contact_phone'),
            'address' => config('diagnosis.id_card.address'),
            'website' => config('diagnosis.id_card.website'),
            'return_message' => config('diagnosis.id_card.return_message'),
            'is_active' => true,
        ];
    }
}
