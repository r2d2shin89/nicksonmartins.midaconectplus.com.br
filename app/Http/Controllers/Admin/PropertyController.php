<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Models\Property;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('admin.properties.index', [
            'properties' => Property::latest()->paginate(12),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.properties.form', ['property' => null]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePropertyRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $attributes = $this->propertyAttributes($validated);

        $defaults = [
            'condo_fee' => 0,
            'iptu' => 0,
            'area' => 0,
            'bedrooms' => 0,
            'suite' => 0,
            'bathrooms' => 0,
            'parking' => 0,
            'address' => '',
            'city' => '',
            'state' => '',
            'description' => '',
        ];

        foreach ($defaults as $field => $default) {
            $attributes[$field] ??= $default;
        }

        $attributes['featured'] = (bool) ($attributes['featured'] ?? false);
        $attributes['status'] = $attributes['status'] ?? 'disponivel';
        $attributes['features'] = $this->featureList($validated['features'] ?? null);
        $attributes['photos'] = $this->resolvePhotos(
            $request,
            $this->submittedPhotoUrls($validated),
            $this->removedPhotoUrls($validated),
        );

        Property::create($attributes);

        return redirect()->route('admin.properties.index')->with('status', 'Imóvel criado com sucesso.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Property $property): View
    {
        return view('admin.properties.form', ['property' => $property]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePropertyRequest $request, Property $property): RedirectResponse
    {
        $validated = $request->validated();
        $attributes = $this->propertyAttributes($validated);

        if (array_key_exists('featured', $attributes)) {
            $attributes['featured'] = (bool) $attributes['featured'];
        }

        if (array_key_exists('features', $validated)) {
            $attributes['features'] = $this->featureList($validated['features']);
        }

        $hasSubmittedPhotos = array_key_exists('photos', $validated)
            || array_key_exists('photo_urls', $validated)
            || array_key_exists('existing_photos', $validated);

        $submittedPhotos = $hasSubmittedPhotos
            ? $this->submittedPhotoUrls($validated)
            : ($property->photos ?? []);

        $attributes['photos'] = $this->resolvePhotos(
            $request,
            $submittedPhotos,
            $this->removedPhotoUrls($validated),
        );

        $property->update($attributes);

        return redirect()->route('admin.properties.index')->with('status', 'Imóvel atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property): RedirectResponse
    {
        $this->deleteLocalPhotos($property->photos ?? []);
        $property->delete();

        return redirect()->route('admin.properties.index')->with('status', 'Imóvel removido com sucesso.');
    }
}
