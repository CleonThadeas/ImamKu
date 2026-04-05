@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <script>window.location.href = '{{ auth()->user()->isAdmin() ? route("admin.dashboard") : route("imam.dashboard") }}';</script>
@endsection
