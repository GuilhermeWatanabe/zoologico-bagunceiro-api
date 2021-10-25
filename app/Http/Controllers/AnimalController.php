<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class AnimalController extends Controller
{
    public function __construct()
    {
        $this->validationrules = [
            'nickname' => 'required|string',
            'scientific_name' => 'required|string',
            'password' => 'required|string',
            'zoo_wing' => 'required|string',
            'image' => 'required|image'
        ];
        $this->validationMessages = [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute não é um nome/texto válido.',
            'image' => 'A imagem não é válida.'
        ];
        $this->validationAttributes = [
            'nickname' => 'apelido',
            'scientific_name' => 'nome científico',
            'password' => 'senha',
            'zoo_wing' => 'ala do zoológico',
            'image' => 'imagem'
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
        //validation
        $validator = Validator::make($request->all(), $this->validationrules, $this->validationMessages, $this->validationAttributes);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //image upload to imgur
        $response = Http::withHeaders([
            'Authorization' => 'Client-ID 599b2d427ea9e85'
        ])->post('https://api.imgur.com/3/image', [
            'image' => base64_encode(file_get_contents($request->image->path()))
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Falha ao fazer upload do arquivo.'], 500);
        }

        return response()->json(
            Animal::create(array_merge(
                $request->only(
                    'nickname',
                    'scientific_name',
                    'password',
                    'zoo_wing'
                ),
                ['image_url' => $response->json('data')['link']]
            )),
            201
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
