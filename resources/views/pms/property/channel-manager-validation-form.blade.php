@extends('pms.layouts.app')
@section('content')

    <style>
        .form-multiselect .dropdown-menu,
        .dropdown-menu {
            max-height: 300px;
            overflow-y: auto;
        }

        #property-field {
            display: none;
        }

        .toggle-password {
            cursor: pointer;
        }

        #company, #withoutCompany {
            display: none;
            flex-wrap: wrap;
        }

        #withoutCompany {
            display: flex; /* Show by default */
        }
    </style>

    <section class="section">
        <div class="container-fluid">
            <div class="title">
                <div class="row gx-2 align-items-center">
                    <div class="col">
                        <h1 class="fs-5 mb-0">
                            Validate Minimum Content for Property according to Channel Manager
                        </h1>
                    </div>
                    
                </div>
            </div>

            <form method="post" action="{{ route('pms.property.channel.manager.validate') }}">
                @csrf
                <div class="content-box p-3">
                    <div class="form-box">
                        @if(session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-6">
                                <div class="form-field">
                                    <label for="channel_manager">Channel Manager<sup>*</sup></label>
                                    <select name="channel_manager" id="channel_manager"
                                        class="form-control @error('channel_manager') is-invalid @enderror select2">
                                        <option value="">Select Channel Manager</option>
                                        
                                        @foreach($channelManagerArray as $channelManager)
                                            <option value="{{ $channelManager['AgentID'] }}" {{ old('channel_manager') == $channelManager['AgentID'] ? 'selected' : '' }}>
                                                {{ $channelManager['Name'] }}
                                            </option>
                                        @endforeach
                                        
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-6">
                                <div class="form-field">
                                    <label for="property">Property<sup>*</sup></label>
                                    <select name="property" id="property"
                                        class="form-control @error('property') is-invalid @enderror select2">
                                        <option value="">Select Property</option>
                                        @foreach($properties as $property)
                                            <option value="{{ $property->ru_property_id }}" {{ old('property') == $property->ru_property_id ? 'selected' : '' }}>
                                                {{ $property->unit_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="btn-wrap pt-2">
                    <button class="btn btn-primary px-5">Validate</button>
                </div>
            </form>
        </div>
    </section>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#channel_manager').select2({
                placeholder: 'Select Channel Manager',
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endsection