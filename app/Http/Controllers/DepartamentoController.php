<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    public function index()
    {
        return Departamento::with('provincias', 'pais')->get();
    }

    public function store(Request $request)
    {
        return Departamento::create($request->all());
    }

    public function show($id)
    {
        return Departamento::with('provincias')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $dpto = Departamento::findOrFail($id);
        $dpto->update($request->all());
        return $dpto;
    }

    public function destroy($id)
    {
        Departamento::destroy($id);
        return response()->json(null, 204);
    }
}