@extends('layouts.agent')

@section('content')
<div class="container-xl">
    <div class="page-header mb-3">
        <h2 class="page-title">
            {{ isset($shipment) ? 'Edit Shipment: ' . $shipment->agent_reference : 'Create New Shipment' }}
        </h2>
    </div>

    {{-- Вывод ошибок валидации --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ isset($shipment) ? route('shipments.update', $shipment->id) : route('shipments.store') }}" method="POST" class="card">
        @csrf
        @if(isset($shipment))
            @method('PUT')
        @endif

        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                <li class="nav-item"><a href="#tabs-general" class="nav-link active" data-bs-toggle="tab">General</a></li>
                <li class="nav-item"><a href="#tabs-parties" class="nav-link" data-bs-toggle="tab">Parties (Addresses)</a></li>
                <li class="nav-item"><a href="#tabs-transport" class="nav-link" data-bs-toggle="tab">Transport</a></li>
                <li class="nav-item"><a href="#tabs-cargo" class="nav-link" data-bs-toggle="tab">Cargo & Specs</a></li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content">
                
                {{-- Вкладка 1: Основное --}}
                <div class="tab-pane active show" id="tabs-general">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label required">Agent Reference (House No)</label>
                            <input type="text" name="agent_reference" class="form-control" required
                                   value="{{ old('agent_reference', $shipment->agent_reference ?? '') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label required">Shippers Reference</label>
                            <input type="text" name="shippers_reference" class="form-control" required
                                   value="{{ old('shippers_reference', $shipment->shippers_reference ?? '') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label required">MBL (Master B/L)</label>
                            <input type="text" name="mbl" class="form-control" required
                                   value="{{ old('mbl', $shipment->mbl ?? '') }}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Pinnacle Receiving Office</label>
                            <input type="text" name="pinnacle_office" class="form-control" 
                                   value="{{ old('pinnacle_office', $shipment->pinnacle_office ?? 'PININTLCS') }}">
                        </div>
                    </div>
                </div>

                {{-- Вкладка 2: Стороны и Адреса --}}
                <div class="tab-pane" id="tabs-parties">
                    <div class="row">
                        <div class="col-md-6">
                            <fieldset class="form-fieldset">
                                <legend>Shipper Details</legend>
                                <div class="mb-3">
                                    <label class="form-label required">Shipper Name</label>
                                    <input type="text" name="shipper_name" class="form-control" required
                                           value="{{ old('shipper_name', $shipment->shipper_name ?? '') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Address 1</label>
                                    <input type="text" name="shipper_address1" class="form-control"
                                           value="{{ old('shipper_address1', $shipment->shipper_address1 ?? '') }}">
                                </div>
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label">City</label>
                                        <input type="text" name="shipper_city" class="form-control"
                                               value="{{ old('shipper_city', $shipment->shipper_city ?? '') }}">
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Country (2 chars)</label>
                                        <input type="text" name="shipper_country" class="form-control" maxlength="2" placeholder="e.g. CN"
                                               value="{{ old('shipper_country', $shipment->shipper_country ?? '') }}">
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        
                        <div class="col-md-6">
                            <fieldset class="form-fieldset">
                                <legend>Consignee Details</legend>
                                <div class="mb-3">
                                    <label class="form-label required">Consignee Name</label>
                                    <input type="text" name="consignee_name" class="form-control" required
                                           value="{{ old('consignee_name', $shipment->consignee_name ?? '') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Address 1</label>
                                    <input type="text" name="consignee_address1" class="form-control"
                                           value="{{ old('consignee_address1', $shipment->consignee_address1 ?? '') }}">
                                </div>
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label">City</label>
                                        <input type="text" name="consignee_city" class="form-control"
                                               value="{{ old('consignee_city', $shipment->consignee_city ?? '') }}">
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Country (2 chars)</label>
                                        <input type="text" name="consignee_country" class="form-control" maxlength="2" placeholder="e.g. GB"
                                               value="{{ old('consignee_country', $shipment->consignee_country ?? '') }}">
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>

                {{-- Вкладка 3: Транспорт --}}
                <div class="tab-pane" id="tabs-transport">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label required">Mode</label>
                            <select name="mode" class="form-select">
                                <option value="LCL" {{ old('mode', $shipment->mode ?? '') == 'LCL' ? 'selected' : '' }}>LCL (Sea)</option>
                                <option value="FCL" {{ old('mode', $shipment->mode ?? '') == 'FCL' ? 'selected' : '' }}>FCL (Sea)</option>
                                <option value="AIR" {{ old('mode', $shipment->mode ?? '') == 'AIR' ? 'selected' : '' }}>AIR</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Incoterms</label>
                            <input type="text" name="incoterms" class="form-control" value="{{ old('incoterms', $shipment->incoterms ?? 'FOB') }}">
                        </div>
                        
                        {{-- New Fields --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Carrier (Line)</label>
                            <input type="text" name="carrier_name" class="form-control" required placeholder="e.g. MAERSK"
                                   value="{{ old('carrier_name', $shipment->carrier_name ?? '') }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Mother Vessel</label>
                            <input type="text" name="vessel" class="form-control" required
                                   value="{{ old('vessel', $shipment->vessel ?? '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Voyage</label>
                            <input type="text" name="voyage" class="form-control" required
                                   value="{{ old('voyage', $shipment->voyage ?? '') }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Origin Port (UNLOCO)</label>
                            <input type="text" name="origin_port" class="form-control" required placeholder="e.g. CNYTN"
                                   value="{{ old('origin_port', $shipment->origin_port ?? '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Destination Port (UNLOCO)</label>
                            <input type="text" name="destination_port" class="form-control" required placeholder="e.g. GBFXT"
                                   value="{{ old('destination_port', $shipment->destination_port ?? '') }}">
                        </div>
                    </div>
                </div>

                {{-- Вкладка 4: Груз --}}
                <div class="tab-pane" id="tabs-cargo">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ETD</label>
                            <input type="date" name="etd" class="form-control" 
                                   value="{{ old('etd', isset($shipment->etd) ? $shipment->etd->format('Y-m-d') : '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ETA</label>
                            <input type="date" name="eta" class="form-control" 
                                   value="{{ old('eta', isset($shipment->eta) ? $shipment->eta->format('Y-m-d') : '') }}">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label required">Goods Description (Commodity)</label>
                            <textarea name="goods_description" class="form-control" rows="3" required>{{ old('goods_description', $shipment->goods_description ?? '') }}</textarea>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label required">Quantity</label>
                            <input type="number" name="qty_value" class="form-control" required
                                   value="{{ old('qty_value', $shipment->qty_value ?? '') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Package Type</label>
                            <input type="text" name="qty_type" class="form-control" value="{{ old('qty_type', $shipment->qty_type ?? 'CTN') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label required">Weight (KG)</label>
                            <input type="number" step="0.01" name="weight_value" class="form-control" required
                                   value="{{ old('weight_value', $shipment->weight_value ?? '') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Volume (CBM)</label>
                            <input type="number" step="0.001" name="volume_qty" class="form-control" 
                                   value="{{ old('volume_qty', $shipment->volume_qty ?? '') }}">
                        </div>

                        <div class="hr-text">Container Details</div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Container Number</label>
                            <input type="text" name="container_number" class="form-control" 
                                   value="{{ old('container_number', $shipment->container_number ?? '') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Container Type</label>
                            <select name="container_type" class="form-select">
                                <option value="">Select...</option>
                                <option value="20GP" {{ old('container_type', $shipment->container_type ?? '') == '20GP' ? 'selected' : '' }}>20GP</option>
                                <option value="40GP" {{ old('container_type', $shipment->container_type ?? '') == '40GP' ? 'selected' : '' }}>40GP</option>
                                <option value="40HC" {{ old('container_type', $shipment->container_type ?? '') == '40HC' ? 'selected' : '' }}>40HC</option>
                                <option value="LCL" {{ old('container_type', $shipment->container_type ?? '') == 'LCL' ? 'selected' : '' }}>LCL</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Seal Number</label>
                            <input type="text" name="seal_number" class="form-control" 
                                   value="{{ old('seal_number', $shipment->seal_number ?? '') }}">
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="card-footer d-flex align-items-center">
            <a href="{{ route('agent.dashboard') }}" class="btn btn-link">Cancel</a>
            <div class="ms-auto">
                <button type="submit" class="btn btn-primary">Save Shipment</button>
            </div>
        </div>
    </form>
</div>
@endsection