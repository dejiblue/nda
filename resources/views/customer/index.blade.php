@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Customer Optin</div>
                    <div class="card-body">
                        @if(Session::has('message'))
                            <div class="{{ Session::get('message_type') }}">{{ Session::get('message') }}</div>
                        @endif
                        <p>{{ session('message') }}</p>

                        <form method="post" action="{{ route('customers.contact.save') }}" role="form">
                            {{ csrf_field() }}
                            <div class="controls">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="form_name">First Name</label>
                                            <input id="first_name" type="text" name="first_name" class="form-control" placeholder="Please enter your first name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="form_lastname">Last Name</label>
                                            <input id="last_name" type="text" name="last_name" class="form-control" placeholder="Please enter your last name">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="province">Province</label>
                                            <input id="province" type="text" name="province" class="form-control" placeholder="Please enter your province">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="form_email">Email</label>
                                            <input id="email" type="email" name="email" class="form-control" placeholder="Please enter your email">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input class="form-control" type="checkbox" name="opt_in" id="opt_in" value="1">
                                            <label for="opt_in">
                                                Opt-in
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group{{ $errors->has('g-recaptcha-response') ? ' has-error' : '' }}">
                                            <label class="col-md-4 control-label">Captcha</label>
                                            <div class="col-md-6 pull-center">
                                                {!! app('captcha')->display() !!}
                                                @if ($errors->has('g-recaptcha-response'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <input type="submit" class="btn btn-success btn-send" value="Send message">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    {!! NoCaptcha::renderJs() !!}
                </div>
            </div>
        </div>
    </div>
@endsection