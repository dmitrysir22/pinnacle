@extends('layouts.agent')

@section('content')
<div class="container-xl">
    <div class="page-header mb-3">
        <h2 class="page-title">
            {{ isset($shipment) ? 'Edit Shipment: ' . $shipment->agent_reference : 'Create New Shipment' }}
        </h2>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="shipment-form" 
          action="{{ isset($shipment) ? route('shipments.update', $shipment->id) : route('shipments.store') }}" 
          method="POST" 
          class="card needs-validation" 
          novalidate>
          
        @csrf
        @if(isset($shipment))
            @method('PUT')
        @endif

        {{-- Hidden EDI Defaults --}}
        <input type="hidden" name="sender_id" value="HECNY">
        <input type="hidden" name="action_code" value="ADD">

        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="shipment-tabs" data-bs-toggle="tabs">
                <li class="nav-item"><a href="#tab-parties" class="nav-link active" data-bs-toggle="tab">Parties</a></li>
                <li class="nav-item"><a href="#tab-bl" class="nav-link" data-bs-toggle="tab">Shipment / B/L</a></li>
                <li class="nav-item"><a href="#tab-cargo" class="nav-link" data-bs-toggle="tab">Cargo (Items)</a></li>
                <li class="nav-item"><a href="#tab-containers" class="nav-link" data-bs-toggle="tab">Containers</a></li>
                <li class="nav-item"><a href="#tab-notes" class="nav-link" data-bs-toggle="tab">Notes</a></li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content">
                
                {{-- TAB: PARTIES --}}
                <div class="tab-pane active show" id="tab-parties">
                    <div class="row">
                        {{-- Shipper Section --}}
                        <div class="col-md-6 mb-3">
                            <fieldset class="form-fieldset">
                                <legend>Shipper Details</legend>
                                
                                {{-- Selection Logic --}}
                                <div class="mb-3">
                                    <label class="form-label">Select Existing or Create New</label>
                                    <div class="input-group">
                                        <select class="form-select" id="shipperSelect">
                                            <option value="">-- Select Shipper --</option>
                                            @if(isset($shippers))
                                                @foreach($shippers as $s)
                                                    <option value="{{ $s->name }}" 
                                                        data-addr1="{{ $s->address1 }}" 
                                                        data-addr2="{{ $s->address2 ?? '' }}"
                                                        data-city="{{ $s->city }}" 
                                                        data-state="{{ $s->state ?? '' }}"
                                                        data-postcode="{{ $s->postcode ?? '' }}"
                                                        data-country="{{ $s->country }}"
                                                        {{ (old('shipper_name', $shipment->shipper_name ?? '') == $s->name) ? 'selected' : '' }}>
                                                        {{ $s->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <button type="button" class="btn btn-outline-primary" id="btnNewShipper">
                                            Create New Shipper
                                        </button>
                                    </div>
                                    <small class="form-hint">Clicking "Create New" will clear fields below for manual entry.</small>
                                </div>

                                {{-- Shipper Fields --}}
                                <div class="mb-2">
                                    <label class="form-label required">Company Name</label>
                                    <input type="text" name="shipper_name" id="shp_name" class="form-control" required value="{{ old('shipper_name', $shipment->shipper_name ?? '') }}">
                                    <div class="invalid-feedback">Company Name is required</div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label required">Address 1</label>
                                    <input type="text" name="shipper_address1" id="shp_addr1" class="form-control" required value="{{ old('shipper_address1', $shipment->shipper_address1 ?? '') }}">
                                    <div class="invalid-feedback">Address 1 is required</div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Address 2</label>
                                    <input type="text" name="shipper_address2" id="shp_addr2" class="form-control" value="{{ old('shipper_address2', $shipment->shipper_address2 ?? '') }}">
                                </div>
                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <label class="form-label required">City</label>
                                        <input type="text" name="shipper_city" id="shp_city" class="form-control" required value="{{ old('shipper_city', $shipment->shipper_city ?? '') }}">
                                        <div class="invalid-feedback">City is required</div>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <label class="form-label">State</label>
                                        <input type="text" name="shipper_state" id="shp_state" class="form-control" value="{{ old('shipper_state', $shipment->shipper_state ?? '') }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <label class="form-label">Postcode</label>
                                        <input type="text" name="shipper_postcode" id="shp_zip" class="form-control" value="{{ old('shipper_postcode', $shipment->shipper_postcode ?? '') }}">
                                    </div>
                                    <div class="col-6 mb-2">
                                        <label class="form-label required">Country Code</label>
                                        <input type="text" name="shipper_country" id="shp_country" class="form-control" placeholder="GB" maxlength="2" required value="{{ old('shipper_country', $shipment->shipper_country ?? '') }}">
                                        <div class="invalid-feedback">2-letter Country Code required</div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>

                        {{-- Consignee Section --}}
                        <div class="col-md-6 mb-3">
                            <fieldset class="form-fieldset">
                                <legend>Consignee Details</legend>
                                <div class="mb-2">
                                    <label class="form-label required">Company Name</label>
                                    <input type="text" name="consignee_name" class="form-control" required value="{{ old('consignee_name', $shipment->consignee_name ?? '') }}">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label required">Address 1</label>
                                    <input type="text" name="consignee_address1" class="form-control" required value="{{ old('consignee_address1', $shipment->consignee_address1 ?? '') }}">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Address 2</label>
                                    <input type="text" name="consignee_address2" class="form-control" value="{{ old('consignee_address2', $shipment->consignee_address2 ?? '') }}">
                                </div>
                                <div class="row">
                                    <div class="col-6 mb-2"><label class="form-label">City</label><input type="text" name="consignee_city" class="form-control" value="{{ old('consignee_city', $shipment->consignee_city ?? '') }}"></div>
                                    <div class="col-6 mb-2"><label class="form-label">State</label><input type="text" name="consignee_state" class="form-control" value="{{ old('consignee_state', $shipment->consignee_state ?? '') }}"></div>
                                </div>
                                <div class="row">
                                    <div class="col-6 mb-2"><label class="form-label">Postcode</label><input type="text" name="consignee_postcode" class="form-control" value="{{ old('consignee_postcode', $shipment->consignee_postcode ?? '') }}"></div>
                                    <div class="col-6 mb-2"><label class="form-label required">Country</label><input type="text" name="consignee_country" class="form-control" required maxlength="2" value="{{ old('consignee_country', $shipment->consignee_country ?? '') }}"></div>
                                </div>
                            </fieldset>
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label">Pinnacle Office Code</label>
                            <input type="text" name="pinnacle_office" class="form-control" value="{{ old('pinnacle_office', $shipment->pinnacle_office ?? 'PININTLCS') }}">
                        </div>
                    </div>
                </div>

                {{-- TAB: BL DETAILS --}}
                <div class="tab-pane" id="tab-bl">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label required">Mode</label>
                            <select name="mode" class="form-select" required>
                                <option value="FCL" {{ old('mode', $shipment->mode ?? '') == 'FCL' ? 'selected' : '' }}>FCL</option>
                                <option value="LCL" {{ old('mode', $shipment->mode ?? '') == 'LCL' ? 'selected' : '' }}>LCL</option>
                                <option value="AIR" {{ old('mode', $shipment->mode ?? '') == 'AIR' ? 'selected' : '' }}>AIR</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Incoterms</label>
                            <input type="text" name="incoterms" class="form-control" value="{{ old('incoterms', $shipment->incoterms ?? 'DDU') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label required">Shippers Ref</label>
                            <input type="text" name="shippers_reference" class="form-control" required value="{{ old('shippers_reference', $shipment->shippers_reference ?? '') }}">
                            <div class="invalid-feedback">Required</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label required">Agent Ref</label>
                            <input type="text" name="agent_reference" class="form-control" required value="{{ old('agent_reference', $shipment->agent_reference ?? '') }}">
                            <div class="invalid-feedback">Required</div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Goods Description</label>
                            <textarea name="goods_description" class="form-control" rows="2">{{ old('goods_description', $shipment->goods_description ?? '') }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Marks & Numbers</label>
                            <textarea name="marks_and_numbers" class="form-control" rows="2">{{ old('marks_and_numbers', $shipment->marks_and_numbers ?? 'N/M') }}</textarea>
                        </div>
                    </div>

                    <div class="hr-text">Routing Details</div>
                    <div class="row">
                        <div class="col-md-3 mb-3"><label class="form-label required">Origin (UNLOCO)</label><input type="text" name="origin_port" class="form-control" required maxlength="5" value="{{ old('origin_port', $shipment->origin_port ?? '') }}"></div>
                        <div class="col-md-3 mb-3"><label class="form-label required">Dest (UNLOCO)</label><input type="text" name="destination_port" class="form-control" required maxlength="5" value="{{ old('destination_port', $shipment->destination_port ?? '') }}"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">ETD</label><input type="date" name="etd" class="form-control" value="{{ old('etd', isset($shipment->etd) ? $shipment->etd->format('Y-m-d') : '') }}"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">ETA</label><input type="date" name="eta" class="form-control" value="{{ old('eta', isset($shipment->eta) ? $shipment->eta->format('Y-m-d') : '') }}"></div>
                        
                        <div class="col-md-3 mb-3"><label class="form-label required">MBL</label><input type="text" name="mbl" class="form-control" required value="{{ old('mbl', $shipment->mbl ?? '') }}"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Carrier</label><input type="text" name="mother_carrier" class="form-control" value="{{ old('mother_carrier', $shipment->mother_carrier ?? '') }}"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Vessel</label><input type="text" name="mother_vessel" class="form-control" value="{{ old('mother_vessel', $shipment->mother_vessel ?? '') }}"></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Voyage</label><input type="text" name="mother_voyage" class="form-control" value="{{ old('mother_voyage', $shipment->mother_voyage ?? '') }}"></div>
                    </div>
                </div>

                {{-- TAB: CARGO --}}
                <div class="tab-pane" id="tab-cargo">
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table" id="cargo-table">
                            <thead>
                                <tr>
                                    <th>Qty</th>
                                    <th>Type</th>
                                    <th>Weight (KG)</th>
                                    <th>Vol (M3)</th>
                                    <th>Container Link</th>
                                    <th><button type="button" class="btn btn-sm btn-success" onclick="addCargoRow()">+ Add</button></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $items = old('packing_items', $shipment->packing_items ?? []);
                                    if(empty($items)) $items = [['qty_value' => '', 'qty_type' => 'CTN', 'weight_value' => '', 'volume_qty' => '', 'container_number' => '']];
                                @endphp
                                @foreach($items as $index => $item)
                                <tr class="cargo-row">
                                    <td><input type="number" name="packing_items[{{$index}}][qty_value]" class="form-control" value="{{ $item['qty_value'] ?? '' }}" required><div class="invalid-feedback">Required</div></td>
                                    <td><input type="text" name="packing_items[{{$index}}][qty_type]" class="form-control" value="{{ $item['qty_type'] ?? 'CTN' }}"></td>
                                    <td><input type="number" step="0.01" name="packing_items[{{$index}}][weight_value]" class="form-control" value="{{ $item['weight_value'] ?? '' }}" required></td>
                                    <td><input type="number" step="0.001" name="packing_items[{{$index}}][volume_qty]" class="form-control" value="{{ $item['volume_qty'] ?? '' }}"></td>
                                    <td><input type="text" name="packing_items[{{$index}}][container_number]" class="form-control" placeholder="Container #" value="{{ $item['container_number'] ?? '' }}"></td>
                                    <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">X</button></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB: CONTAINERS --}}
                <div class="tab-pane" id="tab-containers">
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table" id="container-table">
                            <thead>
                                <tr>
                                    <th>Container Number</th>
                                    <th>Type</th>
                                    <th>Seal Number</th>
                                    <th><button type="button" class="btn btn-sm btn-success" onclick="addContainerRow()">+ Add</button></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $containers = old('containers', $shipment->containers ?? []);
                                @endphp
                                @foreach($containers as $index => $cnt)
                                <tr class="container-row">
                                    <td><input type="text" name="containers[{{$index}}][container_number]" class="form-control" value="{{ $cnt['container_number'] ?? '' }}"></td>
                                    <td><input type="text" name="containers[{{$index}}][container_type]" class="form-control" value="{{ $cnt['container_type'] ?? '' }}"></td>
                                    <td><input type="text" name="containers[{{$index}}][seal_number]" class="form-control" value="{{ $cnt['seal_number'] ?? '' }}"></td>
                                    <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">X</button></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                 {{-- TAB: NOTES --}}
                 <div class="tab-pane" id="tab-notes">
                    <div class="table-responsive">
                       <table class="table table-vcenter" id="notes-table">
                           <thead><tr><th>Note Text</th><th><button type="button" class="btn btn-sm btn-success" onclick="addNoteRow()">+</button></th></tr></thead>
                           <tbody>
                               @php
                                   $notes = old('notes', $shipment->notes ?? []);
                               @endphp
                               @foreach($notes as $index => $note)
                               <tr>
                                   <td><input type="text" name="notes[{{$index}}][note_text]" class="form-control" value="{{ $note['note_text'] ?? '' }}"></td>
                                   <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">X</button></td>
                               </tr>
                               @endforeach
                           </tbody>
                       </table>
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

{{-- Templates --}}
<template id="cargo-row-template">
    <tr class="cargo-row">
        <td><input type="number" name="packing_items[INDEX][qty_value]" class="form-control" required></td>
        <td><input type="text" name="packing_items[INDEX][qty_type]" class="form-control" value="CTN"></td>
        <td><input type="number" step="0.01" name="packing_items[INDEX][weight_value]" class="form-control" required></td>
        <td><input type="number" step="0.001" name="packing_items[INDEX][volume_qty]" class="form-control"></td>
        <td><input type="text" name="packing_items[INDEX][container_number]" class="form-control"></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">X</button></td>
    </tr>
</template>
<template id="container-row-template">
    <tr class="container-row">
        <td><input type="text" name="containers[INDEX][container_number]" class="form-control text-uppercase"></td>
        <td><input type="text" name="containers[INDEX][container_type]" class="form-control text-uppercase"></td>
        <td><input type="text" name="containers[INDEX][seal_number]" class="form-control"></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">X</button></td>
    </tr>
</template>
<template id="note-row-template">
    <tr><td><input type="text" name="notes[INDEX][note_text]" class="form-control"></td><td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">X</button></td></tr>
</template>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Dynamic Rows Logic
        window.addCargoRow = function() { addRow('cargo-table', 'cargo-row-template', 'packing_items'); };
        window.addContainerRow = function() { addRow('container-table', 'container-row-template', 'containers'); };
        window.addNoteRow = function() { addRow('notes-table', 'note-row-template', 'notes'); };
        window.removeRow = function(btn) { btn.closest('tr').remove(); };

        function addRow(tableId, templateId, prefix) {
            const table = document.getElementById(tableId).getElementsByTagName('tbody')[0];
            const template = document.getElementById(templateId).content.cloneNode(true);
            const index = Math.floor(Math.random() * 10000); // Random index to avoid collision
            
            const inputs = template.querySelectorAll('input, select');
            inputs.forEach(input => {
                input.name = input.name.replace('INDEX', index);
            });
            table.appendChild(template);
        }

        // 2. Shipper Selection & Clear Logic
        const shipperSelect = document.getElementById('shipperSelect');
        const btnNewShipper = document.getElementById('btnNewShipper');

        // Mapping fields
        const fields = {
            'name': 'shp_name', 'addr1': 'shp_addr1', 'addr2': 'shp_addr2',
            'city': 'shp_city', 'state': 'shp_state', 'zip': 'shp_zip', 'country': 'shp_country'
        };

        if(shipperSelect) {
            shipperSelect.addEventListener('change', function() {
                const opt = this.options[this.selectedIndex];
                if (!opt.value) return;

                document.getElementById(fields.name).value = opt.value;
                document.getElementById(fields.addr1).value = opt.dataset.addr1 || '';
                document.getElementById(fields.addr2).value = opt.dataset.addr2 || '';
                document.getElementById(fields.city).value = opt.dataset.city || '';
                document.getElementById(fields.state).value = opt.dataset.state || '';
                document.getElementById(fields.zip).value = opt.dataset.postcode || '';
                document.getElementById(fields.country).value = opt.dataset.country || '';
            });
        }

        if(btnNewShipper) {
            btnNewShipper.addEventListener('click', function() {
                // Reset Select
                shipperSelect.value = "";
                // Clear fields
                Object.values(fields).forEach(id => {
                    const el = document.getElementById(id);
                    if(el) el.value = '';
                });
                // Focus on Name
                document.getElementById(fields.name).focus();
            });
        }

        // 3. Validation - Auto Tab Switch
        const form = document.getElementById('shipment-form');
        const submitBtn = form.querySelector('button[type="submit"]');

// Находим форму

form.addEventListener('submit', function(event) {
    if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
        
        form.classList.add('was-validated');

        const firstInvalid = form.querySelector(':invalid');
        if (firstInvalid) {
            const tabPane = firstInvalid.closest('.tab-pane');
            if (tabPane) {
                const tabId = tabPane.getAttribute('id');
                const tabLink = document.querySelector(`a[href="#${tabId}"]`);
                
                if (tabLink) {
                    // Проверяем, определен ли bootstrap
                    if (window.bootstrap && window.bootstrap.Tab) {
                        const tab = new window.bootstrap.Tab(tabLink);
                        tab.show();
                    } else {
                        // Если bootstrap не найден, просто имитируем клик по вкладке
                        tabLink.click(); 
                    }
                }
            }
            setTimeout(() => { firstInvalid.focus(); }, 200);
        }
    }
}, false);

    });
</script>
@endsection