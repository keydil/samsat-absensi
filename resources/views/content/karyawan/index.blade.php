@extends('layouts.app')

@section('title', 'Dashboard')
@section('page', 'Home User')
@section('card', 'Card Content')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>@yield('title')</h1>
            @include('partials.breadcrumb')
        </div>
        <div class="section-body">
            <h2 class="section-title">@yield('page')</h2>
            <div class="row">
                <div class="col-12 col-md-12 col-lg-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h4 class="section-title mt-0 mb-0">@yield('card')</h4>
                        </div>
                        <div class="card-body">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection