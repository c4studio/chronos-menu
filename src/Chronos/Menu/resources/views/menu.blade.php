@extends('chronos::admin')

@push('styles')
<link href="{{ asset('chronos/css/menu.css?v=' . Config::get('menu.version')) }}" rel="stylesheet" />
@endpush