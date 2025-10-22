@extends('layouts.app')

@section('title', 'Dashboard')
@section('page', 'Data User')
@section('card', 'List')

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
                            <div class="table-responsive mt-0">
                                <table class="table table-borderless table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Nama</th>
                                            <th>Usename</th>
                                            <th>Kode User</th>
                                            <th>Telpon</th>
                                            <th>Email</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->username }}</td>
                                                <td>{{ $item->code_name }}</td>
                                                <td><a href="https://wa.me/{{ $item->phone }}" target="_blank" rel="noopener noreferrer">{{ $item->phone }}</a></td>
                                                <td><a href="mailto:{{ $item->email }}" target="_blank" rel="noopener noreferrer">{{ $item->email }}</a></td>
                                                <td>
                                                    <button onclick="window.location.href=''" class="btn btn-sm btn-info">
                                                        <span class="fas fa-eye"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div> <!-- card-body -->
                    </div> <!-- card -->
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')

@endpush
