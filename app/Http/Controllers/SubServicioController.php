<?php

namespace App\Http\Controllers;

use App\Models\SubServicio;
use Illuminate\Http\Request;

class SubServicioController extends Controller
{
    public function index()
    {
        return SubServicio::with('categoria')->get();
    }

    public function store(Request $request)
    {
        $sub = SubServicio::create($request->all());
        return response()->json($sub, 201);
    }

    public function show($id)
    {
        return SubServicio::with('categoria')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $sub = SubServicio::findOrFail($id);
        $sub->update($request->all());
        return response()->json($sub);
    }

    public function destroy($id)
    {
        SubServicio::destroy($id);
        return response()->json(null, 204);
    }
}