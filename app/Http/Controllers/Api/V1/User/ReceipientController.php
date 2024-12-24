<?php

namespace App\Http\Controllers\Api\V1\User;

use Exception;
use App\Models\User;
use App\Models\Receipient;
use Illuminate\Http\Request;
use App\Http\Helpers\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ReceipientController extends Controller
{
    public function index() { 
        $receipients = Receipient::auth()->with('receiver')->orderByDesc("id")->get()->map(function($data){
            return[
                'id'         => $data->id,
                'receipient_id'  => $data->receiver->id,
                'firstname'  => $data->firstname,
                'lastname'   => $data->lastname,
                'username '  => $data->receiver->username,
                'type '      => $data->type,
                'email'      => $data->email,
                'address'    => $data->address,
                'country'    => $data->country,
                'state'      => $data->state,
                'city'       => $data->city,
                'zip_code'   => $data->zip_code,
                'image'   => $data->receiver->image,
                'path_location'     => files_asset_path_basename("user-profile"),
                'default_image'     => files_asset_path_basename("profile-default"),
                'created_at' => $data->created_at,
            ];
        });  
        $data =[
            'receipients'         => $receipients,  
            'base_url'          => url('/'), 
        ]; 
        return Response::success([__('Receipients info fetch successfully!')], $data);
    }
    public function userSearch(Request $request) {
        $validator = Validator::make($request->all(),[
            'text'     => "required|string", 
        ]); 
        if($validator->fails()) return Response::error($validator->errors()->all(),[]);

        $validated = $validator->validate();
        $user_data = User::where('id','!=',auth()->user()->id)->where('email',$validated['text'])->orWhere('username',$validated['text'])->first();
        if(!$user_data) return Response::error([__("User doesn't exists.")],[],400);
        $user_data = [
            'id'         => $user_data->id,
            'firstname'  => $user_data->firstname,
            'lastname'   => $user_data->lastname,
            'username'   => $user_data->username,
            'email'      => $user_data->email,
            'address'    => $user_data->address,
            'created_at' => $user_data->created_at,
        ];
        $data =[
            'user_data'         => $user_data,  
            'base_url'          => url('/'), 
        ]; 
        return Response::success([__('User info fetch successfully!')], $data);
    }
    public function storeReceipient(Request $request) { 
        $validator = Validator::make($request->all(),[
            'country'     => 'required',
            'firstname'   => 'required|string',
            'lastname'    => 'required|string',
            'email'       => "required|string",
            'city'        => 'required|string',
            'address'     => 'required|string',
            'state'       => 'required|string',
            'zip'         => 'required|string',
        ]);
        if($validator->fails()) return Response::error($validator->errors()->all(),[]);
        $validated = $validator->validate();

        $user = User::where('id','!=',auth()->user()->id)->where('email',$validated['email'])->orWhere('username',$validated['email'])->first(); 
        if(!$user) return Response::error([__("User doesn't exists.")],[],400);

        $existReceipient = Receipient::auth()->where('email',$validated['email'])->first();
        if($existReceipient) return Response::error([__("This recipient  already exist.")],[],400);
        $validated['user_id'] = auth()->user()->id;
        $validated['email'] = $user->email;
        $validated['zip_code'] = $validated['zip'];
        DB::beginTransaction();
        try{  
            Receipient::create($validated);
            DB::commit();  
        }catch(Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        } 
          
        return Response::success([__('New Receipient Added Successful.')], []);
    }
    public function updateReceipient(Request $request) { 
        $validator = Validator::make($request->all(),[
            'id'   => 'required|integer',
            'firstname'   => 'required|string',
            'lastname'    => 'required|string',
            'country'     => 'required',
            'email'       => "nullable|email",
            'city'        => 'required|string',
            'address'     => 'required|string',
            'state'       => 'required|string',
            'zip'         => 'required|string',
        ]);
        if($validator->fails()) return Response::error($validator->errors()->all(),[]);
        $validated = $validator->validate();
        $existReceipient = Receipient::find($request->id); 
        if(!$existReceipient) return Response::error([__("Recipient doesn't exists.")],[],400);

        $existReceipient->firstname = $validated['firstname'];
        $existReceipient->lastname  = $validated['lastname'];
        $existReceipient->country   = $validated['country'];
        $existReceipient->city      = $validated['city'];
        $existReceipient->address   = $validated['address'];
        $existReceipient->state     = $validated['state'];
        $existReceipient->zip_code  = $validated['zip'];
        DB::beginTransaction();
        try{  
            $existReceipient->save();
            DB::commit();  
        }catch(Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        } 
        return Response::success([__('Receipient Updated Successful.')], []); 
    }
    public function deleteReceipient(Request $request) {
        // dd($request->target);
        $validator = Validator::make($request->all(),[
            'target'        => 'required|string|exists:receipients,id',
        ]);
        if($validator->fails()) return Response::error($validator->errors()->all(),[]);
        $validated = $validator->validate();
        $receipient = Receipient::find($validated['target']);
        if(!$receipient) return Response::error([__("Recipient doesn't exists.")],[],400);
        try{
            $receipient->delete();
        }catch(Exception $e) {
            return Response::error([__("Something went worng! Please try again.")],[],400);
        }
        return Response::success([__('Receipient deleted successfully!')], []);  
    }
}
