<?php

namespace App\Http\Controllers;

use App\Desafio;
use App\Equipo;
use App\Disciplina;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DesafioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Desafio::where("visible", true)->get(); // en aqui se dice que muestre los datos que tienen
        //el campo visible con valor true
        ////////////////////////////////////////////////////////////////////////
        $compilado = array();
        foreach($data as $item) {
            $xrow = array(
                "id" => $item->id,
                "disciplina" => Disciplina::find($item->disciplina_id),//en aqui se traen los datos de disciplina
                "invitado" => Equipo::find($item->invitado_id),//en aqui se traen los datos de la tabla equipo
                "retador" => Equipo::find($item->retador_id),
                "invitado_puntaje" => $item->invitado_puntaje,
                "retador_puntaje" => $item->retador_puntaje,
                "fecha" => $item->fecha,
                "fase" => $item->fase,
                "estado" => $item->estado  
            );
            array_push($compilado, $xrow);
        }

        return response()->json(array("status" => true, "objects" => $compilado));// en aqui se dice que se traigan los datos
        //del array compilado y mostrarlos

    }

    public function indexByDisciplina(Request $request, $disciplina_id)
    {
        $disc = $disciplina_id;
        $data = Desafio::where([["visible", "=", true], ["disciplina_id", "=",$disc], ["invitado_puntaje", ">","retador_puntaje"]])->get();
        $compilado = array();
        foreach($data as $item) {
            $xrow = array(
                "id" => $item->id,
                "disciplina" => Disciplina::find($item->disciplina_id),
                "invitado" => Equipo::find($item->invitado_id),
                "retador" => Equipo::find($item->retador_id),
                "invitado_puntaje" => $item->invitado_puntaje,
                "retador_puntaje" => $item->retador_puntaje,
                "fecha" => $item->fecha,
                "fase" => $item->fase,
                "ganador" => Equipo::find($item->ganador_id)
            );
            array_push($compilado, $xrow);
        }

        return response()->json(array("status" => true, "objects" => $compilado));
    }

    public function indexRepoterGanador(Request $request, $disciplina_id)
    {
        $disc = $disciplina_id;
        $q1 = Desafio::select('disciplinas.nombre as nombrea')
                ->join('equipos', 'desafios.invitado_id', '=', 'equipos.id')
                ->join('disciplinas', 'desafios.disciplina_id', '=', 'disciplinas.id')
                ->where([["desafios.disciplina_id", "=",$disc],["desafios.invitado_puntaje", ">","desafios.retador_puntaje"]])
                ->get();
        $q2 = Desafio::select('disciplinas.nombre as nombrea', 'equipos.nombre as nombreq')
                ->join('equipos', 'desafios.retador_id', '=', 'equipos.id')
                ->join('disciplinas', 'desafios.disciplina_id', '=', 'disciplinas.id')
                ->where([["desafios.disciplina_id", "=",$disc],["retador_puntaje", ">","invitado_puntaje"]])
                ->get();
                $resultado = [];

foreach($q1 as $r1) {
    array_push($resultado, $r1);
}

foreach($q2 as $r2) {
    array_push($resultado, $r2);
}
                return response()->json(array("status" => true, "objects" => $resultado));
    }
    /*public function indexByFase(Request $request, $fase)
    {
        $fas = $fase;
        $data = Desafio::where([["visible", "=", true], ["fase", "=",$fas]])->get();
        $compilado = array();
        foreach($data as $item) {
            $xrow = array(
                "id" => $item->id,
                "disciplina" => Disciplina::find($item->disciplina_id),
                "invitado" => Equipo::find($item->invitado_id),
                "retador" => Equipo::find($item->retador_id),
                "invitado_puntaje" => $item->invitado_puntaje,
                "retador_puntaje" => $item->retador_puntaje,
                "fecha" => $item->fecha,
                "fase" => $item->fase,
            );
            array_push($compilado, $xrow);
        }

        return response()->json(array("status" => true, "objects" => $compilado));
    }*/
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
        //En aqui definimos los campos que se van insertar en la base de datos
        $disciplina_id = $request->input("disciplina_id");
        $invitado_id = $request->input("invitado_id");
        $retador_id = $request->input("retador_id");
        $invitado_puntaje = $request->input("invitado_puntaje");
        $retador_puntaje = $request->input("retador_puntaje");
        $fecha = $request->input("fecha");
        $fase = $request->input("fase");

        $desafio_object = new Desafio;
        $desafio_object->disciplina_id = $disciplina_id;
        $desafio_object->invitado_id = $invitado_id;
        $desafio_object->retador_id = $retador_id;
        $desafio_object->invitado_puntaje = $invitado_puntaje;
        $desafio_object->retador_puntaje = $retador_puntaje;
        $desafio_object->fecha = $fecha;
        $desafio_object->fase = $fase;
        $desafio_object->visible = true;
        $desafio_object->estado = 'No iniciado';
        $desafio_object->save();

        return response()->json([
            "status" => true,
            "object" => $desafio_object
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Desafio  $desafio
     * @return \Illuminate\Http\Response
     */
    public function show(Desafio $desafio)
    {
        //Esto nos permite ver los datos de manera individual
        //con el id
        $disciplina = Disciplina::find($desafio->disciplina_id);
        $invitado = Equipo::find($desafio->invitado_id);
        $retador = Equipo::find($desafio->retador_id);
        $invitado_puntaje = $desafio->invitado_puntaje;
        $retador_puntaje = $desafio->retador_puntaje;
        $fase = $desafio->fase;
        $fecha = $desafio->fecha;
        $result = array("invitado" => $invitado, 
            "retador" => $retador,
            "disciplina" => $disciplina, 
            "fecha" => $fecha,
            "desafio" => $desafio);

        return response()->json([
            "status" => true, 
            "object" => $result
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Desafio  $desafio
     * @return \Illuminate\Http\Response
     */
    public function edit(Desafio $desafio)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Desafio  $desafio
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Desafio $desafio)
    {
        //Es el metodo de actualizar y se colocan los campos que se van a 
        //actualizar 
        if( $request->input("invitado_puntaje") == $request->input("retador_puntaje") ){
            return response()->json([
               'error' => 'no se permite empate' 
            ], 401);
        }
        if( $request->input("disciplina_id") == 2 ){
            if($request->input("invitado_puntaje")<=5 AND $request->input("retador_puntaje")<=5){
                $desafio->invitado_puntaje = $request->input("invitado_puntaje"); 
                $desafio->invitado_puntaje = $request->input("retador_puntaje");
                $desafio->update();
            }else{
                return response()->json([
                    'error' => 'Solo se puede jugar 5 sets' 
                 ], 400);   
            }          
        }
        if( $request->input("invitado_puntaje") > $request->input("retador_puntaje") ){
            $desafio->ganador_id = $request->input("invitado_id");
            //$competidorequipo->visible = $request->true;
            $desafio->update();
        }
        if( $request->input("invitado_puntaje") < $request->input("retador_puntaje") ){
            $desafio->ganador_id = $request->input("retador_id");
            //$competidorequipo->visible = $request->true;
            $desafio->update();
        }
        $desafio->disciplina_id = $request->input("disciplina_id");
        $desafio->invitado_id = $request->input("invitado_id");
        $desafio->retador_id = $request->input("retador_id");
        $desafio->invitado_puntaje = $request->input("invitado_puntaje");
        $desafio->retador_puntaje = $request->input("retador_puntaje");
        $desafio->fecha = $request->input("fecha");
        $desafio->fase = $request->input("fase");
        $desafio->estado = $request->input("estado");
        $desafio->update();
        return response()->json(array("status" => true,
        "object" =>$desafio));
        //HOLA
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Desafio  $desafio
     * @return \Illuminate\Http\Response
     */
    public function destroy(Desafio $desafio)
    {
        //Este es metodo de elimnar en donde se esta colocando la funcion
        //de ocultar en el campo visible
        $desafio->visible = false;
        $desafio->update();
    }
}
