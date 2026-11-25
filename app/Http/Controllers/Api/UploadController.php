<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

class UploadController extends Controller
{
    /**
     * Store an uploaded image and return its public URL.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $uploadedPhotos = array_merge(
            Arr::wrap($request->file('photo')),
            Arr::wrap($request->file('photos')),
            Arr::wrap($request->file('image')),
            Arr::wrap($request->file('file')),
        );

        $uploadedPhoto = collect($uploadedPhotos)
            ->first(fn (mixed $photo): bool => $photo instanceof UploadedFile);

        if ($uploadedPhoto === null) {
            return response()->json(['error' => 'Nenhuma imagem enviada.'], 422);
        }

        $url = $this->storeUploadedPhoto($uploadedPhoto);

        return response()->json([
            'ok' => true,
            'url' => $url,
            'path' => public_path(ltrim($url, '/')),
        ]);
    }
}
