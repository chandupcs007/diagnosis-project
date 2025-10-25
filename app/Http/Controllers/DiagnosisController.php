<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DiagnosisController extends Controller
{
    /**
     * Display a listing of the diagnosis.
     */
    public function index()
    {
        return view('diagnosis.index');
    }

    /**
     * Show the form for creating a new diagnosis.
     */
    public function create()
    {
        return view('diagnosis.create');
    }

    /**
     * Store a newly created diagnosis in storage.
     */
    public function store(Request $request)
    {
        // Add your diagnosis storage logic here
        return redirect()->route('diagnosis.index')
            ->with('success', 'Diagnosis created successfully!');
    }

    // Add other methods as needed (show, edit, update, destroy)
}