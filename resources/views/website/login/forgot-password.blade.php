@extends('website.layouts.app')
@section('content')

<div class="cms-pages">
    <section class="section section-signin pt-5">
        <div class="container">
            <div class="form-wrap">
                <div class="card-info form-box form-inner-wrap form-box-login">
                    <form method="post" id="forgotpassword">
                        @csrf()
                        <div class="form-title">
                            <h1 class="h3">Forgot Password</h1>
                            <p>It's good to see you again!</p>
                        </div>    
                        <input type="hidden" class="form-control" name="uri" id="uri" value="">
                        <div class="row align-items-center g-3">
                            <div class="col-12">
                                <label for="">Email<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="email">
                                <div class="error text-danger mb-3" id="email_error"></div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary w-100" type="submit">
                                    Submit&nbsp;&nbsp;
                                    <span id="spinner_new_form" class="spinner-border spinner-border-sm " style="color: #fff; display: none;" role="status"  aria-hidden="true" ></span>
                                </button>
                            </div>
                        </div>                        
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>


<script>
    $(document).on('submit', '#forgotpassword', function(ev) {
        $('.error').html('');
        ev.preventDefault(); // Prevent browers default submit.
        var formData = new FormData(this);
        var error = false;
        var button = document.getElementById('submitbutton');
        var spinner = document.getElementById('spinner_new_form');
        spinner.style.display = 'inline-block';

        if (error == false) {
            $.ajax({
                url: "{{ url('submitforgotpassword') }} ",
                type: 'post',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $(".hstackloader").html('<lord-icon src="https://cdn.lordicon.com/dpinvufc.json" trigger="loop" colors="primary:#4bb543,secondary:#4bb543" style="width:50px;"> </lord-icon>');
                    $(".hstack").css('display', 'none');
                    $(".error").text('');
                },
                success: function(result) {
                    if (result.code == 200) {
                        spinner.style.display = 'none';
                        swal({
                            title: result.message,
                            text: "",
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "OK",
                            closeOnConfirm: false, 
                        }, function() {
                            let query = window.location.search;
                            if (query) {
                               // window.location.href = '/login' + '?uri=' + encodeURIComponent(result.url);
                               
                                // let params = new URLSearchParams(window.location.search);
                                // let slug = params.get("slug") || "";
                                // let ptype = params.get("ptype") || "";
                                // let check_in = params.get("check_in") || "";
                                // let check_out = params.get("check_out") || "";
                                // let adults = params.get("adults") || "";
                                // let children = params.get("children") || "";
                                // let total_guests = params.get("total_guests") || "0";
                                // let tot_no_of_days = params.get("tot_no_of_days") || "0";
    
                                // let newParams = new URLSearchParams();
                                // newParams.set("slug", slug);
                                // newParams.set("ptype", ptype);
                                // newParams.set("check_in", check_in);
                                // newParams.set("check_out", check_out);
                                // newParams.set("adults", adults);
                                // newParams.set("children", children);
                                // newParams.set("total_guests", total_guests);
                                // newParams.set("tot_no_of_days", tot_no_of_days);
                                window.location.href =  '/login' + (query ? query : '');
                               
                            } else {
                                window.location.href = '/login';
                            }
                        });
                        // swal(result.message, ' ', 'success');
                        // setTimeout(function() {
                        //     window.location.href = '/login';
                        // }, 2000);
                    } else if (result.code == 401) {
                         spinner.style.display = 'none';
                        $.each(result.message, function(prefix, val) {
                            $('#' + prefix + '_error').text(val[0]);
                        });
                        // swal(result.message, ' ', 'error');
                    } else {
                        spinner.style.display = 'none';
                        swal(result.message, ' ', 'error');
                    }
                },
                error: function(xhr) {
                    $(".hstack").css('display', 'flex');
                },
                complete: function() {
                    $(".hstack").css('display', 'flex');
                    $(".hstackloader").text('');
                },
            })
        }
    })
</script>

<script>
    // Check if the URL has a 'uri' parameter and populate the hidden input field
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const uri = urlParams.get('slug');
        if (uri) {
            document.getElementById('uri').value = uri;
        }
    });
</script>
@endsection
