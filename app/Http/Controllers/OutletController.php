<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Outlet;
use App\Models\Company;
use Inertia\Inertia;

class OutletController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $outlets = Outlet::with('company')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('company', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->get();

        return Inertia::render('Outlets/Index', [
            'outlets' => $outlets,
            'companies' => Company::all(),
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        Outlet::create($validated);

        return redirect()->back();
    }

    public function update(Request $request, Outlet $outlet)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        $outlet->update($validated);

        return redirect()->back();
    }

    public function destroy(Outlet $outlet)
    {
        $outlet->delete();
        return redirect()->back();
    }
}
