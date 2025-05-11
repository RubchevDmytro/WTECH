<?php
namespace App\Http\Controllers;

abstract class Controller
{
public function __construct()
{
    $categories = \App\Models\Category::all();
    view()->share('categories', $categories);
}    //
}
