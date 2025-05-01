<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function home(): View
    {
        return view('home');
    }

    public function generateExercises(Request $request): View
    {
        //form validation
        $request->validate([
            'check_sum' => 'required_without_all:check_subtraction,check_multiplication,check_division',
            'check_subtraction' => 'required_without_all:check_sum,check_multiplication,check_division',
            'check_multiplication' => 'required_without_all:check_sum,check_subtraction,check_division',
            'check_division' => 'required_without_all:check_sum,check_subtraction,check_multiplication',
            'number_one' => 'required|integer|min:0|max:999|lt:number_two',
            'number_two' => 'required|integer|min:0|max:999',
            'number_exercises' => 'required|integer|min:5|max:50'
        ]);


        // get operações selecionadas
        $operations = [];
        if ($request->check_sum) {
            $operations[] = 'sum';
        };
        if ($request->subtraction) {
            $operations[] = 'subtraction';
        };
        if ($request->check_multiplication) {
            $operations[] = 'multiplication';
        };
        if ($request->check_division) {
            $operations[] = 'division';
        };

        // get numeros (min and max)
        $min = $request->number_one;
        $max = $request->number_two;

        // get do numero de exercicios
        $numberExercises = $request->number_exercises;

        // gerar exercicios
        $exercises = [];
        for ($index = 1; $index <= $numberExercises; $index++) {
            $exercises[] = $this->generateExercise($index, $operations, $min, $max);
        }

        //guardar os dados na sessão
        session(['exercises' => $exercises ]);

        return view('operations', ['exercises' => $exercises]);
    }

    public function printExercises()
    {
        // Checkar se os exercicios estão na sessão
        if(!session()->has('exercises')){
            return redirect()->route('home');
        }

        $exercises = session('exercises');
        echo '<pre>';
        echo '<h1>Exercícios de Matemática (' .env('APP_NAME') . ')</h1>';
        echo '<hr>';

        foreach($exercises as $exercise){
            echo '<h2><small>'. str_pad($exercise['exercise_number'], 2, "0", STR_PAD_LEFT) . ' - </small> ' . $exercise['exercise'] . '</h2>';
        }

        //Soluções
        echo '<hr>';
        echo '<small>Soluções</small><br>';
        foreach($exercises as $exercise){
            echo '<small>'. str_pad($exercise['exercise_number'], 2, "0", STR_PAD_LEFT) . ' - ' . $exercise['sollution'] . '</small><br>';
        }

    }

    public function exportExercises()
    {
        echo 'Exportar os exercícios';
    }

    private function generateExercise($index, $operations, $min, $max)
    {
        $operation = $operations[array_rand($operations)];
        $number1 = rand($min, $max);
        $number2 = rand($min, $max);

        $exercise = '';
        $sollution = '';

        switch ($operation) {
            case 'sum':
                $exercise = "$number1 + $number2 =";
                $sollution = $number1 + $number2;
                break;
            case 'subtraction':
                $exercise = "$number1 - $number2 =";
                $sollution = $number1 - $number2;
                break;
            case 'multiplication':
                $exercise = "$number1 X $number2 =";
                $sollution = $number1 * $number2;
                break;
            case 'division':

                //evitar divisão por zero
                if ($number2 == 0) {
                    $number2 = 1;
                }
                $exercise = "$number1 : $number2 =";
                $sollution = $number1 / $number2;
                break;
        }

        // Transformar o número float da solução em 2 casas decimais.
        if (is_float($sollution)) {
            $sollution = round($sollution, 2);
        }

        return [
            'operation' => $operation,
            'exercise_number' => $index,
            'exercise' => $exercise,
            'sollution' => "$exercise $sollution"
        ];
    }
}
