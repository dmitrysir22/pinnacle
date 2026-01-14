@extends('layouts.agent')

@section('content')
<div class="container-xl">
    <div class="page-header mb-3">
        <h2 class="page-title">
            {{ isset($shipment) ? 'Edit Shipment: ' . $shipment->agent_reference : 'Create New Shipment' }}
        </h2>
    </div>

    {{-- Вывод ошибок сервера --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Основная форма создания груза --}}
    <form id="shipment-form" 
          action="{{ isset($shipment) ? route('shipments.update', $shipment->id) : route('shipments.store') }}" 
          method="POST" 
          class="card needs-validation" 
          novalidate>
          
        @csrf
        @if(isset($shipment))
            @method('PUT')
        @endif

        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="shipment-tabs" data-bs-toggle="tabs">
                <li class="nav-item">
                    <a href="#tabs-general" class="nav-link active" data-bs-toggle="tab">General</a>
                </li>
                <li class="nav-item">
                    <a href="#tabs-parties" class="nav-link" data-bs-toggle="tab">Parties (Addresses)</a>
                </li>
                <li class="nav-item">
                    <a href="#tabs-transport" class="nav-link" data-bs-toggle="tab">Transport</a>
                </li>
                <li class="nav-item">
                    <a href="#tabs-cargo" class="nav-link" data-bs-toggle="tab">Cargo & Specs</a>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content">
                
                {{-- Вкладка 1: Основное --}}
                <div class="tab-pane active show" id="tabs-general">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label required">Agent Reference (House No)</label>
                            <input type="text" name="agent_reference" class="form-control text-uppercase" required
                                   value="{{ old('agent_reference', $shipment->agent_reference ?? '') }}">
                            <div class="invalid-feedback">Agent Reference is required.</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label required">Shippers Reference</label>
                            <input type="text" name="shippers_reference" class="form-control" required
                                   value="{{ old('shippers_reference', $shipment->shippers_reference ?? '') }}">
                            <div class="invalid-feedback">Shippers Reference is required.</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label required">MBL (Master B/L)</label>
                            <input type="text" name="mbl" class="form-control text-uppercase" required
                                   value="{{ old('mbl', $shipment->mbl ?? '') }}">
                            <div class="invalid-feedback">Master B/L is required.</div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Pinnacle Receiving Office</label>
                            <input type="text" name="pinnacle_office" class="form-control text-uppercase" 
                                   value="{{ old('pinnacle_office', $shipment->pinnacle_office ?? 'PININTLCS') }}">
                        </div>
                    </div>
                </div>

                {{-- Вкладка 2: Стороны (Здесь основные изменения) --}}
                <div class="tab-pane" id="tabs-parties">
                    <div class="row">
                        <div class="col-md-6">
                            <fieldset class="form-fieldset">
                                <legend>Shipper Details</legend>
                                
                                <div class="mb-3">
                                    <label class="form-label required">Select Shipper</label>
                                    <div class="input-group">
                                        <select class="form-select" id="shipperSelect" >
                                            <option value="">-- Choose Existing Shipper --</option>
                                            @if(isset($shippers))
                                                @foreach($shippers as $shipper)
                                                    <option value="{{ $shipper->name }}"
                                                            data-address1="{{ $shipper->address1 }}"
                                                            data-city="{{ $shipper->city }}"
                                                            data-country="{{ $shipper->country }}"
                                                            {{ (old('shipper_name', $shipment->shipper_name ?? '') == $shipper->name) ? 'selected' : '' }}>
                                                        {{ $shipper->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newShipperModal">
                                            + Request New
                                        </button>
                                    </div>
                                    <small class="text-muted">Can't find the shipper? Request a new one.</small>
                                </div>

                                {{-- Скрытые/Readonly поля, которые уходят в БД --}}
                                <div class="mb-3">
                                    <label class="form-label required">Shipper Name (Auto-filled)</label>
                                    <input type="text" name="shipper_name" id="inputShipperName" class="form-control bg-light" readonly required
                                           value="{{ old('shipper_name', $shipment->shipper_name ?? '') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Address 1</label>
                                    <input type="text" name="shipper_address1" id="inputShipperAddress1" class="form-control bg-light" readonly
                                           value="{{ old('shipper_address1', $shipment->shipper_address1 ?? '') }}">
                                </div>
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label">City</label>
                                        <input type="text" name="shipper_city" id="inputShipperCity" class="form-control bg-light" readonly
                                               value="{{ old('shipper_city', $shipment->shipper_city ?? '') }}">
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Country</label>
                                        <input type="text" name="shipper_country" id="inputShipperCountry" class="form-control text-uppercase bg-light" readonly maxlength="2"
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
                                        <input type="text" name="consignee_country" class="form-control text-uppercase" maxlength="2" placeholder="GB"
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
                            <select name="mode" class="form-select" required>
                                <option value="LCL" {{ old('mode', $shipment->mode ?? '') == 'LCL' ? 'selected' : '' }}>LCL (Sea)</option>
                                <option value="FCL" {{ old('mode', $shipment->mode ?? '') == 'FCL' ? 'selected' : '' }}>FCL (Sea)</option>
                                <option value="AIR" {{ old('mode', $shipment->mode ?? '') == 'AIR' ? 'selected' : '' }}>AIR</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Incoterms</label>
                            <input type="text" name="incoterms" class="form-control text-uppercase" value="{{ old('incoterms', $shipment->incoterms ?? 'FOB') }}">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Carrier (Line)</label>
                            <input type="text" name="carrier_name" class="form-control text-uppercase" required placeholder="MAERSK"
                                   value="{{ old('carrier_name', $shipment->carrier_name ?? '') }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Mother Vessel</label>
                            <input type="text" name="vessel" class="form-control text-uppercase" required
                                   value="{{ old('vessel', $shipment->vessel ?? '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Voyage</label>
                            <input type="text" name="voyage" class="form-control text-uppercase" required
                                   value="{{ old('voyage', $shipment->voyage ?? '') }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Origin Port (UNLOCO 5 chars)</label>
                            <input type="text" name="origin_port" class="form-control text-uppercase" required maxlength="5" placeholder="CNYTN"
                                   value="{{ old('origin_port', $shipment->origin_port ?? '') }}">
                            <div class="invalid-feedback">Must be exactly a 5-letter UNLOCO code (e.g. CNYTN).</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Destination Port (UNLOCO 5 chars)</label>
                            <input type="text" name="destination_port" class="form-control text-uppercase" required maxlength="5" placeholder="GBFXT"
                                   value="{{ old('destination_port', $shipment->destination_port ?? '') }}">
                            <div class="invalid-feedback">Must be exactly a 5-letter UNLOCO code (e.g. GBFXT).</div>
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
                            <label class="form-label required">Goods Description</label>
                            <textarea name="goods_description" class="form-control" rows="3" required>{{ old('goods_description', $shipment->goods_description ?? '') }}</textarea>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label required">Quantity</label>
                            <input type="number" name="qty_value" class="form-control" required min="1"
                                   value="{{ old('qty_value', $shipment->qty_value ?? '') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Type</label>
                            <input type="text" name="qty_type" class="form-control" value="{{ old('qty_type', $shipment->qty_type ?? 'CTN') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label required">Weight (KG)</label>
                            <input type="number" step="0.01" name="weight_value" class="form-control" required min="0.1"
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
                            <input type="text" name="container_number" class="form-control text-uppercase" 
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

<div class="modal modal-blur fade" id="newShipperModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Request New Shipper</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="shipperRequestForm">
          @csrf
          <div class="modal-body">
            <p class="text-muted small">Please provide details. The admin will verify and add this shipper to CargoWise.</p>
            <div class="mb-3">
                <label class="form-label required">Company Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Address</label>
                <input type="text" name="address1" class="form-control">
            </div>
            <div class="row">
                <div class="col-6 mb-3">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control">
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label">Country Code</label>
                    <input type="text" name="country" class="form-control text-uppercase" maxlength="2" placeholder="CN">
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary ms-auto" id="btnSendRequest">
                Send Request
            </button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('shipment-form');
    
    // ---------------------------------------------------------
    // 1. Логика выбора отправителя (Shipper Auto-fill)
    // ---------------------------------------------------------
    const shipperSelect = document.getElementById('shipperSelect');
    const inputName = document.getElementById('inputShipperName');
    const inputAddr = document.getElementById('inputShipperAddress1');
    const inputCity = document.getElementById('inputShipperCity');
    const inputCountry = document.getElementById('inputShipperCountry');

    if(shipperSelect) {
        shipperSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            // Если выбрали "Select...", очищаем поля
            if (!this.value) {
                inputName.value = '';
                inputAddr.value = '';
                inputCity.value = '';
                inputCountry.value = '';
                return;
            }

            // Заполняем поля данными из data-attributes
            inputName.value = selectedOption.value; // Name
            inputAddr.value = selectedOption.dataset.address1 || '';
            inputCity.value = selectedOption.dataset.city || '';
            inputCountry.value = selectedOption.dataset.country || '';
        });
    }

    // ---------------------------------------------------------
    // 2. Логика отправки запроса на нового отправителя (AJAX)
    // ---------------------------------------------------------
    const requestForm = document.getElementById('shipperRequestForm');
    const btnSendRequest = document.getElementById('btnSendRequest');

    requestForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Блокируем кнопку
        btnSendRequest.disabled = true;
        btnSendRequest.innerHTML = 'Sending...';

        const formData = new FormData(requestForm);

        // Предполагаемый роут - замените на реальный URI вашего контроллера заявок
        // Например: /portal/shipper-request/store
        // Если роута нет в JS, используйте жесткий URL или route() через Blade выше
        fetch('/shipper-request/store', { 
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            alert('Request sent successfully! Once approved, the shipper will appear in the list.');
            // Закрываем модалку
            const modalCloseBtn = document.querySelector('#newShipperModal .btn-close');
            if (modalCloseBtn) modalCloseBtn.click();
            requestForm.reset();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to send request. Please try again.');
        })
        .finally(() => {
            btnSendRequest.disabled = false;
            btnSendRequest.innerHTML = 'Send Request';
        });
    });


    // ---------------------------------------------------------
    // 3. Стандартная валидация (Tabs Switching)
    // ---------------------------------------------------------
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            
            form.classList.add('was-validated');

            // Находим первое невалидное поле и переключаем вкладку
            const firstInvalid = form.querySelector(':invalid');
            
            if (firstInvalid) {
                const tabPane = firstInvalid.closest('.tab-pane');
                if (tabPane) {
                    const tabId = tabPane.getAttribute('id');
                    const tabLink = document.querySelector(`a[href="#${tabId}"]`);
                    
                    if (tabLink) {
                        if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                            const tab = new bootstrap.Tab(tabLink);
                            tab.show();
                        } else {
                            tabLink.click();
                        }
                    }
                }
                // Фокус
                setTimeout(() => {
                    firstInvalid.focus();
                }, 100);
            }
        }
    }, false);
});
</script>
@endsection