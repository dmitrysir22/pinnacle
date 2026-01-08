@extends('layouts.agent')

@section('content')
<div class="container-xl">
    <div class="page-header mb-3">
        <h2 class="page-title">
            {{ isset($shipment) ? 'Edit Shipment: ' . $shipment->agent_reference : 'Create New Shipment' }}
        </h2>
    </div>

    <form action="{{ isset($shipment) ? route('shipments.update', $shipment->id) : route('shipments.store') }}" method="POST" class="card">
        @csrf
        @if(isset($shipment))
            @method('PUT')
        @endif

        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                <li class="nav-item"><a href="#tabs-general" class="nav-link active" data-bs-toggle="tab">General & Parties</a></li>
                <li class="nav-item"><a href="#tabs-transport" class="nav-link" data-bs-toggle="tab">Transport Details</a></li>
                <li class="nav-item"><a href="#tabs-cargo" class="nav-link" data-bs-toggle="tab">Cargo & Specs</a></li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content">
                
                {{-- Вкладка 1: Основное и Стороны --}}
                <div class="tab-pane active show" id="tabs-general">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label required">Agent Reference</label>
                            <input type="text" name="agent_reference" class="form-control @error('agent_reference') is-invalid @enderror" 
                                   value="{{ old('agent_reference', $shipment->agent_reference ?? '') }}" placeholder="e.g. NSZX07251595">
                            @error('agent_reference') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Shippers Reference</label>
                            <input type="text" name="shippers_reference" class="form-control" value="{{ old('shippers_reference', $shipment->shippers_reference ?? '') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">MBL (Master B/L)</label>
                            <input type="text" name="mbl" class="form-control" value="{{ old('mbl', $shipment->mbl ?? '') }}">
                        </div>

                        <div class="hr-text">Parties</div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Shipper Name</label>
                            <input type="text" name="shipper_name" class="form-control @error('shipper_name') is-invalid @enderror" 
                                   value="{{ old('shipper_name', $shipment->shipper_name ?? '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Consignee Name</label>
                            <input type="text" name="consignee_name" class="form-control @error('consignee_name') is-invalid @enderror" 
                                   value="{{ old('consignee_name', $shipment->consignee_name ?? '') }}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Pinnacle Receiving Office</label>
                            <input type="text" name="pinnacle_office" class="form-control" value="{{ old('pinnacle_office', $shipment->pinnacle_office ?? 'PININTLCS') }}">
                        </div>
                    </div>
                </div>

                {{-- Вкладка 2: Транспорт --}}
                <div class="tab-pane" id="tabs-transport">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Mode</label>
                            <select name="mode" class="form-select">
                                <option value="LCL" {{ old('mode', $shipment->mode ?? '') == 'LCL' ? 'selected' : '' }}>LCL (Sea)</option>
                                <option value="FCL" {{ old('mode', $shipment->mode ?? '') == 'FCL' ? 'selected' : '' }}>FCL (Sea)</option>
                                <option value="AIR" {{ old('mode', $shipment->mode ?? '') == 'AIR' ? 'selected' : '' }}>AIR</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Incoterms</label>
                            <input type="text" name="incoterms" class="form-control" value="{{ old('incoterms', $shipment->incoterms ?? 'FOB') }}" placeholder="e.g. FOB">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Container Number</label>
                            <input type="text" name="container_number" class="form-control" value="{{ old('container_number', $shipment->container_number ?? '') }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mother Vessel</label>
                            <input type="text" name="vessel" class="form-control" value="{{ old('vessel', $shipment->vessel ?? '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Voyage</label>
                            <input type="text" name="voyage" class="form-control" value="{{ old('voyage', $shipment->voyage ?? '') }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Origin Port (POL)</label>
                            <input type="text" name="origin_port" class="form-control @error('origin_port') is-invalid @enderror" 
                                   value="{{ old('origin_port', $shipment->origin_port ?? '') }}" placeholder="e.g. CNYTN">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Destination Port (POD)</label>
                            <input type="text" name="destination_port" class="form-control @error('destination_port') is-invalid @enderror" 
                                   value="{{ old('destination_port', $shipment->destination_port ?? '') }}" placeholder="e.g. GBFXT">
                        </div>
                    </div>
                </div>

                {{-- Вкладка 3: Груз и Даты --}}
                <div class="tab-pane" id="tabs-cargo">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ETD (Estimated Departure)</label>
                            <input type="date" name="etd" class="form-control" value="{{ old('etd', isset($shipment->etd) ? $shipment->etd->format('Y-m-d') : '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ETA (Estimated Arrival)</label>
                            <input type="date" name="eta" class="form-control" value="{{ old('eta', isset($shipment->eta) ? $shipment->eta->format('Y-m-d') : '') }}">
                        </div>

                        <div class="hr-text">Cargo Specifications</div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label required">Quantity</label>
                            <input type="number" name="qty_value" class="form-control @error('qty_value') is-invalid @enderror" 
                                   value="{{ old('qty_value', $shipment->qty_value ?? '') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Package Type</label>
                            <input type="text" name="qty_type" class="form-control" value="{{ old('qty_type', $shipment->qty_type ?? 'CTN') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label required">Weight (KG)</label>
                            <input type="number" step="0.01" name="weight_value" class="form-control @error('weight_value') is-invalid @enderror" 
                                   value="{{ old('weight_value', $shipment->weight_value ?? '') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Volume (CBM)</label>
                            <input type="number" step="0.001" name="volume_qty" class="form-control" value="{{ old('volume_qty', $shipment->volume_qty ?? '') }}">
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="card-footer d-flex align-items-center">
            <a href="{{ route('agent.dashboard') }}" class="btn btn-link">Cancel</a>
            <div class="ms-auto">
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"></path><path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path><path d="M14 4l0 4l-4 0l0 -4"></path></svg>
                    {{ isset($shipment) ? 'Update Shipment' : 'Create Shipment' }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection