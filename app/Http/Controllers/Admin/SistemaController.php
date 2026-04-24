<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class SistemaController extends Controller
{
    public function index()
    {
        return view('admin.sistema.index');
    }
}