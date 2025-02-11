@extends('layouts.template_master')

@section('title', 'Mail Setting')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Mail Setting</h3>
                <p><span class="text-danger">*</span> <small>Fields are required</small></p>
            </div>
            <div class="card-body">
                <form class="forms-sample" action="{{ route('backend.mail.setting.update') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="mail_driver" class="form-label">Mail Driver <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="mail_driver" name="mail_driver" value="{{ old('mail_driver', $mailSetting->mail_driver) }}" placeholder="Mail Driver">
                            @error('mail_driver')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="mail_mailer" class="form-label">Mail Mailer <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="mail_mailer" name="mail_mailer" value="{{ old('mail_mailer', $mailSetting->mail_mailer) }}" placeholder="Mail Mailer">
                            @error('mail_mailer')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="mail_host" class="form-label">Mail Host <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="mail_host" name="mail_host" value="{{ old('mail_host', $mailSetting->mail_host) }}" placeholder="Mail Host">
                            @error('mail_host')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="mail_port" class="form-label">Mail Port <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="mail_port" name="mail_port" value="{{ old('mail_port', $mailSetting->mail_port) }}" placeholder="Mail Port">
                            @error('mail_port')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="mail_username" class="form-label">Mail Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="mail_username" name="mail_username" value="{{ old('mail_username', $mailSetting->mail_username) }}" placeholder="Mail Username">
                            @error('mail_username')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="mail_password" class="form-label">Mail Password <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="mail_password" name="mail_password" value="{{ old('mail_password', $mailSetting->mail_password) }}" placeholder="Mail Password">
                            @error('mail_password')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="mail_encryption" class="form-label">Mail Encryption <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="mail_encryption" name="mail_encryption" value="{{ old('mail_encryption', $mailSetting->mail_encryption) }}" placeholder="Mail Encryption">
                            @error('mail_encryption')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="mail_from_address" class="form-label">Mail From Address <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="mail_from_address" name="mail_from_address" value="{{ old('mail_from_address', $mailSetting->mail_from_address) }}" placeholder="Mail From Address">
                            @error('mail_from_address')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                    </div><!-- Row -->
                    <div class="row mt-3">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
