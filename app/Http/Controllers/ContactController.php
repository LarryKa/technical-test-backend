<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::paginate(10);

        $contacts->load(['phones', 'emails', 'addresses']);
        $data = array(
            "status" => "success",
            "code" => 200,
            "contacts" => $contacts
        );        
        return response()->json($data, $data['code']);
    }

    public function store(Request $request)
    {
        // Validar datos
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'surname' => 'required|string',
            'city' => 'required|string',
        ]);

        // Verificar si hay errrores
        if ($validator->fails()) {
            $data = array(
                "code" => 422,
                "status" => "error",
                'errors' => $validator->errors()
            );
            return response()->json($data, $data['code']);
        }
            
        // Creación de contacto
        $contact = new Contact();
        $contact->name = $request->name;
        $contact->surname = $request->surname;
        $contact->city = $request->city;
        if($contact->save()){
            $data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Contacto creado exitosamente',
                'contact' => $contact
            ];    
        }else {
            $data = [
                'status' => 'error',
                'code' => 500,
                'message' => 'Ocurrió un error'                
            ];  
        }                    
        return response()->json([$data], $data['code']);        
    }

    public function show(string $id)
    {
        // Buscar el contacto 
        $contact = Contact::find($id);
        // Verificar si el contacto existe
        if (!$contact) {
            $data = [
                'status' => 'error',
                'code' => 404,
                'message' => 'Contacto no encontrado'
            ];
            return response()->json([$data], $data['code']);
        }
        $data = [
            'status' => 'success',
            'code' => 200,            
            'contact' => $contact
        ];
        return response()->json([$data], $data['code']);
    }

    public function update(Request $request, $id)
    {
        // Buscar el contacto
        $contact = Contact::find($id);

        // Verificar si el contacto existe
        if (!$contact) {
            $data = [
                'status' => 'error',
                'code' => 404,
                'message' => 'Contacto no encontrado'
            ];
            return response()->json([$data], $data['code']);
        }

        // Validar datos
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'surname' => 'required|string',
            'city' => 'required|string',
        ]);

        // Verificar si hay errrores
        if ($validator->fails()) {
            $data = [
                "status" => "error",
                "code" => 422,
                'errors' => $validator->errors()
            ];
            return response()->json($data, $data['code']);
        }

        // Actualizar datos
        $contact->name = $request->name;
        $contact->surname = $request->surname;
        $contact->city = $request->city;
        if ($contact->save()) {
            $data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Contacto actualizado exitosamente',
                'contact' => $contact
            ];
        } else {
            $data = [
                'status' => 'error',
                'code' => 500,
                'message' => 'Ocurrió un error al actualizar el contacto'
            ];
        }

        return response()->json([$data], $data['code']);
    }

    public function destroy($id)
    {        
        $contact = Contact::find($id);
        
        if (!$contact) {
            $data = [
                'status' => 'error',
                'code' => 404,
                'message' => 'Contacto no encontrado'
            ];
            return response()->json([$data], $data['code']);
        }
        
        if ($contact->delete()) {
            $data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Contacto eliminado exitosamente'
            ];
        } else {
            $data = [
                'status' => 'error',
                'code' => 500,
                'message' => 'Ocurrió un error al eliminar el contacto'
            ];
        }

        return response()->json([$data], $data['code']);
    }


    public function search($searchTerm)
    {
        
        $query = Contact::query();
        
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%$searchTerm%")
                    ->orWhere('surname', 'like', "%$searchTerm%")
                    ->orWhereHas('phones', function ($q) use ($searchTerm) {
                        $q->where('phone', 'like', "%$searchTerm%");
                    })
                    ->orWhereHas('addresses', function ($q) use ($searchTerm) {
                        $q->where('address', 'like', "%$searchTerm%");
                    })
                    ->orWhereHas('emails', function ($q) use ($searchTerm) {
                        $q->where('email', 'like', "%$searchTerm%");
                    });
            });
        }
        
        $contacts = $query->paginate(10);
        
        $contacts->load(['phones', 'emails', 'addresses']);
        
        $data = [
            "status" => "success",
            "code" => 200,
            "contacts" => $contacts
        ];

        return response()->json($data, $data['code']);
    }

}
