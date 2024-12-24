@extends('developer.layouts.master') 
@section('content')  
    <div class="developer-main-wrapper">
            <div class="row mb-30-none">
                <div class="col-lg-6 mb-30">
                    <h1 class="heading-title mb-20">Get Access Token</h1>
                    <p>Get access token to initiates payment transaction.</p>
                    <div class="mb-10">
                        <strong>Endpoint:</strong> <span class="badge rounded-pill bg--base">POST</span> <code class="fw-bold fs-6" style="color: #EE8D1D;"><code>@{{base_url}}</code>/authentication/token</code>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                              <tr>
                                <th scope="col">Parameter</th>
                                <th scope="col">Type</th>
                                <th scope="col">Comments</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <th scope="row">client_id</th>
                                <td>string</td>
                                <td>Enter merchant API client/primary key</td>
                              </tr>
                              <tr>
                                <th scope="row">secret_id</th>
                                <td>string</td>
                                <td>Enter merchant API secret key</td>
                              </tr>
                            </tbody>
                          </table>
                    </div>
                </div>
                <div class="col-lg-6 mb-30">
                    <span class="mb-10">Just request to that endpoint with all parameter listed below:</span>
                    <pre class="prettyprint mt-0" style="white-space: normal;">
                        <span class="code-show-list">
                            <span>Request Example (guzzle)</span>
                            <br>
                            <span>
                                <br>?php
                                <br> require_once('vendor/autoload.php');
                                <br> $client = new \GuzzleHttp\Client();
                                <br> $response = $client->request('POST', '@{{base_url}}/authentication/token', [
                                <br> “client_id”: "tRCDXCuztQzRYThPwlh1KXAYm4bG3rwWjbxM2R63kTefrGD2B9jNn6JnarDf7ycxdzfnaroxcyr5cnduY6AqpulRSebwHwRmGerA",
                                <br> “secret_id”: "oZouVmqHCbyg6ad7iMnrwq3d8wy9Kr4bo6VpQnsX6zAOoEs4oxHPjttpun36JhGxDl7AUMz3ShUqVyPmxh4oPk3TQmDF7YvHN5M3",
                                <br>],
                                <br>'headers' => [
                                <br>;'accept' => 'application/json',
                                <br>'content-type' => 'application/json',
                                <br>;],
                                <br>]);
                                <br>echo $response->getBody();
                            </span>
                        </span>
                    </pre>
                    <pre class="prettyprint mt-0" style="white-space: normal;">
                        <span class="code-show-list">
                            <br>**Response: SUCCESS (200 OK)**
                            <br>{
                            <br>"message": {
                            <br>"code": 200,
                            <br>"success": [
                            <br>"SUCCESS"
                            <br>]
                            <br>},
                            <br>"data": {
                            <br>"access_token":"nyXPO8Re5SXP1c5gMqHbW6DQ5BfQdbYGpuWVjEQAP76SUT7YfdngoFzDGSNHTvmzq8AjPRrCyzxzukrJvOlSSwtAPAqjvAQJdse4YOnlHasD3vg6EYg6qyKxSiHeXBoRluD2NbZzxN3sAYVqd9q1XCAl7oaW3BbJl2ktEQWBUuNYMZPQaDyNEGwxoY389TCNJvxVcroveYxPJkYANvnaxOy16aE9Qp6EBClSjvK17WR3cJupTXlUhgw9ddpv1gDSlbDJvzKutrQX7XJqwk1GW1Dm6aK4PTn1D4mvMVqiOqQKigTzcEi2KPQnkoM86ONw3X8SxttFOfesdSwxKJMXuQpdnFHOjo",
                            <br>"expire_time": 600
                            <br>},
                            <br>"type": "success"
                            <br>}
                        </span>
                    </pre>
                    <pre class="prettyprint mt-0" style="white-space: normal;">
                        <span class="code-show-list">
                            <br>**Response: ERROR (400 FAILED)**
                            <br>{
                            <br>"message": {
                            <br>"code": 400,
                            <br>"error": [
                            <br>"Invalid secret ID"
                            <br>]
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
                <a href="{{ setRoute("developer.baseUrl") }}" class="left"><i class="las la-arrow-left me-1"></i>  Base URL</a>
                <a href="{{ setRoute("developer.initiatePayment") }}" class="right">Initiate Payment <i class="las la-arrow-right ms-1"></i></a>
            </div>
             
        </div>
@endsection