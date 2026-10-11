<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Breed;
use App\Models\District;
use App\Models\Province;
use App\Models\Species;
use App\Models\Subdistrict;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function provinces()
    {
        return Province::query()->orderBy('name_th')->paginate(100);
    }

    public function province(Province $province)
    {
        return $province;
    }

    public function districts(Request $request)
    {
        return District::query()
            ->when($request->filled('province_id'), fn ($query) => $query->where('province_id', $request->input('province_id')))
            ->orderBy('name_th')
            ->paginate(100);
    }

    public function district(District $district)
    {
        return $district;
    }

    public function subdistricts(Request $request)
    {
        return Subdistrict::query()
            ->when($request->filled('district_id'), fn ($query) => $query->where('district_id', $request->input('district_id')))
            ->orderBy('name_th')
            ->paginate(100);
    }

    public function subdistrict(Subdistrict $subdistrict)
    {
        return $subdistrict;
    }

    public function species()
    {
        return Species::query()->with('breeds')->orderBy('name')->paginate(100);
    }

    public function speciesItem(Species $species)
    {
        return $species->load('breeds');
    }

    public function breeds(Request $request)
    {
        return Breed::query()
            ->with('species')
            ->when($request->filled('species_id'), fn ($query) => $query->where('species_id', $request->input('species_id')))
            ->orderBy('name')
            ->paginate(100);
    }

    public function breed(Breed $breed)
    {
        return $breed->load('species');
    }
}
