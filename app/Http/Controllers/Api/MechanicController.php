<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mechanic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class MechanicController extends Controller
{
    public function index(Request $request)
    {
        $query = Mechanic::with('user');

        // Apply filters
        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->has('expertise')) {
            $query->whereJsonContains('expertise', $request->expertise);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        $mechanics = $query->where('is_verified', true)->get()->map(function ($m) {
            $m->logo_url = $m->logo_path ? Storage::url($m->logo_path) : null;
            $m->gallery_urls = collect((array) $m->gallery)->map(fn ($p) => Storage::url($p));
            return $m;
        });

        return response()->json([
            'success' => true,
            'mechanics' => $mechanics
        ]);
    }

    public function show($id)
    {
        $mechanic = Mechanic::with('user')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'mechanic' => $mechanic
        ]);
    }

    public function update(Request $request, $id)
    {
        $mechanic = $request->user()->mechanic ?? Mechanic::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:120',
            'country' => 'nullable|string|max:120',
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
            'services' => 'nullable|array',
            'expertise' => 'nullable|array',
            'experience_years' => 'nullable|integer|min:0|max:60',
            'hourly_rate' => 'nullable|numeric|min:0|max:10000',
            'bio' => 'nullable|string|max:2000',
            'availability' => 'nullable|in:available,busy,offline',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'gallery.*' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:4096',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('logo')) {
            if ($mechanic->logo_path) {
                Storage::disk('public')->delete($mechanic->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('mechanics/logos', 'public');
        }

        $gallery = (array) ($mechanic->gallery ?? []);
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $gallery[] = $file->store('mechanics/gallery', 'public');
            }
            $data['gallery'] = $gallery;
        }

        if (isset($data['lat'], $data['lng'])) {
            $data['geohash'] = base64_encode(pack('dd', (float) $data['lat'], (float) $data['lng']));
        }

        $mechanic->fill($data);
        $mechanic->save();

        $mechanic->refresh();
        $mechanic->logo_url = $mechanic->logo_path ? Storage::url($mechanic->logo_path) : null;
        $mechanic->gallery_urls = collect((array) $mechanic->gallery)->map(fn ($p) => Storage::url($p));

        return response()->json(['success' => true, 'mechanic' => $mechanic->load('user')]);
    }

    public function store(Request $request)
    {
        $mechanic = $request->user()->mechanic ?? Mechanic::create(['user_id' => $request->user()->id]);
        return $this->update($request, $mechanic->id);
    }
}
