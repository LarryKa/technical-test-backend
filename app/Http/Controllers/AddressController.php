<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Address;

class AddressController extends Controller
{
    public function store(Request $request)
    {
        // Validar datos
        $validator = Validator::make($request->all(), [
            'address' => 'required|string',
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

        // Crear address
        $address = new Address();
        $address->address = $request->address;
        $address->contact_id = $request->contact_id;
        if($address->save()){
            $data = [
                'status' => 'success',
                'code' => 201,
                'message' => 'address creado exitosamente',
                'address' => $address
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
            'address' => 'required|string',
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

        // Buscar address
        $address = Address::find($id);
        if (!$address) {
            $data = [
                'status' => 'error',
                'code' => 404,
                'message' => 'address no encontrado'
            ];
            return response()->json($data, $data['code']);
        }

        $address->address = $request->address;
        $address->contact_id = $request->contact_id;

        if ($address->save()) {
            $data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'address actualizado exitosamente',
                'address' => $address
            ];
        } else {
            $data = [
                'status' => 'error',
                'code' => 500,
                'message' => 'Ocurrió un error al actualizar el address'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function destroy($id)
    {
        // Buscar hpone
        $address = Address::find($id);
        if (!$address) {
            $data = [
                'status' => 'error',
                'code' => 404,
                'message' => 'address no encontrado'
            ];
            return response()->json($data, $data['code']);
        }
        
        if ($address->delete()) {
            $data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'address eliminado exitosamente'
            ];
        } else {
            $data = [
                'status' => 'error',
                'code' => 500,
                'message' => 'Ocurrió un error al eliminar el address'
            ];
        }

        return response()->json($data, $data['code']);
    }
}
