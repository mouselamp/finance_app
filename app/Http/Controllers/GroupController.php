<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Display the group management page.
     */
    public function index()
    {
        return view('groups.index');
    }
}
