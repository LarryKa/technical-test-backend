<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Phone;
use App\Models\Email;
use App\Models\Address;
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
            'emails' => 'required|array',
            'emails.*' => 'required|email',
            'addresses' => 'required|array',
            'addresses.*' => 'required|string',
            'phones' => 'required|array',
            'phones.*' => 'required|regex:/^\d{10}$/'
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

            foreach ($request->emails as $email) {
                $newEmail = new Email();
                $newEmail->email = $email;
                $newEmail->contact_id = $contact->id;
                $newEmail->save();
            }
                    
            foreach ($request->addresses as $address) {
                $newAddress = new Address();
                $newAddress->address = $address;
                $newAddress->contact_id = $contact->id;
                $newAddress->save();
            }
                    
            foreach ($request->phones as $phone) {
                $newPhone = new Phone();
                $newPhone->phone = $phone;
                $newPhone->contact_id = $contact->id;
                $newPhone->save();
            }

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
        return response()->json($data, $data['code']);        
    }

    public function show(string $id)
    {
        // Buscar el contacto 
        $contact = Contact::find($id);
        $contact->load(['phones', 'emails', 'addresses']);
        // Verificar si el contacto existe
        if (!$contact) {
            $data = [
                'status' => 'error',
                'code' => 404,
                'message' => 'Contacto no encontrado'
            ];
            return response()->json($data, $data['code']);
        }
        $data = [
            'status' => 'success',
            'code' => 200,            
            'contact' => $contact
        ];
        return response()->json($data, $data['code']);
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
            return response()->json($data, $data['code']);
        }

        // Validar datos        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'surname' => 'required|string',
            'city' => 'required|string',
            'emails' => 'required|array',
            'emails.*' => 'required|email',
            'addresses' => 'required|array',
            'addresses.*' => 'required|string',
            'phones' => 'required|array',
            'phones.*' => 'required|regex:/^\d{10}$/'
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

        $contact->phones()->delete();
        $contact->emails()->delete();
        $contact->addresses()->delete();

        // Actualizar datos
        $contact->name = $request->name;
        $contact->surname = $request->surname;
        $contact->city = $request->city;
        if ($contact->save()) {
            foreach ($request->emails as $email) {
                $newEmail = new Email();
                $newEmail->email = $email;
                $newEmail->contact_id = $contact->id;
                $newEmail->save();
            }
                    
            foreach ($request->addresses as $address) {
                $newAddress = new Address();
                $newAddress->address = $address;
                $newAddress->contact_id = $contact->id;
                $newAddress->save();
            }
                    
            foreach ($request->phones as $phone) {
                $newPhone = new Phone();
                $newPhone->phone = $phone;
                $newPhone->contact_id = $contact->id;
                $newPhone->save();
            }
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

        return response()->json($data, $data['code']);
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
            return response()->json($data, $data['code']);
        }

        $contact->phones()->delete();
        $contact->emails()->delete();
        $contact->addresses()->delete();
        
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

        return response()->json($data, $data['code']);
    }


    public function search($searchTerm)
    {
        
        $query = Contact::query();
        
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%$searchTerm%")
                    ->orWhere('surname', 'like', "%$searchTerm%")
                    ->orWhere('city', 'like', "%$searchTerm%")
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
