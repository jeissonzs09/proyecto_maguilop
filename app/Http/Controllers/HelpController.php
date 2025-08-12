<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelpController extends Controller
{
    public function index()
    {
        // Aquí puedes pasar los manuales o links que mostrarás en la vista
        return view('help.index');
    }
}