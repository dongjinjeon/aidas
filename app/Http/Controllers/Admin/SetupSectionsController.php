<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use App\Models\Admin\Language;
use App\Constants\LanguageConst;
use App\Models\Admin\SiteSections;
use App\Constants\SiteSectionConst;
use App\Http\Controllers\Controller;
use App\Models\Frontend\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Frontend\AnnouncementCategory;

class SetupSectionsController extends Controller
{
    protected $languages;

    public function __construct()
    {
        $this->languages = Language::get();
    }

    /**
     * Register Sections with their slug
     * @param string $slug
     * @param string $type
     * @return string
     */
    public function section($slug,$type) {
        $sections = [
            'auth-section'    => [
                'view'      => "authView",
                'update'    => "authUpdate",
            ],
            'banner'    => [
                'view'      => "bannerView",
                'update'    => "bannerUpdate",
            ],
            'brand'    => [
                'view'      => "brandView",
                'itemStore'     => "brandItemStore",
                'itemDelete'    => "brandItemDelete",
            ],
            'feature'  => [
                'view'      => "featureView",
                'update'    => "featureUpdate",
                'itemStore'     => "featureItemStore",
                'itemUpdate'    => "featureItemUpdate",
                'itemDelete'    => "featureItemDelete",
            ],
            'security-system'  => [
                'view'      => "securitySystemView",
                'update'    => "securitySystemUpdate",
                'itemStore'     => "securitySystemItemStore",
                'itemUpdate'    => "securitySystemItemUpdate",
                'itemDelete'    => "securitySystemItemDelete",
            ],
            'why-choose-us'  => [
                'view'      => "whyChooseUsView",
                'update'    => "whyChooseUsUpdate",
                'itemStore'     => "whyChooseUsItemStore",
                'itemUpdate'    => "whyChooseUsItemUpdate",
                'itemDelete'    => "whyChooseUsItemDelete",
            ],
            'app-section'  => [
                'view'      => "appView",
                'update'    => "appUpdate", 
                'itemStore'     => "appItemStore",
                'itemUpdate'    => "appItemUpdate",
                'itemDelete'    => "appItemDelete",
            ],
            'clients-feedback' => [
                'view'          => "clientsFeedbackView",
                'update'        => "clientsFeedbackUpdate",
                'itemStore'     => "clientsFeedbackItemStore",
                'itemUpdate'    => "clientsFeedbackItemUpdate",
                'itemDelete'    => "clientsFeedbackItemDelete",
            ],
            'announcement' => [
                'view'          => "announcementView",
                'update'        => "announcementUpdate",
            ],
            'how-it-work' => [
                'view'          => "howItWorkView",
                'update'        => "howItWorkUpdate",
            ],
            'about-page'  => [
                'view'          => "aboutPageView",
                'update'        => "aboutPageUpdate", 
            ],
            'service-page'  => [
                'view'          => "servicePageView",
                'update'        => "servicePageUpdate",
                'itemStore'     => "servicePageItemStore",
                'itemUpdate'    => "servicePageItemUpdate",
                'itemDelete'    => "servicePageItemDelete",
            ],
            'contact-us' => [
                'view'          => "contactUsView",
                'update'        => "contactUsUpdate",
                'itemStore'     => "contactItemStore",
                'itemUpdate'    => "contactItemUpdate",
                'itemDelete'    => "contactItemDelete",
            ],
            'faq-section'  => [
                'view'      => "faqView",
                'update'    => "faqUpdate",
                'itemStore'     => "faqItemStore",
                'itemUpdate'    => "faqItemUpdate",
                'itemDelete'    => "faqItemDelete",
            ],
            'footer' => [
                'view'          => "footerView",
                'update'        => "footerUpdate",
            ]
        ];

        if(!array_key_exists($slug,$sections)) abort(404);
        if(!isset($sections[$slug][$type])) abort(404);
        $next_step = $sections[$slug][$type];
        return $next_step;
    }

    /**
     * Method for getting specific step based on incoming request
     * @param string $slug
     * @return method
     */
    public function sectionView($slug) {
        $section = $this->section($slug,'view');
        return $this->$section($slug);
    }

    /**
     * Method for distribute store method for any section by using slug
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     * @return method
     */
    public function sectionItemStore(Request $request, $slug) {
        $section = $this->section($slug,'itemStore');
        return $this->$section($request,$slug);
    }

    /**
     * Method for distribute update method for any section by using slug
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     * @return method
     */
    public function sectionItemUpdate(Request $request, $slug) {
        $section = $this->section($slug,'itemUpdate');
        return $this->$section($request,$slug);
    }

    /**
     * Method for distribute delete method for any section by using slug
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     * @return method
     */
    public function sectionItemDelete(Request $request,$slug) {
        $section = $this->section($slug,'itemDelete');
        return $this->$section($request,$slug);
    }

    /**
     * Method for distribute update method for any section by using slug
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     * @return method
     */
    public function sectionUpdate(Request $request,$slug) {
        $section = $this->section($slug,'update');
        return $this->$section($request,$slug);
    }

        /**
     * Mehtod for show auth section page
     * @param string $slug
     * @return view
     */
    //=======================================Auth section Start =======================================
    public function authView($slug) {
        $page_title = "Auth Section";
        $section_slug = Str::slug(SiteSectionConst::AUTH_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.auth-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }
    public function authUpdate(Request $request,$slug) {
        $basic_field_name = [
            'login_title' => "required|string",
            'login_text' => "required|string",
            'register_title' => "required|string",
            'register_text' => "required|string",
            'forget_title' => "required|string",
            'forget_text' => "required|string",
        ];
        $slug = Str::slug(SiteSectionConst::AUTH_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        $data['image'] = $section->value->image ?? null;
        if($request->hasFile("image")) {
            $data['image']      = $this->imageValidate($request,"image",$section->value->image ?? null);
        }
        
        $data['language']  = $this->contentValidate($request,$basic_field_name);
        $update_data['value']  = $data;
        $update_data['key']    = $slug;
        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went worng! Please try again.']]);
        }
        return back()->with(['success' => ['Section updated successfully!']]);
    }

//=======================================Auth section End ==========================================

    /**
     * Method for show banner section page
     * @param string $slug
     * @return view
     */
    public function bannerView($slug) {
        $page_title = "Banner Section";
        $section_slug = Str::slug(SiteSectionConst::BANNER_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.banner-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update banner section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function bannerUpdate(Request $request,$slug) {

        $basic_field_name = [
            'heading' => "required|string|max:100",
            'button_name' => "required|string|max:50",
            'button_name_two' => "required|string|max:50",
            'desc' => "required|string|max:5000",
        ];

        $slug = Str::slug(SiteSectionConst::BANNER_SECTION);
        $section = SiteSections::where("key",$slug)->first();
        $data['image'] = $section->value->image ?? null;

        $data['language']  = $this->contentValidate($request,$basic_field_name);
        $update_data['value']  = $data;
        $update_data['key']    = $slug;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully!']]);
    }


    /**
     * Method for show brand section page
     * @param string $slug
     * @return view
     */
    public function brandView($slug) {
        $page_title = "Brand Section";
        $section_slug = Str::slug(SiteSectionConst::BRAND_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.brand-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }


    /**
     * Method for store brand item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function brandItemStore(Request $request,$slug) {
        $slug = Str::slug(SiteSectionConst::BRAND_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        $validator = Validator::make($request->all(),[
            'image'     => "required|mimes:png,jpg,svg,webp,jpeg|max:10240",
        ]);
        if($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('modal','brand-add');
        }
        $validated = $validator->validate();

        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $section_data['items'][$unique_id]['id'] = $unique_id;
        $section_data['items'][$unique_id]['image'] = "";

        if($request->hasFile("image")) {
            $section_data['items'][$unique_id]['image'] = $this->imageValidate($request,"image",$section->value?->items?->image ?? null);
        }

        $update_data['key']     = $slug;
        $update_data['value']   = $section_data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Section item added successfully!']]);
    }

    /**
     * Method for delete brand item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function brandItemDelete(Request $request,$slug) {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::BRAND_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        try{
            $image_link = get_files_path('site-section') . '/' . $section_values['items'][$request->target]['image'];
            unset($section_values['items'][$request->target]);
            delete_file($image_link);
            $section->update([
                'value'     => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section item delete successfully!']]);
    }

    /**
     * Method for show about us section page
     * @param string $slug
     * @return view
     */
    public function aboutUsView($slug) {
        $page_title = "About US Section";
        $section_slug = Str::slug(SiteSectionConst::ABOUT_US_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.about-us-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update about section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function aboutUsUpdate(Request $request,$slug) {

        $basic_field_name = [
            'heading'       => "required|string|max:100",
            'sub_heading'   => "required|string|max:255",
        ];

        $slug = Str::slug(SiteSectionConst::ABOUT_US_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        if($section != null) {
            $data = json_decode(json_encode($section->value),true);
        }else {
            $data = [];
        }

        $data['image'] = $section->value->image ?? null;
        if($request->hasFile("image")) {
            $data['image']      = $this->imageValidate($request,"image",$section->value->image ?? null);
        }

        $data['language']  = $this->contentValidate($request,$basic_field_name);
        $update_data['value']  = $data;
        $update_data['key']    = $slug;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully!']]);
    }

    /**
     * Method for store about us item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function aboutUsItemStore(Request $request,$slug) {

        $basic_field_name = [
            'title'         => "required|string|max:255",
            'description'   => "required|string|max:500",
        ];

        $validator = Validator::make($request->all(),[
            'icon'      => "required|string|max:255",
        ]);
        if($validator->fails()) return back()->withErrors($validator)->withInput()->with('modal','about-us-item-add');
        $validated = $validator->validate();

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"about-us-item-add");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::ABOUT_US_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;
        $section_data['items'][$unique_id]['icon'] = $validated['icon'];

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Section item added successfully!']]);
    }

    /**
     * Method for update about us item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function aboutUsItemUpdate(Request $request,$slug) {

        $request->validate([
            'target'        => "required|string",
            'icon_edit'     => "required|string|max:255",
        ]);

        $basic_field_name = [
            'title_edit'     => "required|string|max:255",
            'description_edit'   => "required|string|max:500",
        ];

        $slug = Str::slug(SiteSectionConst::ABOUT_US_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"about-us-item-edit");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function($language) {
            return replace_array_key($language,"_edit");
        },$language_wise_data);

        $section_values['items'][$request->target]['language'] = $language_wise_data;
        $section_values['items'][$request->target]['icon']    = $request->icon_edit;

        try{
            $section->update([
                'value' => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Information updated successfully!']]);
    }

    /**
     * Method for delete about us item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function aboutUsItemDelete(Request $request,$slug) {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::ABOUT_US_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        try{
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section item delete successfully!']]);
    }  
    /**
     * Method for show Feature section page
     * @param string $slug
     * @return view
     */
    public function featureView($slug) {
        $page_title = "Feature Section";
        $section_slug = Str::slug(SiteSectionConst::FEATURE_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.feature-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update Feature section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function featureUpdate(Request $request,$slug) {
        $basic_field_name = [
            'heading'       => "required|string|max:100",
            'sub_heading'   => "required|string|max:255",
        ];

        $slug = Str::slug(SiteSectionConst::FEATURE_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        if($section != null) {
            $data = json_decode(json_encode($section->value),true);
        }else {
            $data = [];
        }
        
        $data['language']  = $this->contentValidate($request,$basic_field_name);
        $update_data['value']  = $data;
        $update_data['key']    = $slug;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully!']]);
    }
      /**
     * Method for store feature item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function featureItemStore(Request $request,$slug) {

        $basic_field_name = [
            'title'         => "required|string|max:255",
            'description'   => "required|string|max:500",
        ];

        $validator = Validator::make($request->all(),[
            'icon'      => "required|string|max:255",
        ]);
        if($validator->fails()) return back()->withErrors($validator)->withInput()->with('modal','feature-item-add');
        $validated = $validator->validate();

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"feature-item-add");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::FEATURE_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;
        $section_data['items'][$unique_id]['icon'] = $validated['icon'];

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Section item added successfully!']]);
    }

    /**
     * Method for update feature item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function featureItemUpdate(Request $request,$slug) {

        $request->validate([
            'target'        => "required|string",
            'icon_edit'     => "required|string|max:255",
        ]);

        $basic_field_name = [
            'title_edit'     => "required|string|max:255",
            'description_edit'   => "required|string|max:500",
        ];

        $slug = Str::slug(SiteSectionConst::FEATURE_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"feature-item-edit");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function($language) {
            return replace_array_key($language,"_edit");
        },$language_wise_data);

        $section_values['items'][$request->target]['language'] = $language_wise_data;
        $section_values['items'][$request->target]['icon']    = $request->icon_edit;

        try{
            $section->update([
                'value' => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Information updated successfully!']]);
    }

    /**
     * Method for delete feature item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function featureItemDelete(Request $request,$slug) {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::FEATURE_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        try{
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section item delete successfully!']]);
    }
    /**
     * Method for show Security System section page
     * @param string $slug
     * @return view
     */
    public function securitySystemView($slug) {
        $page_title = "Security System Section";
        $section_slug = Str::slug(SiteSectionConst::SECURITY_SYSTEM_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.security-system-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update Security System section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function securitySystemUpdate(Request $request,$slug) {
        $basic_field_name = [
            'heading'       => "required|string|max:100",
            'sub_heading'   => "required|string|max:255",
        ];

        $slug = Str::slug(SiteSectionConst::SECURITY_SYSTEM_SECTION);
        $section = SiteSections::where("key",$slug)->first();
        if($section != null) {
            $data = json_decode(json_encode($section->value),true);
        }else {
            $data = [];
        }
        $data['language']  = $this->contentValidate($request,$basic_field_name);
        $update_data['value']  = $data;
        $update_data['key']    = $slug;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully!']]);
    }
      /**
     * Method for store Security System item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function securitySystemItemStore(Request $request,$slug) {

        $basic_field_name = [
            'title'         => "required|string|max:255",
            'description'   => "required|string|max:500",
        ];

        $validator = Validator::make($request->all(),[
            'icon'      => "required|string|max:255",
        ]);
        if($validator->fails()) return back()->withErrors($validator)->withInput()->with('modal','security-system-item-add');
        $validated = $validator->validate();

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"security-system-item-add");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::SECURITY_SYSTEM_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;
        $section_data['items'][$unique_id]['icon'] = $validated['icon'];

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Section item added successfully!']]);
    }

    /**
     * Method for update Security System item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function securitySystemItemUpdate(Request $request,$slug) {

        $request->validate([
            'target'        => "required|string",
            'icon_edit'     => "required|string|max:255",
        ]);

        $basic_field_name = [
            'title_edit'     => "required|string|max:255",
            'description_edit'   => "required|string|max:500",
        ];

        $slug = Str::slug(SiteSectionConst::SECURITY_SYSTEM_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"security-system-item-edit");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function($language) {
            return replace_array_key($language,"_edit");
        },$language_wise_data);

        $section_values['items'][$request->target]['language'] = $language_wise_data;
        $section_values['items'][$request->target]['icon']    = $request->icon_edit;

        try{
            $section->update([
                'value' => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Information updated successfully!']]);
    }

    /**
     * Method for delete Security System item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function securitySystemItemDelete(Request $request,$slug) {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::SECURITY_SYSTEM_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        try{
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section item delete successfully!']]);
    }
    /**
     * Method for show Why Choose Us section page
     * @param string $slug
     * @return view
     */
    public function whyChooseUsView($slug) {
        $page_title = "Why Choose Us Section";
        $section_slug = Str::slug(SiteSectionConst::WHY_CHOOSE_US);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.why-choose-us-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update Why Choose Us section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function whyChooseUsUpdate(Request $request,$slug) {
        $basic_field_name = [
            'heading'       => "required|string|max:100",
            'sub_heading'   => "required|string|max:255",
        ];

        $slug = Str::slug(SiteSectionConst::WHY_CHOOSE_US);
        $section = SiteSections::where("key",$slug)->first(); 
        if($section != null) {
            $data = json_decode(json_encode($section->value),true);
        }else {
            $data = [];
        }
        $data['language']  = $this->contentValidate($request,$basic_field_name);
        $update_data['value']  = $data;
        $update_data['key']    = $slug;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully!']]);
    }
      /**
     * Method for store Why Choose Us item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function whyChooseUsItemStore(Request $request,$slug) {

        $basic_field_name = [
            'title'         => "required|string|max:255",
            'description'   => "required|string|max:500",
        ];

        $validator = Validator::make($request->all(),[
            'icon'      => "required|string|max:255",
        ]);
        if($validator->fails()) return back()->withErrors($validator)->withInput()->with('modal','why-choose-us-add');
        $validated = $validator->validate();

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"why-choose-us-add");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::WHY_CHOOSE_US);
        $section = SiteSections::where("key",$slug)->first();

        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;
        $section_data['items'][$unique_id]['icon'] = $validated['icon'];

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Section item added successfully!']]);
    }

    /**
     * Method for update Why Choose Us item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function whyChooseUsItemUpdate(Request $request,$slug) {

        $request->validate([
            'target'        => "required|string",
            'icon_edit'     => "required|string|max:255",
        ]);

        $basic_field_name = [
            'title_edit'     => "required|string|max:255",
            'description_edit'   => "required|string|max:500",
        ];

        $slug = Str::slug(SiteSectionConst::WHY_CHOOSE_US);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"why-choose-us-item-edit");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function($language) {
            return replace_array_key($language,"_edit");
        },$language_wise_data);

        $section_values['items'][$request->target]['language'] = $language_wise_data;
        $section_values['items'][$request->target]['icon']    = $request->icon_edit;

        try{
            $section->update([
                'value' => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Information updated successfully!']]);
    }

    /**
     * Method for delete Why Choose Us item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function whyChooseUsItemDelete(Request $request,$slug) {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::WHY_CHOOSE_US);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        try{
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section item delete successfully!']]);
    }
//==========================App Section Start====================================
public function appView($slug){
    $page_title = "App Section";
    $section_slug = Str::slug(SiteSectionConst::APP_SECTION);
    $data = SiteSections::getData($section_slug)->first();
    $languages = $this->languages;

    return view('admin.sections.setup-sections.app-section',compact(
        'page_title',
        'data',
        'languages',
        'slug',
    ));
}
public function appUpdate(Request $request,$slug) {
    $basic_field_name = [
        'heading'     => "required|string|max:100", 
        'details'     => "required|string",
    ];

    $slug = Str::slug(SiteSectionConst::APP_SECTION);
    $section = SiteSections::where("key",$slug)->first();
    if($section != null) {
        $data = json_decode(json_encode($section->value),true);
    }else {
        $data = [];
    }
    
    $data['language']  = $this->contentValidate($request,$basic_field_name);
    //image upload 
    if($request->hasFile('image')) {
        $image = get_files_from_fileholder($request,'image');
        $upload = upload_files_from_path_dynamic($image,'site-section');
        $data['image'] = $upload;
    }
    $update_data['key']    = $slug;
    $update_data['value']  = $data;

    try{
        SiteSections::updateOrCreate(['key' => $slug],$update_data);
    }catch(Exception $e) {
        return back()->with(['error' => ['Something went worng! Please try again.']]);
    }

    return back()->with(['success' => ['Section updated successfully!']]);
}
public function appItemStore(Request $request,$slug) { 
    $validator = Validator::make($request->all(), [
        'link' => 'required|string'
    ]);

    if($validator->fails()) {
        return back()->withErrors($validator)->withInput()->with('modal','app-add');
    }

    $validated = $validator->validate();

    $basic_field_name = [
        'title' => "required|string|max:200",  
        'sub_title' => "required|string|max:200",  
    ];


    $language_wise_data = $this->contentValidate($request,$basic_field_name,"app-add");
    if($language_wise_data instanceof RedirectResponse) return $language_wise_data;
    $slug = Str::slug(SiteSectionConst::APP_SECTION);
    $section = SiteSections::where("key",$slug)->first();

    if($section != null) {
        $section_data = json_decode(json_encode($section->value),true);
    }else {
        $section_data = [];
    }
    $unique_id = uniqid();
    
    $section_data['items'][$unique_id]['language'] = $language_wise_data;
    $section_data['items'][$unique_id]['id'] = $unique_id;
    $section_data['items'][$unique_id]['link'] = $validated['link'];
    
    if($request->hasFile("image")) {
        $section_data['items'][$unique_id]['image'] = $this->imageValidate($request,"image",$section->value?->items?->image ?? null);
    }

    $update_data['key'] = $slug;
    $update_data['value']   = $section_data;

    try{
        SiteSections::updateOrCreate(['key' => $slug],$update_data);
    }catch(Exception $e) {
        return back()->with(['error' => ['Something went worng! Please try again']]);
    }

    return back()->with(['success' => ['Item added successfully!']]);
}

public function appItemUpdate(Request $request,$slug) {
    $validator = Validator::make($request->all(), [ 
        'link_edit' => 'required|string'
    ]);

    if($validator->fails()) {
        return back()->withErrors($validator)->withInput()->with('modal','service-section-edit');
    }

    $validated = $validator->validate();

    $request->validate([
        'target'    => "required|string",
    ]);

    $basic_field_name = [
        'title_edit'     => "required|string|max:100", 
        'sub_title_edit'     => "required|string|max:100", 
    ];

    $slug = Str::slug(SiteSectionConst::APP_SECTION);
    $section = SiteSections::getData($slug)->first();
    if(!$section) return back()->with(['error' => ['Section not found!']]);
    $section_values = json_decode(json_encode($section->value),true);
    if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
    if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);
    
    $language_wise_data = $this->contentValidate($request,$basic_field_name,"app-edit");
    if($language_wise_data instanceof RedirectResponse) return $language_wise_data;
    
    $language_wise_data = array_map(function($language) {
        return replace_array_key($language,"_edit");
    },$language_wise_data);
    
    $section_values['items'][$request->target]['language'] = $language_wise_data; 
    $section_values['items'][$request->target]['link'] = $validated['link_edit'];
      //image upload 
      if($request->hasFile('image_edit')) {
        $image = get_files_from_fileholder($request,'image_edit');
        $upload = upload_files_from_path_dynamic($image,'site-section'); 
        $section_values['items'][$request->target]['image'] = $upload;
    } 
    try{
        $section->update([
            'value' => $section_values,
        ]);
    }catch(Exception $e) {
        return back()->with(['error' => ['Something went worng! Please try again']]);
    }

    return back()->with(['success' => ['Information updated successfully!']]);
}
public function appItemDelete(Request $request,$slug) {
    $request->validate([
        'target'    => 'required|string',
    ]);
    $slug = Str::slug(SiteSectionConst::APP_SECTION);
    $section = SiteSections::getData($slug)->first();
    if(!$section) return back()->with(['error' => ['Section not found!']]);
    $section_values = json_decode(json_encode($section->value),true);
    if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
    if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);
    try{
        unset($section_values['items'][$request->target]);
        $section->update([
            'value'     => $section_values,
        ]);
    }catch(Exception $e) {
        return  $e->getMessage();
    }

    return back()->with(['success' => ['Item delete successfully!']]);
}
//==========================App Section End====================================
    /**
     * Method for show clients feedback section page
     * @param string $slug
     * @return view
     */
    public function clientsFeedbackView($slug) {
        $page_title = "Testimonial Section";
        $section_slug = Str::slug(SiteSectionConst::CLIENT_FEEDBACK_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.clients-feedback-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update clients feedback section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function clientsFeedbackUpdate(Request $request,$slug) {
        $basic_field_name = [
            'heading' => "required|string|max:100",
            'sub_heading' => "required|string|max:255",
        ];

        $slug = Str::slug(SiteSectionConst::CLIENT_FEEDBACK_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }

        $section_data['language']  = $this->contentValidate($request,$basic_field_name);

        $update_data['key']    = $slug;
        $update_data['value']  = $section_data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully!']]);
    }

    /**
     * Method for store clients feedback item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function clientsFeedbackItemStore(Request $request,$slug) {

        $basic_field_name = [
            'comment'    => "required|string|max:1000",
        ];

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"client-feedback-add");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::CLIENT_FEEDBACK_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }
        $unique_id = uniqid();

        // request data validate
        $validator = Validator::make($request->all(),[
            'name'              => "required|string|max:255",
            'designation'       => "required|string|max:500",
            'image'             => "nullable|image|mimes:jpg,png,svg,webp|max:10240",
            'star'              => "required|integer|gt:0|lt:6"
        ]);
        if($validator->fails()) return back()->withErrors($validator->errors())->withInput()->with('modal','client-feedback-add');
        $validated = $validator->validate();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id']            = $unique_id;
        $section_data['items'][$unique_id]['image']         = "";
        $section_data['items'][$unique_id]['name']          = $validated['name'];
        $section_data['items'][$unique_id]['designation']   = $validated['designation'];
        $section_data['items'][$unique_id]['star']          = $validated['star'];

        if($request->hasFile("image")) {
            $section_data['items'][$unique_id]['image'] = $this->imageValidate($request,"image",$section->value->items->image ?? null);
        }

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Section item added successfully!']]);
    }

    /**
     * Method for update testimonial item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function clientsFeedbackItemUpdate(Request $request,$slug) {
        $validator = Validator::make($request->all(),[
            'target'                => "required|string",
            'name_edit'             => "required|string|max:255",
            'designation_edit'      => "required|string|max:500",
            'star_edit'             => "required|integer|gt:0|lt:6",
            'image_edit'            => "nullable|image|mimes:jpg,png,svg,webp|max:10240",
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput()->with('modal','client-feedback-update');
        }

        $validated = $validator->validate();

        $basic_field_name = [
            'comment_edit'     => "required|string|max:1000",
        ];

        $slug = Str::slug(SiteSectionConst::CLIENT_FEEDBACK_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"client-feedback-update");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function($language) {
            return replace_array_key($language,"_edit");
        },$language_wise_data);

        $section_values['items'][$request->target]['language']          = $language_wise_data;
        $section_values['items'][$request->target]['name']              = $request->name_edit;
        $section_values['items'][$request->target]['designation']       = $request->designation_edit;
        $section_values['items'][$request->target]['star']              = $request->star_edit;

        $section_values['items'][$request->target]['image']     = $section_values['items'][$request->target]['image'] ?? "";
        if($request->hasFile("image_edit")) {
            $section_values['items'][$request->target]['image'] = $this->imageValidate($request,"image_edit",$section_values['items'][$request->target]['image'] ?? null);
        }

        try{
            $section->update([
                'value' => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Information updated successfully!']]);
    }

    /**
     * Method for delete testimonial item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function clientsFeedbackItemDelete(Request $request,$slug) {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::CLIENT_FEEDBACK_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        try{
            $image_link = get_files_path('site-section') . '/' . $section_values['items'][$request->target]['image'];
            unset($section_values['items'][$request->target]);
            delete_file($image_link);
            $section->update([
                'value'     => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section item delete successfully!']]);
    }

    /**
     * Method for show announcement section page
     * @param string $slug
     * @return view
     */
    public function announcementView($slug) {
        $page_title = "Web Journal Section";
        $section_slug = Str::slug(SiteSectionConst::ANNOUNCEMENT_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        $announcements = Announcement::get();
        $categories = AnnouncementCategory::get();

        $total_categories = $categories->count();
        $active_categories = $categories->where("status",GlobalConst::ACTIVE)->count();

        $total_announcements = $announcements->count();
        $active_announcements = $announcements->where("status",GlobalConst::ACTIVE)->count();

        // dd($announcements,$categories);

        return view('admin.sections.setup-sections.announcement-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
            'total_categories',
            'active_categories',
            'total_announcements',
            'active_announcements',
        ));
    }

    /**
     * Method for update announcement update section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function announcementUpdate(Request $request,$slug) {
        $basic_field_name = [
            'heading' => "required|string|max:100",
            'sub_heading' => "required|string|max:255",
        ];

        $slug = Str::slug(SiteSectionConst::ANNOUNCEMENT_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }

        $section_data['language']  = $this->contentValidate($request,$basic_field_name);

        $update_data['key']    = $slug;
        $update_data['value']  = $section_data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully!']]);
    }


    /**
     * Method for show How It Work section page
     * @param string $slug
     * @return view
     */
    public function howItWorkView($slug) {
        $page_title = "How It Work Section";
        $section_slug = Str::slug(SiteSectionConst::HOW_IT_WORK_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.how-it-work-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update How It Work section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function howItWorkUpdate(Request $request,$slug) {

        $basic_field_name = [
            'heading'       => "required|string|max:100",
            'sub_heading'   => "required|string|max:255",
            'description'   => "required|string|max:1000",
            'content'      => "required|string|max:3000",
            'button_name'   => "required|string|max:50",
            'button_link'   => "required|string|max:255"
        ];

        $slug = Str::slug(SiteSectionConst::HOW_IT_WORK_SECTION);
        $section = SiteSections::where("key",$slug)->first();
        $data['image'] = $section->value->image ?? null;
        if($request->hasFile("image")) {
            $data['image']      = $this->imageValidate($request,"image",$section->value->image ?? null);
        }

        $data['language']  = $this->contentValidate($request,$basic_field_name);
        $update_data['value']  = $data;
        $update_data['key']    = $slug;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully!']]);
    }

    /**
     * Method for show footer section page
     * @param string $slug
     * @return view
     */
    public function footerView($slug) {
        $page_title = "Footer Section";
        $section_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.footer-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update footer section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function footerUpdate(Request $request,$slug) {
        $basic_field_name = [
            'contact_desc'      => "required|string|max:1000",
        ];

        $data['contact']['language']   = $this->contentValidate($request,$basic_field_name);

        $validated = Validator::make($request->all(),[
            'icon'              => "required|array",
            'icon.*'            => "required|string|max:200",
            'link'              => "required|array",
            'link.*'            => "required|string|url|max:255",
        ])->validate();

        // generate input fields
        $social_links = [];
        foreach($validated['icon'] as $key => $icon) {
            $social_links[] = [
                'icon'          => $icon,
                'link'          => $validated['link'][$key] ?? "",
            ];
        }

        $data['contact']['social_links']    = $social_links;

        $slug = Str::slug(SiteSectionConst::FOOTER_SECTION);

        try{
            SiteSections::updateOrCreate(['key' => $slug],[
                'key'   => $slug,
                'value' => $data,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Section updated successfully!']]);
    }

    /**
     * Method for show about page section page
     * @param string $slug
     * @return view
     */
    public function aboutPageView($slug) {
        $page_title = "About Page Section";
        $section_slug = Str::slug(SiteSectionConst::ABOUT_PAGE_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.about-page-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update about page section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function aboutPageUpdate(Request $request,$slug) {

        $basic_field_name = [
            'heading'       => "required|string|max:100",
            'sub_heading'   => "required|string|max:255",
            'desc'          => "required|string|max:2000",
            'button_name'   => "nullable|string|max:60",
            'button_link'   => "nullable|string|max:255",
        ];

        $slug = Str::slug(SiteSectionConst::ABOUT_PAGE_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        if($section != null) {
            $data = json_decode(json_encode($section->value),true);
        }else {
            $data = [];
        }

        $data['image'] = $section->value->image ?? null;
        if($request->hasFile("image")) {
            $data['image']      = $this->imageValidate($request,"image",$section->value->image ?? null);
        }

        $data['language']  = $this->contentValidate($request,$basic_field_name);
        $update_data['value']  = $data;
        $update_data['key']    = $slug;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully!']]);
    } 
    /**
     * Method for show service page section page
     * @param string $slug
     * @return view
     */
    public function servicePageView($slug) {
        $page_title = "Service Page Section";
        $section_slug = Str::slug(SiteSectionConst::SERVICE_PAGE_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.service-page-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update service page section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function servicePageUpdate(Request $request,$slug) {

        $basic_field_name = [
            'heading'       => "required|string|max:100", 
            'desc'          => "required|string|max:2000", 
        ];

        $slug = Str::slug(SiteSectionConst::SERVICE_PAGE_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        if($section != null) {
            $data = json_decode(json_encode($section->value),true);
        }else {
            $data = [];
        } 

        $data['language']  = $this->contentValidate($request,$basic_field_name);
        $update_data['value']  = $data;
        $update_data['key']    = $slug;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully!']]);
    }

    /**
     * Method for store service page item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function servicePageItemStore(Request $request,$slug) {

        $basic_field_name = [ 
            'description'   => "required|string|max:2000",
        ];

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"service-page-item-add");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::SERVICE_PAGE_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Section item added successfully!']]);
    }

    /**
     * Method for update service page item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function servicePageItemUpdate(Request $request,$slug) {

        $request->validate([
            'target'        => "required|string"
        ]);

        $basic_field_name = [ 
            'description_edit'   => "required|string|max:3000",
        ];

        $slug = Str::slug(SiteSectionConst::SERVICE_PAGE_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"service-page-item-edit");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function($language) {
            return replace_array_key($language,"_edit");
        },$language_wise_data);

        $section_values['items'][$request->target]['language'] = $language_wise_data;

        try{
            $section->update([
                'value' => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Information updated successfully!']]);
    }

    /**
     * Method for delete service page item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function servicePageItemDelete(Request $request,$slug) {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::SERVICE_PAGE_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        try{
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section item delete successfully!']]);
    }

    /**
     * Method for show contact us section page
     * @param string $slug
     * @return view
     */
    public function contactUsView($slug) {
        $page_title = "Contact US Section";
        $section_slug = Str::slug(SiteSectionConst::CONTACT_US_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.contact-us-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update contact us section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function contactUsUpdate(Request $request,$slug) {
        $basic_field_name = [
            'heading'       => "required|string|max:100",
            'sub_heading'   => "required|string|max:255",
        ];

        $slug = Str::slug(SiteSectionConst::CONTACT_US_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }

        $section_data['image'] = $section->value->image ?? null;
        if($request->hasFile("image")) {
            $section_data['image']      = $this->imageValidate($request,"image",$section->value->image ?? null);
        }

        $section_data['language']  = $this->contentValidate($request,$basic_field_name);

        $update_data['key']    = $slug;
        $update_data['value']  = $section_data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully!']]);
    }
         /**
     * Method for store feature item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function contactItemStore(Request $request,$slug) {

        $basic_field_name = [
            'title'         => "required|string|max:255",
            'details'   => "required|string|max:500",
        ];

        $validator = Validator::make($request->all(),[
            'icon'      => "required|string|max:255",
        ]);
        if($validator->fails()) return back()->withErrors($validator)->withInput()->with('modal','feature-item-add');
        $validated = $validator->validate();

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"feature-item-add");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::CONTACT_US_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;
        $section_data['items'][$unique_id]['icon'] = $validated['icon'];

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Section item added successfully!']]);
    }

    /**
     * Method for update feature item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function contactItemUpdate(Request $request,$slug) {

        $request->validate([
            'target'        => "required|string",
            'icon_edit'     => "required|string|max:255",
        ]);

        $basic_field_name = [
            'title_edit'     => "required|string|max:255",
            'details_edit'   => "required|string|max:500",
        ];

        $slug = Str::slug(SiteSectionConst::CONTACT_US_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"feature-item-edit");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function($language) {
            return replace_array_key($language,"_edit");
        },$language_wise_data);

        $section_values['items'][$request->target]['language'] = $language_wise_data;
        $section_values['items'][$request->target]['icon']    = $request->icon_edit;

        try{
            $section->update([
                'value' => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Information updated successfully!']]);
    }

    /**
     * Method for delete feature item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function contactItemDelete(Request $request,$slug) {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::CONTACT_US_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        try{
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section item delete successfully!']]);
    }

    /**
     * Method for get languages form record with little modification for using only this class
     * @return array $languages
     */
    public function languages() {
        $languages = Language::whereNot('code',LanguageConst::NOT_REMOVABLE)->select("code","name")->get()->toArray();
        $languages[] = [
            'name'      => LanguageConst::NOT_REMOVABLE_CODE,
            'code'      => LanguageConst::NOT_REMOVABLE,
        ];
        return $languages;
    }

    /**
     * Method for validate request data and re-decorate language wise data
     * @param object $request
     * @param array $basic_field_name
     * @return array $language_wise_data
     */
    public function contentValidate($request,$basic_field_name,$modal = null) {
        $languages = $this->languages();

        $current_local = get_default_language_code();
        $validation_rules = [];
        $language_wise_data = [];
        foreach($request->all() as $input_name => $input_value) {
            foreach($languages as $language) {
                $input_name_check = explode("_",$input_name);
                $input_lang_code = array_shift($input_name_check);
                $input_name_check = implode("_",$input_name_check);
                if($input_lang_code == $language['code']) {
                    if(array_key_exists($input_name_check,$basic_field_name)) {
                        $langCode = $language['code'];
                        if($current_local == $langCode) {
                            $validation_rules[$input_name] = $basic_field_name[$input_name_check];
                        }else {
                            $validation_rules[$input_name] = str_replace("required","nullable",$basic_field_name[$input_name_check]);
                        }
                        $language_wise_data[$langCode][$input_name_check] = $input_value;
                    }
                    break;
                } 
            }
        }
        if($modal == null) {
            $validated = Validator::make($request->all(),$validation_rules)->validate();
        }else {
            $validator = Validator::make($request->all(),$validation_rules);
            if($validator->fails()) {
                return back()->withErrors($validator)->withInput()->with("modal",$modal);
            }
            $validated = $validator->validate();
        }

        return $language_wise_data;
    }

    /**
     * Method for validate request image if have
     * @param object $request
     * @param string $input_name
     * @param string $old_image
     * @return boolean|string $upload
     */
    public function imageValidate($request,$input_name,$old_image) {
        if($request->hasFile($input_name)) {
            $image_validated = Validator::make($request->only($input_name),[
                $input_name         => "image|mimes:png,jpg,webp,jpeg,svg",
            ])->validate();

            $image = get_files_from_fileholder($request,$input_name);
            $upload = upload_files_from_path_dynamic($image,'site-section',$old_image);
            return $upload;
        }

        return false;
    }
    //======================Faq section Start =================================
public function faqView($slug) {
    $page_title = "FAQ Section";
    $section_slug = Str::slug(SiteSectionConst::FAQ_SECTION);
    $data = SiteSections::getData($section_slug)->first();
    $languages = $this->languages;

    return view('admin.sections.setup-sections.faq-section',compact(
        'page_title',
        'data',
        'languages',
        'slug',
    ));
}

public function faqUpdate(Request $request,$slug) {
    $basic_field_name = [
        'heading' => "required|string|max:100", 
        'details' => "required|string", 
    ];

    $slug = Str::slug(SiteSectionConst::FAQ_SECTION);
    $section = SiteSections::where("key",$slug)->first();
    if($section != null) {
        $data = json_decode(json_encode($section->value),true);
    }else {
        $data = [];
    }
    
    $data['language']  = $this->contentValidate($request,$basic_field_name);
    
    $update_data['key']    = $slug;
    $update_data['value']  = $data;

    try{
        SiteSections::updateOrCreate(['key' => $slug],$update_data);
    }catch(Exception $e) {
        return back()->with(['error' => ['Something went worng! Please try again.']]);
    }

    return back()->with(['success' => ['Section updated successfully!']]);
}

public function faqItemStore(Request $request,$slug) {
    $basic_field_name = [
        'question' => "required|string|max:200",
        'answer' => "required|string",
    ];


    $language_wise_data = $this->contentValidate($request,$basic_field_name,"faq-add");
    if($language_wise_data instanceof RedirectResponse) return $language_wise_data;
    $slug = Str::slug(SiteSectionConst::FAQ_SECTION);
    $section = SiteSections::where("key",$slug)->first();

    if($section != null) {
        $section_data = json_decode(json_encode($section->value),true);
    }else {
        $section_data = [];
    }
    $unique_id = uniqid();
    
    $section_data['items'][$unique_id]['language'] = $language_wise_data;
    $section_data['items'][$unique_id]['id'] = $unique_id;
    
    $update_data['key'] = $slug;
    $update_data['value']   = $section_data;

    try{
        SiteSections::updateOrCreate(['key' => $slug],$update_data);
    }catch(Exception $e) {
        return back()->with(['error' => ['Something went worng! Please try again']]);
    }

    return back()->with(['success' => ['Item added successfully!']]);
}


public function faqItemUpdate(Request $request,$slug) {
    $request->validate([
        'target'    => "required|string",
    ]);

    $basic_field_name = [
        'question_edit'     => "required|string|max:100",
        'answer_edit'     => "required|string",
    ];

    $slug = Str::slug(SiteSectionConst::FAQ_SECTION);
    $section = SiteSections::getData($slug)->first();
    if(!$section) return back()->with(['error' => ['Section not found!']]);
    $section_values = json_decode(json_encode($section->value),true);
    if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
    if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);
    

    $language_wise_data = $this->contentValidate($request,$basic_field_name,"faq-edit");
    if($language_wise_data instanceof RedirectResponse) return $language_wise_data;
    
    $language_wise_data = array_map(function($language) {
        return replace_array_key($language,"_edit");
    },$language_wise_data);
    
    $section_values['items'][$request->target]['language'] = $language_wise_data;
    try{
        $section->update([
            'value' => $section_values,
        ]);
    }catch(Exception $e) {
        return back()->with(['error' => ['Something went worng! Please try again']]);
    }

    return back()->with(['success' => ['Information updated successfully!']]);
}

public function faqItemDelete(Request $request,$slug) {
    $request->validate([
        'target'    => 'required|string',
    ]);
    $slug = Str::slug(SiteSectionConst::FAQ_SECTION);
    $section = SiteSections::getData($slug)->first();
    if(!$section) return back()->with(['error' => ['Section not found!']]);
    $section_values = json_decode(json_encode($section->value),true);
    if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
    if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);
    try{
        unset($section_values['items'][$request->target]);
        $section->update([
            'value'     => $section_values,
        ]);
    }catch(Exception $e) {
        return  $e->getMessage();
    }

    return back()->with(['success' => ['Item delete successfully!']]);
}
//=======================Faq  Section End===================================
}
