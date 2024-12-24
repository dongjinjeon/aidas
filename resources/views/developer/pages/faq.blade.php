@extends('developer.layouts.master') 
@section('content') 
<div class="developer-main-wrapper">
    <h1 class="heading-title mb-30">FAQ</h1>
    <div class="row">
        <div class="col-lg-8">
            <h4 class="mb-10">1. What is {{ @$basic_settings->site_name }}, and how does it work?</h4>
            <p class="ps-4 mb-40">{{ @$basic_settings->site_name }} is an advanced digital mobile wallet that allows you to store payment information securely on your mobile device. It works by enabling you to make quick and secure payments at supported merchants, both online and in-store, using your stored payment methods.</p>
            <h4 class="mb-10">2. Is {{ @$basic_settings->site_name }} safe to use?</h4>
            <p class="ps-4 mb-40">Yes, {{ @$basic_settings->site_name }} prioritizes security. It employs encryption and multi-factor authentication to protect your payment information. Plus, {{ @$basic_settings->site_name }} complies with industry standards for data security, ensuring your transactions are safe.</p>
            <h4 class="mb-10">3. How do I add my payment methods to {{ @$basic_settings->site_name }}?</h4>
            <p class="ps-4 mb-40">To add payment methods, open the {{ @$basic_settings->site_name }} app, navigate to the "Payment Methods" section, and follow the on-screen instructions to add your credit/debit cards or link your bank accounts.</p>
            <h4 class="mb-10">4. Can I use {{ @$basic_settings->site_name }} on multiple devices?</h4>
            <p class="ps-4 mb-40">Yes, {{ @$basic_settings->site_name }} is designed to be used on multiple devices. Simply log in to your {{ @$basic_settings->site_name }} account on the new device, and your payment methods and transaction history will be synchronized.</p>
            <h4 class="mb-10">5. What should I do if I lose my mobile device?</h4>
            <p class="ps-4 mb-40">If your device is lost or stolen, immediately contact {{ @$basic_settings->site_name }} customer support to freeze your account and prevent unauthorized access. You can also remotely wipe your payment data via our website.</p>
            <h4 class="mb-10">6. Are there any transaction fees associated with {{ @$basic_settings->site_name }}?</h4>
            <p class="ps-4 mb-40">{{ @$basic_settings->site_name }} may charge nominal transaction fees, which will be clearly displayed before you confirm a payment. These fees help support our service and ensure its continued availability.</p>
            <h4 class="mb-10">7. How can I track my transaction history in {{ @$basic_settings->site_name }}?</h4>
            <p class="ps-4 mb-40">You can view your transaction history within the {{ @$basic_settings->site_name }} app. Simply go to the "Transaction History" section to see a detailed list of all your past transactions.</p>
            <h4 class="mb-10">8. Does {{ @$basic_settings->site_name }} offer rewards or cashback programs?</h4>
            <p class="ps-4 mb-40">Yes, {{ @$basic_settings->site_name }} may offer rewards and cashback programs with participating merchants. Keep an eye on our promotions and offers section to take advantage of these benefits.  </p>
            <h4 class="mb-10">9. Can I use {{ @$basic_settings->site_name }} for international transactions??</h4>
            <p class="ps-4 mb-40">Yes, {{ @$basic_settings->site_name }} is accepted at many international merchants and supports multiple currencies. Be sure to check with the specific merchant to confirm international acceptance.</p>
            <h4 class="mb-10">10. How can I contact {{ @$basic_settings->site_name }} customer support for assistance?</h4>
            <p class="ps-4 mb-40"> You can reach {{ @$basic_settings->site_name }} customer support through our dedicated support email, phone number, or live chat on our website. We're here to assist you with any questions or concerns you may have.
            </p>
        </div>
    </div>
</div>
<div class="page-change-area">
    <div class="navigation-wrapper">
        <a href="{{ setRoute("developer.examples") }}" class="left"><i class="las la-arrow-left me-1"></i>  Examples</a>
        <a href="{{ setRoute("developer.support") }}" class="right">Support <i class="las la-arrow-right ms-1"></i></a>
    </div>
</div>
@endsection