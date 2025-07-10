<?php
namespace App\Http\Controllers;

use App\Models\CategoriaServicio;
use Illuminate\Http\Request;

class CategoriaServicioController extends Controller
{
    public function index()
    {
        return CategoriaServicio::all();
    }

    public function store(Request $request)
    {
        $categoria = CategoriaServicio::create($request->all());
        return response()->json($categoria, 201);
    }

    public function show($id)
    {
        return CategoriaServicio::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $categoria = CategoriaServicio::findOrFail($id);
        $categoria->update($request->all());
        return response()->json($categoria);
    }

    public function destroy($id)
    {
        CategoriaServicio::destroy($id);
        return response()->json(null, 204);
    }
}