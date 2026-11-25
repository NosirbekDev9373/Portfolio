@extends('layouts.admin')

@section('content')
    <h1 class="mt-0 text-3xl font-semibold tracking-tight text-foreground">New project</h1>

    <form method="POST" action="{{ route('admin.projects.store') }}" enctype="multipart/form-data" class="section-card mt-8 space-y-6">
        @csrf
        @include('admin.projects._form')
    </form>
@endsection
