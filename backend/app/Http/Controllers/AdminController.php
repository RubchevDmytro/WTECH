<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
public function index(Request $request)
    {
        \Log::info('AdminController: Rendering admin menu', [
            'user_id' => auth()->id(),
            'path' => $request->path(),
        ]);

        return view('admin.admin_menu');
}

}
