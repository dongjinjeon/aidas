@extends('developer.layouts.master') 
@section('content') 
<div class="developer-main-wrapper">
    <div class="row mb-30-none">
        <div class="col-lg-6 mb-30">
            <h1 class="heading-title mb-20">Initiate Payment</h1>
            <p>Initiates a new payment transaction.</p>
            <div class="mb-10">
                <strong>Endpoint:</strong> <span class="badge rounded-pill bg-primary">POST</span> <code class="fw-bold fs-6" style="color: #EE8D1D;"><code>@{{base_url}}</code>/payment/create</code>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                      <tr>
                        <th scope="col">Parameter</th>
                        <th scope="col">Type</th>
                        <th scope="col">Details</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <th scope="row">amount</th>
                        <td>decimal</td>
                        <td>Your Amount , Must be rounded at 2 precision.</td>
                      </tr>
                      <tr>
                        <th scope="row">currency</th>
                        <td>string</td>
                        <td>Currency Code, Must be in Upper Case (Alpha-3 code)</td>
                      </tr>
                      <tr>
                        <th scope="row">return_url:</th>
                        <td>string</td>
                        <td>Enter your return or success URL</td>
                      </tr>
                      <tr>
                        <th scope="row">cancel_url:</th>
                        <td>string (optional)</td>
                        <td>Enter your cancel or failed URL</td>
                      </tr>
                    </tbody>
                  </table>
            </div>
        </div>
        <div class="col-lg-6 mb-30">
            <pre class="prettyprint mt-0" style="white-space: normal;">
                <span class="code-show-list">
                    <span>Request Example (guzzle)</span>
                    <br>
                    <span>
                        <br>&lt;?php
                        <br> require_once('vendor/autoload.php');
                        <br> $client = new \GuzzleHttp\Client();
                        <br> $response = $client->request('POST', '{{"base_url"}}/payment/create', [
                        <br>&nbsp;&nbsp; “amount”: "100.00",
                        <br>&nbsp;&nbsp; “currency”: "USD",
                        <br>&nbsp;&nbsp; “return_url”: "www.example.com/success",
                        <br>&nbsp;&nbsp; “cancel_url”: "www.example.com/cancel",
                        <br>],
                        <br>'headers' => [
                        <br>&nbsp;&nbsp;'Authorization' => 'Bearer @{{access_token}}',
                        <br>&nbsp;&nbsp;'accept' => 'application/json',
                        <br>&nbsp;&nbsp;'content-type' => 'application/json',
                        <br>&nbsp;],
                        <br>]);
                        <br>echo $response->getBody();
                    </span>
                </span>
            </pre>
            <pre class="prettyprint mt-0" style="white-space: normal;">
                <span class="code-show-list">
                    <br>**Response: SUCCESS (200 OK)**
                    <br>{
                    <br>&nbsp;"message": {
                    <br>&nbsp;"code": 200,
                    <br>&nbsp;"success": [
                    <br>&nbsp;&nbsp;"CREATED"
                    <br>&nbsp;]
                    <br>},
                    <br>"data": {
                    <br>&nbsp;"token": "2zMRmT3KeYT2BWMAyGhqEfuw4tOYOfGXKeyKqehZ8mF1E35hMwE69gPpyo3e",
                    <br>&nbsp;"payment_url": "www.example.com/pay/sandbox/v1/user/authentication/form/2zMRmT3KeYT2BWMAyGhqEfuw4tOYOfGXKeyKqehZ8mF1E35hMwE69gPpyo3e",
                    <br>},
                    <br>"type": "success"
                    <br>}
                </span>
            </pre>
            <pre class="prettyprint mt-0" style="white-space: normal;">
                <span class="code-show-list">
                    <br>**Response: ERROR (403 FAILED)**
                    <br>{
                    <br>&nbsp;"message": {
                    <br>&nbsp;"code": 403,
                    <br>&nbsp;"error": [
                    <br>&nbsp;&nbsp;"Requested with invalid token!"
                    <br>&nbsp;]
                    <br>},
                    <br>"data": [],
                    <br>"type": "error"
                    <br>}
                </span>
            </pre>
        </div>
    </div>
</div>
<div class="page-change-area"> 
    <div class="navigation-wrapper">
        <a href="{{ setRoute("developer.accessToken") }}" class="left"><i class="las la-arrow-left me-1"></i>  Access Token</a>
        <a href="{{ setRoute("developer.checkPaymentStatus") }}" class="right">Check Payment Status <i class="las la-arrow-right ms-1"></i></a>
    </div>
</div>
@endsection