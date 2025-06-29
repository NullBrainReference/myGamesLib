@if(session('success'))
    <div class="alert alert-success text-center mb-0">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger text-center mb-0">
        {{ session('error') }}
    </div>
@endif