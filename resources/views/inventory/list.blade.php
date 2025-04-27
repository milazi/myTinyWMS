@extends('layout.app')

@section('title', __('Active Inventories'))

@section('breadcrumb')
    <li class="active">
        <strong>@lang('Overview')</strong>
    </li>
@endsection

@section('content')

    <div class="table-toolbar-right-content hidden">
        @can('inventory.create')
        <a href="{{ route('inventory.create_month') }}" class="btn btn-secondary mr-4">@lang('Start New Monthly Inventory')</a>
        <a href="{{ route('inventory.create_year') }}" class="btn btn-secondary">@lang('Start New Yearly Inventory')</a>
        @endcan
    </div>

    {!! $dataTable->table() !!}

    <div class="mt-8">
        <h1>@lang('Completed Inventories')</h1>

        <div class="table-wrapper">
            <table class="table dataTable">
                <thead>
                <tr>
                    <th>@lang('Started')</th>
                    <th>@lang('Completed')</th>
                    <th>@lang('Articles')</th>
                    <th>@lang('Action')</th>
                </tr>
                </thead>
                <tbody>
                @foreach($closedInventories as $inventory)
                    <tr>
                        <td>{{ $inventory->created_at->format('m/d/Y H:i') }}</td>
                        <td>{{ $inventory->items->max('processed_at')->format('m/d/Y H:i') }}</td>
                        <td>{{ $inventory->items->count() }}</td>
                        <td>
                            <a href="{{ route('inventory.show', $inventory) }}" class="table-action">@lang('Details')</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush