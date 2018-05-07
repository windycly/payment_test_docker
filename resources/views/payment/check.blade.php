<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Check Payment</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    </head>
    <body>
        <div class="container">
            <div class="col-12 col-md-8 offset-md-2 pt-3">
                <h1>Check Payment Form</h1>

                <form>
                    <div class="form-group">
                        <label for="customer_name">Customer Name</label>
                        <input type="text" class="form-control" id="customer_name" placeholder="Enter your name">
                    </div>
                    <div class="form-group">
                        <label for="reference_code">Reference Code</label>
                        <input type="text" class="form-control" id="reference_code" placeholder="Reference code">
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

                $.post( "check", {
                    'customer_name': $('#customer_name').val(),
                    'reference_code': $('#reference_code').val(),
                })
                .done(function( data ) {

                    if (data.error) {
                        let msg = [];
                        msg.push("<strong>Error</strong>");
                        $.each(data.data, function( i, d ) { msg.push(d.join(' ')); });
                        $('#infoModal .modal-body').html(msg.join("<br>"));
                        $('#infoModal').modal('show');
                    }
                    else {
                        $( ".btn-submit" ).unbind();
                        $('body .container').html(data.data);
                    }
                });

                event.preventDefault();
            });

        </script>
    </body>
</html>
