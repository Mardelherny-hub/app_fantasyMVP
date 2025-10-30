<?php

namespace App\Http\Controllers\Admin\Quiz;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionTranslation;
use App\Models\QuestionOption;
use App\Models\QuestionOptionTranslation;
use App\Models\QuizCategory;
use App\Http\Requests\Admin\Quiz\QuestionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{   

    /**
     * Display a listing of questions.
     */
    public function index(Request $request)
    {
        $query = Question::with(['category.translations', 'translations']);

        // Filtro por categoría
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filtro por dificultad
        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('translations', function($q) use ($search) {
                $q->where('text', 'like', '%' . $search . '%');
            });
        }

        $questions = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        
        $categories = QuizCategory::with('translations')->get();
        
        return view('admin.quiz.questions.index', compact('questions', 'categories'));
    }

    

    /**
     * Show the form for creating a new question.
     */
    public function create()
    {
        $categories = QuizCategory::with('translations')->get();
        $difficulties = Question::DIFFICULTIES;
        
        return view('admin.quiz.questions.create', compact('categories', 'difficulties'));
    }

    /**
     * Store a newly created question in storage.
     */
    public function store(QuestionRequest $request)
    {
        DB::beginTransaction();
        
        try {
            // Crear pregunta
            $question = Question::create([
                'category_id' => $request->category_id,
                'difficulty' => $request->difficulty,
                'is_active' => $request->is_active ?? true,
                'meta' => $request->meta,
            ]);

            // Crear traducciones de la pregunta
            foreach ($request->translations as $locale => $text) {
                if (!empty($text)) {
                    QuestionTranslation::create([
                        'question_id' => $question->id,
                        'locale' => $locale,
                        'text' => $text,
                    ]);
                }
            }

            // Crear opciones con sus traducciones
            foreach ($request->options as $index => $optionData) {
                $option = QuestionOption::create([
                    'question_id' => $question->id,
                    'is_correct' => $optionData['is_correct'] ?? false,
                    'order' => $index + 1,
                ]);

                // Traducciones de la opción
                foreach ($optionData['translations'] as $locale => $text) {
                    if (!empty($text)) {
                        QuestionOptionTranslation::create([
                            'question_option_id' => $option->id,
                            'locale' => $locale,
                            'text' => $text,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.quiz.questions.index')
                ->with('success', 'Pregunta creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Error al crear la pregunta: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified question.
     */
    public function show($question)
    {
        $question = Question::with([
            'category.translations',
            'translations',
            'options.translations'
        ])->findOrFail($question);
        
        return view('admin.quiz.questions.show', compact('question'));
    }
    
    /**
     * Show the form for editing the specified question.
     */
    public function edit($locale, $id)
    {
        $question = Question::with([
            'translations',
            'options.translations'
        ])->findOrFail($id);
        
        $categories = QuizCategory::with('translations')->get();
        $difficulties = Question::DIFFICULTIES;
        
        return view('admin.quiz.questions.edit', compact('question', 'categories', 'difficulties'));
    }

    /**
     * Update the specified question in storage.
     */
    public function update(QuestionRequest $request, Question $question)
    {
        DB::beginTransaction();
        
        try {
            // Actualizar pregunta
            $question->update([
                'category_id' => $request->category_id,
                'difficulty' => $request->difficulty,
                'is_active' => $request->is_active ?? true,
                'meta' => $request->meta,
            ]);

            // Actualizar traducciones de la pregunta
            foreach ($request->translations as $locale => $text) {
                if (!empty($text)) {
                    QuestionTranslation::updateOrCreate(
                        [
                            'question_id' => $question->id,
                            'locale' => $locale,
                        ],
                        ['text' => $text]
                    );
                }
            }

            // Eliminar opciones existentes y recrear (más simple que actualizar)
            $question->options()->delete();

            // Crear nuevas opciones
            foreach ($request->options as $index => $optionData) {
                $option = QuestionOption::create([
                    'question_id' => $question->id,
                    'is_correct' => $optionData['is_correct'] ?? false,
                    'order' => $index + 1,
                ]);

                // Traducciones de la opción
                foreach ($optionData['translations'] as $locale => $text) {
                    if (!empty($text)) {
                        QuestionOptionTranslation::create([
                            'question_option_id' => $option->id,
                            'locale' => $locale,
                            'text' => $text,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.quiz.questions.index')
                ->with('success', 'Pregunta actualizada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar la pregunta: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified question from storage (soft delete).
     */
    public function destroy(Question $question)
    {
        try {
            $question->delete();
            
            return redirect()
                ->route('admin.quiz.questions.index')
                ->with('success', 'Pregunta eliminada exitosamente.');
                
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error al eliminar la pregunta: ' . $e->getMessage());
        }
    }

    /**
     * Toggle active status of the question.
     */
    public function toggleActive($id)
    {
        try {
            $question = Question::findOrFail($id);
            
            $question->update([
                'is_active' => !$question->is_active
            ]);
            
            $status = $question->is_active ? 'activada' : 'desactivada';
            
            return back()->with('success', "Pregunta {$status} exitosamente.");
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cambiar el estado: ' . $e->getMessage());
        }
    }
}