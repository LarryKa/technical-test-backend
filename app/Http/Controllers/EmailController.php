<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Email;
use Illuminate\Support\Facades\Validator;

class emailController extends Controller
{
    public function store(Request $request)
    {
        // Validar datos
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
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

        // Crear email
        $email = new Email();
        $email->email = $request->email;
        $email->contact_id = $request->contact_id;
        if($email->save()){
            $data = [
                'status' => 'success',
                'code' => 201,
                'message' => 'email creado exitosamente',
                'email' => $email
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
            'email' => 'required|string|email',
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

        // Buscar email
        $email = Email::find($id);
        if (!$email) {
            $data = [
                'status' => 'error',
                'code' => 404,
                'message' => 'email no encontrado'
            ];
            return response()->json($data, $data['code']);
        }

        $email->email = $request->email;
        $email->contact_id = $request->contact_id;

        if ($email->save()) {
            $data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'email actualizado exitosamente',
                'email' => $email
            ];
        } else {
            $data = [
                'status' => 'error',
                'code' => 500,
                'message' => 'Ocurrió un error al actualizar el email'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function destroy($id)
    {
        // Buscar hpone
        $email = Email::find($id);
        if (!$email) {
            $data = [
                'status' => 'error',
                'code' => 404,
                'message' => 'email no encontrado'
            ];
            return response()->json($data, $data['code']);
        }
        
        if ($email->delete()) {
            $data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'email eliminado exitosamente'
            ];
        } else {
            $data = [
                'status' => 'error',
                'code' => 500,
                'message' => 'Ocurrió un error al eliminar el email'
            ];
        }

        return response()->json($data, $data['code']);
    }
}
