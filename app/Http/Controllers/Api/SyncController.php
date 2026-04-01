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
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'code' => 'required|string',
            'device_name' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        $device = Device::where('pairing_code', $validated['code'])->first();

        if (!$device) {
            return response()->json(['message' => 'Invalid or already used pairing code.'], 404);
        }

        // Generate a token for the user, tagging it with the device ID for tracking
        $token = $device->user->createToken('Device:' . $device->id)->plainTextToken;

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
            $contact['mobile'] = trim($contact['mobile']);

            // Validation: skip if mobile number is missing
            if (empty($contact['mobile'])) {
                continue;
            }

            try {
                // Better matching logic: only check email if it's provided
                $query = Customer::where('mobile_number', $contact['mobile']);
                
                if (!empty($contact['email'])) {
                    $query->orWhere('email', $contact['email']);
                }
                
                $customer = $query->first();

                if (!$customer) {
                    Customer::create([
                        'user_id' => $user->id,
                        'name' => $contact['name'] ?? 'Mobile',
                        'surname' => $contact['surname'] ?? 'Contact',
                        'mobile_number' => $contact['mobile'],
                        'email' => $contact['email'] ?? null,
                    ]);
                    $syncedCount++;
                }
            } catch (\Exception $e) {
                \Log::error('Failed to sync contact', [
                    'contact' => $contact,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Update last sync for the specific device that performed the sync
        $token = $request->user()->currentAccessToken();
        if ($token && str_starts_with($token->name, 'Device:')) {
            $deviceId = str_replace('Device:', '', $token->name);
            Device::where('id', $deviceId)->update(['last_sync_at' => now()]);
        }
        
        return response()->json([
            'count' => $syncedCount,
            'message' => 'Contacts synced successfully.',
            'platform_contacts' => Customer::with(['company', 'outlet', 'user'])->get(),
        ]);
    }
}
