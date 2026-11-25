<?php

namespace App\Concerns;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use RuntimeException;

trait HandlesPropertyPhotos
{
    /**
     * Store an uploaded image in public/uploads and return its public URL.
     */
    protected function storeUploadedPhoto(UploadedFile $uploadedFile): string
    {
        $uploadDirectory = public_path('uploads');

        if (! is_dir($uploadDirectory) && ! mkdir($uploadDirectory, 0755, true) && ! is_dir($uploadDirectory)) {
            throw new RuntimeException('Não foi possível criar o diretório de uploads.');
        }

        $extension = mb_strtolower($uploadedFile->guessExtension() ?: '');

        if (! in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            $extension = mb_strtolower($uploadedFile->getClientOriginalExtension());
        }

        if (! in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            $extension = 'jpg';
        }

        if ($extension === 'jpeg') {
            $extension = 'jpg';
        }

        $filename = Str::random(40).'.'.$extension;
        $sourcePath = $uploadedFile->getRealPath() ?: $uploadedFile->getPathname();
        $destinationPath = $uploadDirectory.DIRECTORY_SEPARATOR.$filename;

        if (! is_string($sourcePath) || ! is_file($sourcePath) || ! @copy($sourcePath, $destinationPath)) {
            throw new RuntimeException('Não foi possível salvar a imagem enviada.');
        }

        return '/uploads/'.$filename;
    }

    /**
     * Resolve photos submitted through a traditional HTTP request.
     *
     * @param  array<int, mixed>  $removedPhotos
     * @return array<int, string>
     */
    protected function resolvePhotos(Request $request, mixed $submittedPhotos, array $removedPhotos = []): array
    {
        return $this->resolvePhotoFiles(
            $submittedPhotos,
            $removedPhotos,
            array_merge(
                Arr::wrap($request->file('photos', [])),
                Arr::wrap($request->file('photo', [])),
            ),
            trim((string) $request->input('photo_url', '')),
        );
    }

    /**
     * Resolve photos submitted through a Livewire component.
     *
     * @param  array<int, mixed>  $uploadedFiles
     * @param  array<int, mixed>  $removedPhotos
     * @return array<int, string>
     */
    protected function resolvePhotoFiles(
        mixed $submittedPhotos,
        array $removedPhotos = [],
        array $uploadedFiles = [],
        ?string $photoUrl = null,
    ): array {
        $removed = $this->stringList($removedPhotos);
        $photos = array_values(array_diff($this->stringList($submittedPhotos), $removed));

        foreach ($uploadedFiles as $uploadedPhoto) {
            if ($uploadedPhoto instanceof UploadedFile) {
                $photos[] = $this->storeUploadedPhoto($uploadedPhoto);
            }
        }

        $photoUrl = trim((string) ($photoUrl ?? ''));

        if ($photoUrl !== '' && ! in_array($photoUrl, $photos, true)) {
            $photos[] = $photoUrl;
        }

        if ($removed !== []) {
            $this->deleteLocalPhotos($removed);
        }

        return array_values(array_unique($photos));
    }

    /**
     * Delete locally uploaded files referenced by the given photo URLs.
     *
     * @param  array<int, string>  $photos
     */
    protected function deleteLocalPhotos(array $photos): void
    {
        foreach ($photos as $photo) {
            if (! is_string($photo) || ! str_starts_with($photo, '/uploads/')) {
                continue;
            }

            $fileName = basename($photo);

            if ($fileName === '' || $fileName === '.' || $fileName === '..') {
                continue;
            }

            $path = public_path('uploads'.DIRECTORY_SEPARATOR.$fileName);

            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    /**
     * @return array<int, string>
     */
    protected function stringList(mixed $value): array
    {
        return collect(is_array($value) ? $value : [$value])
            ->filter(fn (mixed $item): bool => is_string($item) && trim($item) !== '')
            ->map(fn (string $item): string => trim($item))
            ->values()
            ->all();
    }

    /**
     * Convert the newline separated features field into an array.
     *
     * @return array<int, string>
     */
    protected function featureList(mixed $value): array
    {
        if (is_string($value)) {
            return $this->stringList(preg_split('/\R+/u', $value) ?: []);
        }

        return is_array($value) ? $this->stringList($value) : [];
    }

    /**
     * Collect photo URLs explicitly submitted by the admin form.
     *
     * @param  array<string, mixed>  $validated
     * @return array<int, string>
     */
    protected function submittedPhotoUrls(array $validated): array
    {
        return $this->stringList(array_merge(
            Arr::wrap($validated['photo_urls'] ?? []),
            Arr::wrap($validated['existing_photos'] ?? []),
            Arr::wrap($validated['photos'] ?? []),
        ));
    }

    /**
     * Collect the photo URLs marked for removal by the admin form.
     *
     * @param  array<string, mixed>  $validated
     * @return array<int, string>
     */
    protected function removedPhotoUrls(array $validated): array
    {
        return $this->stringList(array_merge(
            Arr::wrap($validated['remove_photos'] ?? []),
            Arr::wrap($validated['removed_photos'] ?? []),
        ));
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    protected function propertyAttributes(array $validated): array
    {
        $attributes = Arr::except($validated, [
            'photos',
            'photo',
            'photo_url',
            'photo_urls',
            'existing_photos',
            'remove_photos',
            'removed_photos',
            'existingPhotos',
            'removedPhotos',
            'newPhotos',
            'photoUrl',
        ]);

        foreach (['condo_fee', 'iptu', 'area', 'lot_area', 'latitude', 'longitude'] as $decimalField) {
            if (($attributes[$decimalField] ?? null) === '') {
                $attributes[$decimalField] = null;
            }
        }

        foreach (['bedrooms', 'suite', 'bathrooms', 'parking'] as $integerField) {
            if (($attributes[$integerField] ?? null) === '') {
                $attributes[$integerField] = null;
            }
        }

        return $attributes;
    }
}
