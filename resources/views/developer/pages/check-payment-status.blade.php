@extends('developer.layouts.master') 
@section('content') 
<div class="developer-main-wrapper">
    <h1 class="heading-title mb-20">Check Payment Status</h1>
    <p>Checks the status of a payment.</p>
    <pre class="prettyprint mt-0" style="white-space: normal;">
        <span class="code-show-list">
            <br>**Response: SUCCESS (200 OK)**
            <br>{
            <br>&nbsp;"message": {
            <br>&nbsp;"code": 200,
            <br>&nbsp;"success": [
            <br>&nbsp;&nbsp;"SUCCESS"
            <br>&nbsp;]
            <br>},
            <br>"data": {
            <br>&nbsp;"token": "2zMRmT3KeYT2BWMAyGhqEfuw4tOYOfGXKeyKqehZ8mF1E35hMwE69gPpyo3e",
            <br>&nbsp;"trx_id": "BP2c7sAvw75MTlrP",
            <br>&nbsp;"payer": {
            <br>&nbsp;&nbsp;"username": "testuser",
            <br>&nbsp;&nbsp;"email": "user@appdevs.net"
            <br>&nbsp;}
            <br>},
            <br>"type": "success"
            <br>}
        </span>
    </pre>
</div>
<div class="page-change-area">
     
    <div class="navigation-wrapper">
        <a href="{{ setRoute("developer.initiatePayment") }}" class="left"><i class="las la-arrow-left me-1"></i> Initiate Payment</a>
        <a href="{{ setRoute("developer.responseCodes") }}" class="right">Response Codes <i class="las la-arrow-right ms-1"></i></a>
    </div>
     
</div>
@endsection