<?php

namespace App\Http\Controllers;

use App\Models\Provincia;
use Illuminate\Http\Request;

class ProvinciaController extends Controller
{
    public function index()
    {
        return Provincia::with('departamento')->get();
    }

    public function store(Request $request)
    {
        return Provincia::create($request->all());
    }

    public function show($id)
    {
        return Provincia::with('departamento')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $prov = Provincia::findOrFail($id);
        $prov->update($request->all());
        return $prov;
    }

    public function destroy($id)
    {
        Provincia::destroy($id);
        return response()->json(null, 204);
    }
}