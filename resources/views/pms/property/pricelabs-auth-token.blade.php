@extends('pms.layouts.app')
@section('content')
<section class="section">
    <div class="container-fluid">
         @if(isPriceLabEnable())
            <div class="title">
                <div class="row gx-2 align-items-center">
                    <div class="col">
                        <h1 class="fs-5 mb-0">Integrations</h1>
                    </div>
                    
                </div>
            </div>
        @endif    
        
        @if(isPriceLabEnable())

            <form method="post" action="{{ route('pms.save.pricelab.token') }}">
                @csrf
               
                <div class="content-box p-3">
                    <div class="form-box">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-field">
                                    <label for="code">PriceLabs Auth Token <sup>*</sup></label>
                                    <input type="text" name="user_token" id="user_token" value="{{ old('user_token', $token ?? '') }}" class="form-control  @error('user_token') is-invalid @enderror" >
                                </div>
                            </div>
                           
                        </div>
                    </div>
    
                    <div class="table-footer pt-3">
                        <div class="row align-items-center">
                            <div class="col-12">
                                <button type="submit" class="btn btn-save btn-primary">SUBMIT</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @else
            <form method="post" action="#">
              
               
                <div class="content-box p-3">
                    <div class="form-box">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <div class="form-field">
                                    <label for="code"><strong>Integration Not Enabled</strong></label>
                                    
                                </div>
                            </div>
                           
                        </div>
                    </div>
    
                    
                </div>
            </form>
        
        
        @endif
    </div>
</section>
@endsection

