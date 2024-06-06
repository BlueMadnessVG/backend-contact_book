<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class contactController extends Controller
{
    //
    public function index() {
        $contact = Contact::with(['address', 'phone'])->get();

        if ($contact->isEmpty()){
            $date = [
                "message" => "There is not contact",
                "status"=> 404,
            ];
            return response()->json($date, 404);
        }

        $data = [
            'message' => 'Contacts fount',
            'data' => $contact,
            'status' => 200,
        ];

        return response()->json($data, 200);
    }

    public function show($id) {
        $contact = Contact::with(['address', 'phone'])->find($id);

        if (!$contact) {
            $data = [
                'message' => 'Contact Not Fount',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'message' => 'Contact Fount',
            'data' => $contact,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function search($query) {

        $contact = Contact::with(['address', 'phone'])
            ->where( function ($queryBuilder) use ($query) {
                $queryBuilder->where('name','LIKE','%'. $query .'%')
                    ->orWhere('email','LIKE','%'. $query .'%');
            } ) -> get();

        if (!$contact) {
            $data = [
                'message' => 'Contact Not Fount',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $data = [
            'message' => 'Contact Fount',
            'data' => $contact,
            'status' => 200
         ];

        return response()->json($data, 200);
    }

    public function store(Request $request) {
        
        //  VALIDATION OF THE REQUEST
        $validator = Validator::make($request->all(), [
            'name' => "required|string|max:50",
            'lastName' => 'required|string|max:50',
            'email'=> 'required|email|unique:contact',
            // ADDRESSES VALIDATOR 
            'addresses' => 'required|array|min:1',
            'addresses.*.type' => 'required|string',
            'addresses.*.street' => 'required|string',
            'addresses.*.number' => 'required|string',
            'addresses.*.suburb' => 'required|string',
            'addresses.*.city' => 'required|string',
            'addresses.*.state' => 'required|string',
            'addresses.*.postalCode' => 'required|string|max:5',
            'addresses.*.country' => 'required|string',
            //PHONES VALIDATOR
            'phones' => 'required|array|min:1',
            'phones.*.type' => 'required|string|max:255',
            'phones.*.phoneNumber' => 'required|string|max:20',
        ]);

        // IF VALIDATION FAILS
        if ($validator->fails()) {
            $data = [
                'message' => 'Data not valid',
                'error' => $validator->errors(),
                'status'=> 422,
            ];
            return response()->json($data, 422);
        }

        //  CREATE THE NEW CONTACT
        $data = $request->all();
        $contact = Contact::create([
            'name' => $data['name'],
            'lastName' => $data['lastName'],
            'email' => $data['email']
        ]);

        if (!$contact) {
            $data = [
                'message' => 'Error in creating the contact',
                'status' => 500
            ];
            return response()->json($data, 500);
        }

        // CREATION OF THE DATA FOR THE CONTACT 
        //  ADDRESS CREATION
        foreach ($data['addresses'] as $addressData) {
            $contact->address()->create($addressData);
        }
        //  PHONES CREATION
        foreach ($data['phones'] as $phoneData) {
            $contact->phone()->create($phoneData);
        }

        //  RESPONSE TO THE DATA
        $resp = [
            'message' => 'Contact stored',
            'data' => $contact,
            'status' => 201
        ];

        return response()->json($resp, 201);
    }

    public function destroy($id) {
        $contact = Contact::find($id);

        if (!$contact) {
            $data = [
                'message' => 'Contact not fount',
                'status' => 404
            ];

            return response()->json($data, 404);
        }

        $contact->address()->delete();
        $contact->phone()->delete();
        $contact->delete();

        $data = [
            'message' => 'Contact deleted',
            'status' => 200,
        ];

        return response()->json($data, 200);
    }

    public function update(Request $request, $id) {
        $contact = Contact::find($id);
      
        if (!$contact) {
            $data = [
                'message' => 'Contact not fount',
                'status' => 404
            ];

            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => "required|max:50",
            'lastName' => 'required|max:50',
            "email"=> "required|email|unique:contact",
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Data not valid',
                'error' => $validator->errors(),
                'status'=> 400,
            ];
            return response()->json($data, 400);
        }

        $contact->name = $request->name;
        $contact->lastName = $request->lastName; 
        $contact->email = $request->email; 

        $contact->save();

        $data = [
            'message' => 'Contact Updated',
            'data' => $contact,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function updatePartial(Request $request, $id) {
        //  VALIDATION OF THE DATA
        $validator = Validator::make($request->all(), [
            'name' => "string|max:50",
            'lastName' => 'string|max:50',
            "email"=> "email",
            'addresses' => 'array',
            'phones' => 'array'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Data not valid',
                'error' => $validator->errors(),
                'status'=> 422,
            ];
            return response()->json($data, 422);
        }

        //  FIND THE CONTACT AND UPDATE THE DATA
        $contact = Contact::find($id);
        if (!$contact) {
            $data = [
                'message' => 'Contact not fount',
                'status' => 404
            ];

            return response()->json($data, 404);
        }
        $contact->update($request->only(['name', 'lastName','email',]));

        //  UPDATE OR CREATE ADDRESSES
        if ($request->has('addresses')) {
            $contact->address()->delete();
            foreach ($request->input('addresses') as $addressData) {
                $contact->address()->create($addressData);
            }
        }

        //  UPDATE OR CREATE PHONES
        if ($request->has('phones')) {
            $contact->phone()->delete();
            foreach ($request->input('phones') as $phoneData) {
                $contact->phone()->create($phoneData);
            }
        }

        $data = [
            'message' => 'Contact Updated',
            'data' => $contact,
            'status' => 200
        ];

        return response()->json($data, 200);
    }
}
