@extends('layouts.agent')

@section('content')
<div class="container-fluid">


    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Agent Dashboard</h1>
        {{-- 1. Ссылка на создание --}}
        <a href="{{ route('shipments.create') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
            Create Shipment
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Historic Shipments</h3>
        </div>
        <div class="table-responsive">
            <table class="table card-table table-vcenter text-nowrap">
                <thead>
                    <tr>
                        <th>Ref Number</th>
                        <th>Vessel / Voyage</th>
                        <th>Route (Port)</th>
                        <th>ETD</th>
                        <th>Weight/Vol</th>
                        <th>Status</th>
                        {{-- Пустой заголовок для кнопок --}}
                        <th class="w-1">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shipments as $s)
                    <tr>
                        <td>
                            <strong>{{ $s->agent_reference }}</strong><br>
                            <small class="text-muted">MBL: {{ $s->mbl ?? 'N/A' }}</small>
                        </td>
                        <td>{{ $s->vessel }} <span class="text-muted">({{ $s->voyage }})</span></td>
                        <td>{{ $s->origin_port }} &rarr; {{ $s->destination_port }}</td>
                        {{-- Используем format только если дата существует --}}
                        <td>{{ $s->etd ? $s->etd->format('d/m/Y') : 'TBA' }}</td>
                        <td>{{ $s->weight_value }}kg / {{ $s->volume_qty }}m3</td>
                        <td>
                            {{-- Разные цвета для статусов --}}
                            <span class="badge bg-{{ $s->status == 'pending' ? 'warning' : 'success' }} text-white">
                                {{ strtoupper($s->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-list flex-nowrap">
                                {{-- 2. Ссылка на редактирование --}}
                                <a href="{{ route('shipments.edit', $s->id) }}" class="btn btn-white btn-sm">
                                    Edit
                                </a>

                                {{-- 3. Кнопка удаления (обязательно через форму) --}}
                                <form action="{{ route('shipments.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this shipment?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            No shipments found. <a href="{{ route('shipments.create') }}">Create one now</a>.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection