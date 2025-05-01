<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function home(): View{
        return view('home');
    }

    public function generateExercises(Request $request){
        echo 'Gerar exercícios';
    }

    public function printExercises(){
        echo 'Apresentar os exercícios';
    }

    public function exportExercises(){
        echo 'Exportar os exercícios';
    }
}
