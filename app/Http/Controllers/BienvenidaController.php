<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BienvenidaController extends Controller
{
    public function index()
    {
        return view('bienvenida'); // tu vista bienvenida.blade.php
    }
}