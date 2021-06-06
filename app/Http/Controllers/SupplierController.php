<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(file_get_contents(resource_path('data/suppliers.json')),200, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:suppliers',
            'info' => 'required',
            'rules' => 'required',
            'district' => 'required',
            'url' => 'required',
        ];


        // $validator = Validator::make(

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status'=>0,"message" =>$validator->messages()->all()],422,[
                'Content-Type' => 'application/json'
            ]);
        }

        $supplier = new Supplier();
        $supplier->name = $request->name;
        $supplier->info = $request->info;
        $supplier->rules = $request->rules;
        $supplier->district = $request->district;
        $supplier->url = $request->url;
        $supplier->save();

        return response()->json(['status'=>1],204,[
                    'Content-Type' => 'application/json'
                ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function show(Supplier $supplier)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit(Supplier $supplier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supplier $supplier)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function destroy(Supplier $supplier)
    {
        //
    }
}
