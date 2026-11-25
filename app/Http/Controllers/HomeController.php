<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private const SORT_OPTIONS = ['recent', 'price-asc', 'price-desc', 'area'];

    /**
     * Display the property listing with the requested filters.
     */
    public function index(Request $request): View
    {
        $filters = [
            'purpose' => $request->string('purpose')->toString(),
            'type' => $request->string('type')->toString(),
            'city' => $request->string('city')->toString(),
            'sort' => $request->string('sort')->toString(),
        ];

        $filters['sort'] = in_array($filters['sort'], self::SORT_OPTIONS, true)
            ? $filters['sort']
            : 'recent';

        $propertiesQuery = Property::query()->where('status', '!=', 'vendido');

        if ($filters['purpose'] !== '') {
            $propertiesQuery->where('purpose', $filters['purpose']);
        }

        if ($filters['type'] !== '') {
            $propertiesQuery->where('type', $filters['type']);
        }

        if ($filters['city'] !== '') {
            $searchTerm = '%'.$filters['city'].'%';

            $propertiesQuery->where(function (Builder $query) use ($searchTerm): void {
                $query->where('city', 'like', $searchTerm)
                    ->orWhere('neighborhood', 'like', $searchTerm)
                    ->orWhere('address', 'like', $searchTerm)
                    ->orWhere('zip_code', 'like', $searchTerm)
                    ->orWhere('title', 'like', $searchTerm);
            });
        }

        match ($filters['sort']) {
            'price-asc' => $propertiesQuery->orderBy('price'),
            'price-desc' => $propertiesQuery->orderByDesc('price'),
            'area' => $propertiesQuery->orderByDesc('area'),
            default => $propertiesQuery->latest(),
        };

        $properties = $propertiesQuery->orderByDesc('id')->get();

        return view('home', [
            'properties' => $properties,
            'filters' => $filters,
            'broker' => config('broker'),
        ]);
    }
}
