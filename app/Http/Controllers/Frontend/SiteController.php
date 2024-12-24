<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Admin\SetupPage;
use App\Models\Admin\UsefulLink;
use App\Models\Admin\WebJournal;
use App\Models\Admin\SiteSections;
use App\Constants\SiteSectionConst;
use App\Models\Admin\BasicSettings;
use App\Http\Controllers\Controller;
use App\Models\Frontend\Announcement;
use App\Models\Frontend\AnnouncementCategory;

class SiteController extends Controller
{
    public function home() {
        $basic_settings = BasicSettings::first();
        $page_title = setPageTitle($basic_settings->site_title) ?? "Home"; 
        return view('frontend.index',compact(
            'page_title',
        ));
    }
    public function developer() {
        $page_title = setPageTitle("Introduction"); 
        $basic_settings = BasicSettings::first();
        return view('frontend.pages.developer',compact('page_title','basic_settings'));
    }
    public function aboutUs() {
        $page_title = setPageTitle("About Us");
        $section_slug = Str::slug(SiteSectionConst::ABOUT_PAGE_SECTION);
        $about = SiteSections::getData($section_slug)->first();
        $setupPage = SetupPage::where('slug', 'about-us')->first(); 
        if($setupPage->status == false) return redirect()->route('index'); 
        return view('frontend.pages.about-us', compact('page_title','about'));
    }
    public function services()
    {
        $page_title = setPageTitle("Services");
        $section_slug = Str::slug(SiteSectionConst::SERVICE_PAGE_SECTION);
        $service = SiteSections::getData($section_slug)->first();

        $setupPage = SetupPage::where('slug', 'services')->first(); 
        if($setupPage->status == false) return redirect()->route('index');

        return view('frontend.pages.service', compact('page_title','service'));
    }
    public function contactUs()
    {
        $page_title = setPageTitle("Contact Us");
        $section_slug = Str::slug(SiteSectionConst::CONTACT_US_SECTION);
        $contact = SiteSections::getData($section_slug)->first();
        $setupPage = SetupPage::where('slug', 'contact-us')->first(); 
        if($setupPage->status == false) return redirect()->route('index');

        return view('frontend.pages.contact-us', compact('page_title','contact'));
    }
    public function webJournal()
    {
        $page_title = setPageTitle("Web Journal");   
        $blogs = Announcement::where('status',1)->orderByDesc("id")->get();

        $section_slug = Str::slug(SiteSectionConst::ANNOUNCEMENT_SECTION);
        $blog_section = SiteSections::getData($section_slug)->first();

        $setupPage = SetupPage::where('slug', 'web-journal')->first(); 
        if($setupPage->status == false) return redirect()->route('index');
        
        return view('frontend.pages.web-journal', compact('page_title','blogs','blog_section'));
    }
    public function webJournalDetails($id,$slug){
        $page_title = setPageTitle("Blog Details");
        $categories = AnnouncementCategory::where('status',1)->orderBy('id',"ASC")->get();
        $blog = Announcement::where('id',$id)->first();
        $recentPost = Announcement::where('status',1)->latest()->limit(3)->get();
        // dd($blog);
        return view('frontend.pages.web-journal-details',compact('page_title','blog','recentPost','categories'));
    }
    public function webJournalByCategory ($id){ 
        $defualt = get_default_language_code()??'en';
        $category = AnnouncementCategory::findOrfail($id); 
        $blogs = Announcement::where('status',1)->where('announcement_category_id',$category->id)->latest()->paginate(6);
        $page_title = setPageTitle($category->name->language->$defualt->name);
        $section_slug = Str::slug(SiteSectionConst::ANNOUNCEMENT_SECTION);
        $blog_section = SiteSections::getData($section_slug)->first();
        return view('frontend.pages.web-journal',compact('blogs','category','page_title','blog_section'));
    }
    public function pageView($slug)
    {
        $defualt = get_default_language_code()??'en';
        $page_data = UsefulLink::where('slug', $slug)->where('status', 1)->first();
        if(empty($page_data)){
            abort(404);
        }
        $page_title = $page_data->title->language->$defualt->title??"";

        return view('frontend.pages.index',compact('page_title','page_data'));
    }
    public function faq(){
        $page_title = "Faq";
        return view('frontend.pages.faq',compact('page_title'));
    }

    public function walletiumPaymentSuccess(Request $request){
        return $request->all();

    }
    public function walletiumPaymentCancel(Request $request){
        return $request->all();

    }
}
