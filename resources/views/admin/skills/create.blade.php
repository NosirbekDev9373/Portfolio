@extends('layouts.admin')

@section('content')
    <h1 class="mt-0 text-3xl font-semibold tracking-tight text-foreground">New skill</h1>

    <form method="POST" action="{{ route('admin.skills.store') }}" class="section-card mt-8 space-y-6">
        @csrf
        @include('admin.skills._form')
    </form>
@endsection
