@extends('layouts.admin')

@section('content')
    <h1 class="mt-0 text-3xl font-semibold tracking-tight text-foreground">Edit skill</h1>

    <form method="POST" action="{{ route('admin.skills.update', $skill) }}" class="section-card mt-8 space-y-6">
        @csrf
        @method('PUT')
        @include('admin.skills._form')
    </form>
@endsection
