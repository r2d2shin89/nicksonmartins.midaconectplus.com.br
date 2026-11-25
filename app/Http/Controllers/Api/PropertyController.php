<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Models\Property;
use Illuminate\Http\JsonResponse;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'properties' => Property::latest()->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePropertyRequest $request): JsonResponse
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

        $property = Property::create($attributes);

        return response()->json(['property' => $property], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property): JsonResponse
    {
        return response()->json(['property' => $property]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePropertyRequest $request, Property $property): JsonResponse
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

        return response()->json(['property' => $property->fresh()]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property): JsonResponse
    {
        $this->deleteLocalPhotos($property->photos ?? []);
        $property->delete();

        return response()->json(['ok' => true]);
    }
}
