<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AuthorController extends Controller
{
    /**
     * Obtener todos los autores
     */
    public function index()
{
    $authors = Author::select('id', 'name', 'profile_picture')->get();
    return response()->json([
        'success' => true,
        'data' => $authors
    ]);
}

    /**
     * Crear un nuevo autor
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        try {
            if ($request->hasFile('profile_picture')) {
                $path = $request->file('profile_picture')->store('authors', 'public');
                $validated['profile_picture'] = $path;
            }

            $author = Author::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Autor creado exitosamente',
                'data' => $author
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el autor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar un autor existente
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        try {
            $author = Author::findOrFail($id);

            if ($request->hasFile('profile_picture')) {
                // Eliminar imagen anterior si existe
                if ($author->profile_picture) {
                    Storage::disk('public')->delete($author->profile_picture);
                }
                $path = $request->file('profile_picture')->store('authors', 'public');
                $validated['profile_picture'] = $path;
            }

            $author->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Autor actualizado exitosamente',
                'data' => $author
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Autor no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el autor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un autor
     */
    public function destroy($id)
    {
        try {
            $author = Author::findOrFail($id);

            // Verificar si el autor tiene noticias asociadas
            if ($author->news()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el autor porque tiene noticias asociadas'
                ], 409);
            }

            // Eliminar imagen de perfil si existe
            if ($author->profile_picture) {
                Storage::disk('public')->delete($author->profile_picture);
            }

            $author->delete();

            return response()->json([
                'success' => true,
                'message' => 'Autor eliminado exitosamente'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Autor no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el autor',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
