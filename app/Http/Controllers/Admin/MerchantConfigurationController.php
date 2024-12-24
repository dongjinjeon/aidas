<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\MerchantConfiguration;
use Exception;
use Illuminate\Http\Request;

class MerchantConfigurationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = "Merchant Configuration";
        $merchant_config = MerchantConfiguration::first();
        return view('admin.sections.merchant-config.index',compact('page_title','merchant_config'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'name'          => "required|string|max:200",
            'version'       => "required|string|max:30",
            'email_verify'  => "required|boolean",
            'image'         => "nullable|image|mimes:jpg,png,jpeg,svg,webp|max:10224",
        ]);

        $merchant_config = MerchantConfiguration::first();

        $form_data = $request->except(['image']);
        if($request->hasFile('image')) {
            $image_link = get_files_from_fileholder($request,'image');
            $image = upload_files_from_path_dynamic($image_link,'merchant-config',$merchant_config->image ?? null);
            $form_data['image'] = $image;
        }

        try{
            MerchantConfiguration::updateOrCreate(['id' => 1],$form_data);
        }catch(Exception $e) { 
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Information updated successfully!']]);
    }
}
