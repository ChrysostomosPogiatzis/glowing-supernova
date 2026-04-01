<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Device;
use Inertia\Inertia;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Devices/Index', [
            'devices' => $request->user()->devices()->latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $request->user()->devices()->create([
            'name' => $validated['name'],
            'pairing_code' => strtoupper(Str::random(8)),
        ]);

        return redirect()->back();
    }

    public function show(Device $device)
    {
        // Security check
        if ($device->user_id !== auth()->id()) {
            abort(403);
        }

        // QR Data: JSON with pairing code and API base URI
        $qrData = json_encode([
            'code' => $device->pairing_code,
            'url' => url('/api'),
        ]);

        $qrCode = QrCode::size(300)->generate($qrData);

        return Inertia::render('Devices/Show', [
            'device' => $device,
            'qrCode' => (string)$qrCode,
            'qrData' => $qrData
        ]);
    }

    public function destroy(Device $device)
    {
        if ($device->user_id !== auth()->id()) {
            abort(403);
        }

        $device->delete();
        return redirect()->back();
    }
}
