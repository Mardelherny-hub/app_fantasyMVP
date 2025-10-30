<?php

namespace App\Http\Controllers\Admin\Quiz;

use App\Http\Controllers\Controller;
use App\Models\QuizCategory;
use App\Models\QuizCategoryTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = QuizCategory::with('translations')
            ->withCount('questions')
            ->orderBy('code')
            ->get();
        
        return view('admin.quiz.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('admin.quiz.categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:quiz_categories,code', 'alpha_dash'],
            'translations' => ['required', 'array', 'min:1'],
            'translations.es' => ['required', 'string', 'min:3', 'max:100'],
            'translations.en' => ['nullable', 'string', 'min:3', 'max:100'],
            'translations.fr' => ['nullable', 'string', 'min:3', 'max:100'],
        ]);

        DB::beginTransaction();
        
        try {
            // Crear categoría
            $category = QuizCategory::create([
                'code' => $validated['code'],
            ]);

            // Crear traducciones
            foreach ($validated['translations'] as $locale => $name) {
                if (!empty($name)) {
                    QuizCategoryTranslation::create([
                        'quiz_category_id' => $category->id,
                        'locale' => $locale,
                        'name' => $name,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.quiz.categories.index')
                ->with('success', 'Categoría creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Error al crear la categoría: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit($locale, $category)
    {
        //dd($locale, $category);
        $category = QuizCategory::with('translations')->findOrFail($category);
        
        return view('admin.quiz.categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, $id)
    {
        $category = QuizCategory::findOrFail($id);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:quiz_categories,code,' . $id],
            'translations' => ['required', 'array', 'min:1'],
            'translations.es' => ['required', 'string', 'min:3', 'max:100'],
            'translations.en' => ['nullable', 'string', 'min:3', 'max:100'],
            'translations.fr' => ['nullable', 'string', 'min:3', 'max:100'],
        ]);

        DB::beginTransaction();
        
        try {
            // Actualizar código
            $category->update([
                'code' => $validated['code'],
            ]);

            // Actualizar traducciones
            foreach ($validated['translations'] as $locale => $name) {
                if (!empty($name)) {
                    QuizCategoryTranslation::updateOrCreate(
                        [
                            'quiz_category_id' => $category->id,
                            'locale' => $locale,
                        ],
                        ['name' => $name]
                    );
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.quiz.categories.index')
                ->with('success', 'Categoría actualizada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar la categoría: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy($id)
    {
        $category = QuizCategory::findOrFail($id);

        // Verificar si tiene preguntas asociadas
        if ($category->questions()->count() > 0) {
            return back()->with('error', 'No se puede eliminar una categoría que tiene preguntas asociadas.');
        }

        DB::beginTransaction();
        
        try {
            // Eliminar traducciones
            $category->translations()->delete();
            
            // Eliminar categoría
            $category->delete();

            DB::commit();

            return redirect()
                ->route('admin.quiz.categories.index')
                ->with('success', 'Categoría eliminada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Error al eliminar la categoría: ' . $e->getMessage());
        }
    }
}