<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Device;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;

class SyncController extends Controller
{
    /**
     * Pair a device using a code.
     */
    public function pair(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'device_name' => 'nullable|string',
        ]);

        $device = Device::where('pairing_code', $validated['code'])->first();

        if (!$device) {
            return response()->json(['message' => 'Invalid pairing code.'], 404);
        }

        // Generate a token for the user associated with this device
        $token = $device->user->createToken($device->name . ' Mobile')->plainTextToken;

        // Clear the pairing code so it can't be used again
        $device->update(['pairing_code' => null]);

        return response()->json([
            'token' => $token,
            'device' => $device,
            'user' => $device->user,
        ]);
    }

    /**
     * Sync contacts from mobile to platform.
     */
    public function sync(Request $request)
    {
        $user = $request->user();
        $contacts = $request->input('contacts', []);

        $syncedCount = 0;

        foreach ($contacts as $contact) {
            // Very basic matching logic: check by email or mobile
            $customer = Customer::where('mobile_number', $contact['mobile'])
                ->orWhere('email', $contact['email'] ?? null)
                ->first();

            if (!$customer) {
                Customer::create([
                    'name' => $contact['name'] ?? 'Mobile',
                    'surname' => $contact['surname'] ?? 'Contact',
                    'mobile_number' => $contact['mobile'],
                    'email' => $contact['email'] ?? null,
                ]);
                $syncedCount++;
            }
        }

        // Update last sync for devices?
        // We'd need to know which device called this.
        // Sanctum's currentAccessToken() has a name or we can hit the Device manually if we tracked it.
        
        return response()->json([
            'count' => $syncedCount,
            'message' => 'Contacts synced successfully.',
            'platform_contacts' => Customer::with(['company', 'outlet'])->get(),
        ]);
    }
}
