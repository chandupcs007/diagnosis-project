<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserPhotoService
{
    public function savePhotoFromBase64(User $user, string $base64Data): string
    {
        if (str_contains($base64Data, ',')) {
            $base64Data = explode(',', $base64Data, 2)[1];
        }

        $binary = base64_decode($base64Data, true);

        if ($binary === false) {
            throw new \InvalidArgumentException('Invalid photo data provided.');
        }

        if ($user->photo_path) {
            Storage::disk('public')->delete($user->photo_path);
        }

        $path = 'users/photos/' . $user->id . '_' . Str::uuid() . '.jpg';
        Storage::disk('public')->put($path, $binary);

        $user->update(['photo_path' => $path]);

        return $path;
    }

    public function photoUrl(?string $photoPath): ?string
    {
        if (!$photoPath || !Storage::disk('public')->exists($photoPath)) {
            return null;
        }

        return '/storage/' . ltrim($photoPath, '/');
    }
}
