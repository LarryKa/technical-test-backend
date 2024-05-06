<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Phone;
use Illuminate\Support\Facades\Validator;

class PhoneController extends Controller
{       
    public function store(Request $request)
    {
        // Validar datos
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|digits:10',
            'contact_id' => 'required|exists:contacts,id'
        ]);

        // Verificar si hay errores
        if ($validator->fails()) {
            $data = [
                "status" => "error",
                "code" => 422,
                'errors' => $validator->errors()
            ];
            return response()->json($data, $data['code']);
        }

        // Crear phone
        $phone = new Phone();
        $phone->phone = $request->phone;
        $phone->contact_id = $request->contact_id;
        if($phone->save()){
            $data = [
                'status' => 'success',
                'code' => 201,
                'message' => 'Teléfono creado exitosamente',
                'phone' => $phone
            ];
        }else {
            $data = [
                'status' => 'error',
                'code' => 500,
                'message' => 'Ocurrió un error'                
            ];  
        }                    
        return response()->json($data, $data['code']);
    }

    public function update(Request $request, $id)
    {
        // Validar datos
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|digits:10',
            'contact_id' => 'required|exists:contacts,id'
        ]);

        // Verificar si hay errores
        if ($validator->fails()) {
            $data = [
                "status" => "error",
                "code" => 422,
                'errors' => $validator->errors()
            ];
            return response()->json($data, $data['code']);
        }

        // Buscar hpone
        $phone = Phone::find($id);
        if (!$phone) {
            $data = [
                'status' => 'error',
                'code' => 404,
                'message' => 'Teléfono no encontrado'
            ];
            return response()->json($data, $data['code']);
        }

        $phone->phone = $request->phone;
        $phone->contact_id = $request->contact_id;

        if ($phone->save()) {
            $data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Teléfono actualizado exitosamente',
                'phone' => $phone
            ];
        } else {
            $data = [
                'status' => 'error',
                'code' => 500,
                'message' => 'Ocurrió un error al actualizar el teléfono'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function destroy($id)
    {
        // Buscar hpone
        $phone = Phone::find($id);
        if (!$phone) {
            $data = [
                'status' => 'error',
                'code' => 404,
                'message' => 'Teléfono no encontrado'
            ];
            return response()->json($data, $data['code']);
        }
        
        if ($phone->delete()) {
            $data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Teléfono eliminado exitosamente'
            ];
        } else {
            $data = [
                'status' => 'error',
                'code' => 500,
                'message' => 'Ocurrió un error al eliminar el teléfono'
            ];
        }

        return response()->json($data, $data['code']);
    }
}
