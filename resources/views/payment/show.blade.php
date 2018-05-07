<div class="col-12 col-md-8 offset-md-2 pt-3">
    <h1>Your Payment Information</h1>
    <hr>

    <div class="row">
        <div class="col-6">Customer Name: </div>
        <div class="col-6">{{ $data['customer_name'] }}</div>
    </div>
    <div class="row">
        <div class="col-6">Customer Phone Number: </div>
        <div class="col-6">{{ $data['customer_phone'] }}</div>
    </div>
    <div class="row">
        <div class="col-6">Currency: </div>
        <div class="col-6">{{ $data['currency'] }}</div>
    </div>
    <div class="row">
        <div class="col-6">Price: </div>
        <div class="col-6">{{ $data['price'] }}</div>
    </div>

    <hr>
    <h3>Thank you!</h3>
</div>
