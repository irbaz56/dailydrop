<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class AppController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    
    }

    public function healthCheck(Request $request)
    {

        DB::connection(env('DB_CONNECTION', 'mysql'))->getPdo();

        return response()->json(['outcome' => 'success']);
    }


}
