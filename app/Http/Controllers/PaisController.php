<?php

namespace App\Http\Controllers;

use App\Models\Pais;
use Illuminate\Http\Request;

class PaisController extends Controller
{
    public function index()
    {
        return Pais::with('departamentos')->get();
    }

    public function store(Request $request)
    {
        return Pais::create($request->all());
    }

    public function show($id)
    {
        return Pais::with('departamentos')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $pais = Pais::findOrFail($id);
        $pais->update($request->all());
        return $pais;
    }

    public function destroy($id)
    {
        Pais::destroy($id);
        return response()->json(null, 204);
    }
}