<?php

namespace App\DiagnosticEcosystem\Services;

use App\DiagnosticEcosystem\Models\DeVehicleProfile;
use Illuminate\Support\Facades\Log;

class VehicleRegistrationService
{
    /**
     * Validate a VIN string (ISO 3779 / North American standard).
     */
    public function isValidVin(string $vin): bool
    {
        $vin = strtoupper(trim($vin));

        if (strlen($vin) !== 17) {
            return false;
        }

        if (preg_match('/[IOQ]/', $vin)) {
            return false;
        }

        if (! preg_match('/^[A-HJ-NPR-Z0-9]{17}$/', $vin)) {
            return false;
        }

        return true;
    }

    public function register(int $userId, array $data): DeVehicleProfile
    {
        $vin = strtoupper(trim($data['vin']));

        $profile = DeVehicleProfile::create([
            'user_id' => $userId,
            'vin' => $vin,
            'license_plate' => $data['license_plate'] ?? null,
            'nickname' => $data['nickname'] ?? null,
            'current_mileage' => $data['current_mileage'] ?? null,
            'legacy_car_id' => $data['legacy_car_id'] ?? null,
            'status' => 'active',
        ]);

        Log::info('Vehicle profile registered', [
            'profile_id' => $profile->id,
            'user_id' => $userId,
            'vin' => $vin,
        ]);

        return $profile;
    }

    public function update(DeVehicleProfile $profile, array $data): DeVehicleProfile
    {
        $fillable = ['license_plate', 'nickname', 'current_mileage'];

        $profile->update(array_intersect_key($data, array_flip($fillable)));

        return $profile->fresh();
    }

    public function archive(DeVehicleProfile $profile): DeVehicleProfile
    {
        $profile->update(['status' => 'archived']);

        return $profile;
    }

    public function listForUser(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return DeVehicleProfile::where('user_id', $userId)
            ->with('latestVinDecode')
            ->active()
            ->orderByDesc('updated_at')
            ->get();
    }
}
