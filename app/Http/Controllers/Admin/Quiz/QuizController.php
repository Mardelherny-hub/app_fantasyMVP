<?php

namespace App\Http\Controllers\Admin\Quiz;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizCategory;
use App\Models\Question;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    /**
     * Display a listing of quizzes.
     */
    public function index()
    {
        $quizzes = Quiz::with('category.translations')
            ->withCount('quizQuestions')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.quiz.quizzes.index', compact('quizzes'));
    }

    /**
     * Show the form for creating a new quiz.
     */
    public function create()
    {
        $categories = QuizCategory::with('translations')->get();
        $types = Quiz::TYPES;
        $locales = ['es' => 'Español', 'en' => 'English', 'fr' => 'Français'];
        
        return view('admin.quiz.quizzes.create', compact('categories', 'types', 'locales'));
    }

    /**
     * Store a newly created quiz in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'exists:quiz_categories,id'],
            'type' => ['required', 'integer', 'in:1,2,3'],
            'title' => ['required', 'string', 'min:3', 'max:200'],
            'locale' => ['required', 'string', 'in:es,en,fr'],
            'questions_count' => ['required', 'integer', 'min:5', 'max:50'],
            'time_limit_sec' => ['required', 'integer', 'min:10', 'max:300'],
            'reward_amount' => ['required', 'numeric', 'min:0', 'max:999999'],
            'is_active' => ['boolean'],
        ]);

        DB::beginTransaction();
        
        try {
            // Crear quiz
            $quiz = Quiz::create([
                'category_id' => $validated['category_id'],
                'type' => $validated['type'],
                'title' => $validated['title'],
                'locale' => $validated['locale'],
                'questions_count' => $validated['questions_count'],
                'time_limit_sec' => $validated['time_limit_sec'],
                'reward_amount' => $validated['reward_amount'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.quiz.quizzes.edit', $quiz->id)
                ->with('success', 'Quiz creado exitosamente. Ahora asigna las preguntas.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Error al crear el quiz: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified quiz.
     */
    public function edit($locale, $quiz)
    {
        //dd($quiz);
        $quiz = Quiz::with(['category.translations', 'quizQuestions.question.translations'])
            ->findOrFail($quiz);
        
        $categories = QuizCategory::with('translations')->get();
        $types = Quiz::TYPES;
        $locales = ['es' => 'Español', 'en' => 'English', 'fr' => 'Français'];
        
        // Obtener preguntas disponibles según la categoría del quiz
        $availableQuestions = Question::with('translations')
            ->when($quiz->category_id, function($query) use ($quiz) {
                return $query->where('category_id', $quiz->category_id);
            })
            ->where('is_active', true)
            ->get();
        
        return view('admin.quiz.quizzes.edit', compact('quiz', 'categories', 'types', 'locales', 'availableQuestions'));
    }

    /**
     * Update the specified quiz in storage.
     */
    public function update(Request $request, $locale, $id)
    {
        //dd($id);
        $quiz = Quiz::findOrFail($id);

        $validated = $request->validate([
            'category_id' => ['nullable', 'exists:quiz_categories,id'],
            'type' => ['required', 'integer', 'in:1,2,3'],
            'title' => ['required', 'string', 'min:3', 'max:200'],
            'locale' => ['required', 'string', 'in:es,en,fr'],
            'questions_count' => ['required', 'integer', 'min:5', 'max:50'],
            'time_limit_sec' => ['required', 'integer', 'min:10', 'max:300'],
            'reward_amount' => ['required', 'numeric', 'min:0', 'max:999999'],
            'is_active' => ['boolean'],
        ]);

        DB::beginTransaction();
        
        try {
            // Actualizar quiz
            $quiz->update([
                'category_id' => $validated['category_id'],
                'type' => $validated['type'],
                'title' => $validated['title'],
                'locale' => $validated['locale'],
                'questions_count' => $validated['questions_count'],
                'time_limit_sec' => $validated['time_limit_sec'],
                'reward_amount' => $validated['reward_amount'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.quiz.quizzes.edit', $quiz->id)
                ->with('success', 'Quiz actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar el quiz: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified quiz from storage.
     */
    public function destroy($id)
    {
        $quiz = Quiz::findOrFail($id);

        DB::beginTransaction();
        
        try {
            // Eliminar preguntas asociadas (cascade automático)
            // Eliminar quiz
            $quiz->delete();

            DB::commit();

            return redirect()
                ->route('admin.quiz.quizzes.index')
                ->with('success', 'Quiz eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Error al eliminar el quiz: ' . $e->getMessage());
        }
    }

    /**
     * Assign questions to quiz.
     */
    public function assignQuestions(Request $request, $locale, $id)
    {
        $quiz = Quiz::findOrFail($id);

        $validated = $request->validate([
            'question_ids' => ['required', 'array', 'min:1'],
            'question_ids.*' => ['required', 'exists:questions,id'],
        ]);

        DB::beginTransaction();
        
        try {
            // Eliminar preguntas actuales
            $quiz->quizQuestions()->delete();

            // Asignar nuevas preguntas
            foreach ($validated['question_ids'] as $order => $questionId) {
                QuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question_id' => $questionId,
                    'order' => $order + 1,
                ]);
            }

            DB::commit();

            return back()->with('success', 'Preguntas asignadas exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Error al asignar preguntas: ' . $e->getMessage());
        }
    }

    /**
     * Auto-assign random questions to quiz based on criteria.
     */
    public function autoAssignQuestions(Request $request, $locale, $id)
    {
        $quiz = Quiz::findOrFail($id);

        $validated = $request->validate([
            'count' => ['required', 'integer', 'min:5', 'max:50'],
            'difficulty' => ['nullable', 'integer', 'in:1,2,3'],
        ]);

        DB::beginTransaction();
        
        try {
            // Construir query para obtener preguntas aleatorias
            $query = Question::where('is_active', true);

            if ($quiz->category_id) {
                $query->where('category_id', $quiz->category_id);
            }

            if (isset($validated['difficulty'])) {
                $query->where('difficulty', $validated['difficulty']);
            }

            $questions = $query->inRandomOrder()
                ->limit($validated['count'])
                ->pluck('id');

            if ($questions->count() < $validated['count']) {
                return back()->with('error', 'No hay suficientes preguntas disponibles con los criterios seleccionados.');
            }

            // Eliminar preguntas actuales
            $quiz->quizQuestions()->delete();

            // Asignar nuevas preguntas
            foreach ($questions as $order => $questionId) {
                QuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question_id' => $questionId,
                    'order' => $order + 1,
                ]);
            }

            DB::commit();

            return back()->with('success', "{$questions->count()} preguntas asignadas aleatoriamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Error al asignar preguntas: ' . $e->getMessage());
        }
    }

    /**
     * Toggle active status.
     */
    public function toggleActive($id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->update(['is_active' => !$quiz->is_active]);

        return back()->with('success', 'Estado actualizado exitosamente.');
    }
}