<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Make Payment Form</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    </head>
    <body>
        <div class="container">
            <div class="col-12 col-md-8 offset-md-2 pt-3">
                <h1>Make Payment Form</h1>

                <form>
                    <h3 class="text-center">Order</h3>
                    <div class="form-group">
                        <label for="customer_name">Customer Name</label>
                        <input type="text" class="form-control" id="customer_name" placeholder="Enter your name">
                    </div>
                    <div class="form-group">
                        <label for="customer_phone">Customer Phone</label>
                        <input type="text" class="form-control" id="customer_phone" placeholder="Enter your phone number">
                    </div>
                    <div class="form-group">
                        <label for="currency">Currency</label>
                        <select class="form-control" id="currency">
                            <option value="HKD">HKD</option>
                            <option value="USD">USD</option>
                            <option value="AUD">AUD</option>
                            <option value="EUR">EUR</option>
                            <option value="JPY">JPY</option>
                            <option value="CNY">CNY</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="text" class="form-control" id="price" placeholder="Price">
                    </div>
                    <hr>
                    <h3 class="text-center">Payment</h3>
                    <div class="form-group">
                        <label for="card_holder">Card Holder Name</label>
                        <input type="text" class="form-control" id="card_holder" placeholder="Card Holder Name">
                    </div>
                    <div class="form-group">
                        <label for="card_numbers">Credit Card Number</label>
                        <input type="text" class="form-control" id="card_numbers" placeholder="Credit Card Number">
                    </div>
                    <div class="form-group">
                        <label for="card_expiration">Card Expiration</label>
                        <input type="text" class="form-control" id="card_expiration" placeholder="MM/YY">
                    </div>
                    <div class="form-group">
                        <label for="card_cvv">Card Security Code (CVV)</label>
                        <a href="https://www.cvvnumber.com/cvv.html" target="_blank" style="font-size:11px" class="what-is-cvv">What is my CVV code?</a>
                        <input type="text" class="form-control" id="card_cvv" placeholder="Security Code (We will not store your code)">
                    </div>
                    <button class="btn btn-primary btn-submit">Submit</button>
                </form>
            </div>
        </div>


        <!-- Modal to show information -->
        <div id="infoModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">×</button>
                        <h4 class="modal-title"></h4>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">
                            <span class='glyphicon glyphicon-remove'></span> Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal to show success -->
        <div id="successModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary go-check" data-dismiss="modal">
                            <span class='glyphicon glyphicon-remove'></span> Check Payment
                        </button>
                        <button type="button" class="btn btn-info next-payment" data-dismiss="modal">
                            <span class='glyphicon glyphicon-remove'></span> Make Next Payment
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

        <script type="text/javascript">
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.btn-submit').click(function(event) {

                $.post( "payments", {
                    'customer_name': $('#customer_name').val(),
                    'customer_phone': $('#customer_phone').val(),
                    'currency': $('#currency').val(),
                    'price': $('#price').val(),

                    'card_holder': $('#card_holder').val(),
                    'card_numbers': $('#card_numbers').val(),
                    'card_expiration': $('#card_expiration').val(),
                    'card_cvv': $('#card_cvv').val(),
                })
                .done(function( data ) {
                    let msg = [];
                    if (data.error) {
                        msg.push("<strong>Form input is invalid!</strong>");
                        $.each(data.data, function( i, d ) { msg.push(d.join(' ')); });
                        $('#infoModal .modal-body').html(msg.join("<br>"));
                        $('#infoModal').modal('show');
                    }
                    else if (!data.data.is_success) {
                        msg.push("<strong>Payment failed!</strong>");
                        msg.push(data.data.response_data);
                        $('#infoModal .modal-body').html(msg.join("<br>"));
                        $('#infoModal').modal('show');
                    }
                    else {
                        msg.push("<strong>Payment success!</strong>");
                        msg.push("Reference Code: " + data.data.reference_code);
                        $('#successModal .modal-body').html(msg.join("<br>"));
                        $('#successModal').modal({
                	        backdrop: 'static'
                		});
                    }
                });

                event.preventDefault();
            });

            $('.go-check').click(function(event) {
                window.location = 'check';
                event.preventDefault();
            });
            $('.next-payment').click(function(event) {
                window.location.reload();
                event.preventDefault();
            });
            $('.what-is-cvv').click(function(event) {
                window.open(this.href, '_blank', 'location=yes,height=420,width=650,scrollbars=no,status=yes');
                event.preventDefault();
            });
        </script>
    </body>
</html>
