@php
    $currentStatus = request('status');
    // Массив для кнопок: статус => [название, цвет bootstrap]
    $buttons = [
        'pending'    => ['Pending', 'warning'],
        'processing' => ['Processing', 'info'],
        'completed'  => ['Completed', 'success'],
        'cancelled'  => ['Cancelled', 'danger'],
    ];
@endphp

<div class="mb-3 d-flex flex-wrap gap-2">
    {{-- Кнопка ALL --}}
    <a href="{{ backpack_url('shipment') }}" 
       class="btn btn-sm {{ !$currentStatus ? 'btn-primary' : 'btn-outline-primary' }}">
        All ({{ $widget['all_count'] }})
    </a>

    {{-- Динамические кнопки --}}
    @foreach($buttons as $key => $data)
        @php $count = $widget['counts'][$key] ?? 0; @endphp
        <a href="{{ backpack_url('shipment') }}?status={{ $key }}" 
           class="btn btn-sm {{ $currentStatus == $key ? 'btn-'.$data[1] : 'btn-outline-'.$data[1] }}">
            {{ $data[0] }} ({{ $count }})
        </a>
    @endforeach
</div>