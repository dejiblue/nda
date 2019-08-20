@extends('layouts.app')

@section('content')
    <div class="container">
        <div id="importSection" class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Import Contacts</div>
                    @if ($toImport > 0)
                        <p>Contacts left to import: {{ $import_remainder }}</p>
                    @endif
                    <div class="card-body">
                        <p>{{ session('status') }}</p>

                        <form method="POST" action="{{ route('staff.import.contacts') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('file') ? ' has-error' : '' }}">
                                <label for="file" class="control-label">CSV file to import</label>

                                <input id="file" type="file" class="form-control" name="file" required>

                                @if ($errors->has('file'))
                                    <span class="help-block">
                                       <strong>{{ $errors->first('file') }}</strong>
                                    </span>
                                @endif

                            </div>

                            <p><button type="submit" class="btn btn-success" name="submit"><i class="fa fa-check"></i> Submit</button></p>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection