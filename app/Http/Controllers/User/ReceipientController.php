<?php

namespace App\Http\Controllers\User;

use Exception;
use App\Models\User;
use App\Models\Receipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ReceipientController extends Controller
{
    public function index()
    {
        $page_title = "All Recipient"; 
        $receipients = Receipient::auth()->with('receiver')->orderByDesc("id")->paginate(12);  
        return view('user.sections.receipient.index',compact('page_title','receipients'));
    }
    public function addReceipient(){
        $page_title = "Add New Recipient"; 
        return view('user.sections.receipient.create',compact('page_title'));
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
        if($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $validated = $validator->validate();

        $user = User::where('email',$validated['email'])->orWhere('username',$validated['email'])->first(); 
        if($user == null) {
            return redirect()->back()->withInput()->with(['error' => ['User Not Found.']]);
        }
        if($user->id == auth()->user()->id) return redirect()->back()->withInput()->with(['error' => ["You can't add yourself as a recipient!."]]);
        $existReceipient = Receipient::auth()->where('email',$validated['email'])->first();
        if($existReceipient) return redirect()->back()->withInput()->with(['error' => [__('This recipient  already exist.')]]);
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
        if (isset($request->token) && $request->token != null) {
            return redirect()->route('user.send.money.select.recipient',$request->token)->with(['success' => [__('New Receipient Added Successful.')]]);
        } else {
            return redirect()->route('user.receipient.index');
        }  
    }
    public function editReceipient($id){
        $page_title = "Edit Recipient";
        $receipient =  Receipient::auth()->where('id',$id)->first(); 
        if( !$receipient){
            return back()->with(['error' => ['Sorry, invalid request']]);
        } 
        return view('user.sections.receipient.edit',compact('page_title','receipient'));
    }
    public function updateReceipient(Request $request) { 
        $validator = Validator::make($request->all(),[
            'firstname'   => 'required|string',
            'lastname'    => 'required|string',
            'country'     => 'required',
            'email'       => "required|email",
            'city'        => 'required|string',
            'address'     => 'required|string',
            'state'       => 'required|string',
            'zip'         => 'required|string',
        ]);
        if($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $validated = $validator->validate();
        $existReceipient = Receipient::findOrFail($request->id); 
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
        return redirect()->route('user.receipient.index')->with(['success' => [__('Receipient Updated Successful.')]]); 
    }
    public function deleteReceipient(Request $request) {
        $validator = Validator::make($request->all(),[
            'target'        => 'required|string|exists:receipients,id',
        ]);
        $validated = $validator->validate();
        $receipient = Receipient::where("id",$validated['target'])->first();
        try{
            $receipient->delete();
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went worng! Please try again')]]);
        }

        return back()->with(['success' => [__('Receipient deleted successfully!')]]);
    }
}
