<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\variables;
use Illuminate\Support\Facades\DB;

class VariablesController extends Controller
{
    public function get($key)
    {
        return  variables::where('key', $key)->first()['value'];
    }


    public function store(Request $request)
    {
        $input=$request->all();
        if (variables::where('key', $input['key'])->exists()) {
            $var=variables::where('key', $input['key'])->first();
            $var['value'] = $input['value'];
            
            $var->save();
        } else
        {
        variables::create($input);   
        }}
}
