<?php

namespace App\Http\Controllers;

use App\Models\AgendaUsuario;
use Illuminate\Http\Request;

class AgendaUsuarioController extends Controller
{
    public function index()
    {
        return AgendaUsuario::with('usuario')->get();
    }gi

    public function store(Request $request)
    {
        return AgendaUsuario::create($request->all());
    }

    public function show($id)
    {
        return AgendaUsuario::with('usuario')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $agenda = AgendaUsuario::findOrFail($id);
        $agenda->update($request->all());
        return $agenda;
    }

    public function destroy($id)
    {
        AgendaUsuario::destroy($id);
        return response()->json(null, 204);
    }
}