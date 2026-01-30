<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\ShipperRequest; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShipmentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Shipment::where('organization_id', $user->organization_id);

        if ($user->access_level === 'individual') {
            $query->where('user_id', $user->id);
        }

        $shipments = $query->latest()->get();
        return view('agent.dashboard', compact('shipments'));
    }

    public function create()
    {
        $user = auth()->user();
        $organization = $user->organization;

        if (!$organization) {
            abort(403, 'User has no agent assigned');
        }

        $shippers = $organization->shippers()->orderBy('name')->get();

        return view('agent.shipments.create', compact('shippers'));
    }



    public function store(Request $request)
    {
        $this->validateShipment($request);

        $shipment = new Shipment($request->all());
        $shipment->organization_id = Auth::user()->organization_id; // Исправлено agent_id -> organization_id
        $shipment->user_id = Auth::id();
        $shipment->status = 'pending';
        
        // Для совместимости плоских полей, если массив заполнен
        $this->syncLegacyFields($shipment, $request);

        $shipment->save();
        $this->handleShipperRequest($shipment);
        return redirect()->route('agent.dashboard')
            ->with('success', 'Shipment ' . $shipment->agent_reference . ' created successfully!');
    }



    public function edit(Shipment $shipment)
    {
        // Проверка прав доступа через organization_id (так надежнее)
        if ($shipment->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        $user = auth()->user();
        $organization = $user->organization;
        $shippers = $organization->shippers()->orderBy('name')->get();

        return view('agent.shipments.create', compact('shipment','shippers'));
    }

    public function update(Request $request, Shipment $shipment)
    {
        if ($shipment->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        $this->validateShipment($request, $shipment->id);

        $shipment->fill($request->all());
        $this->syncLegacyFields($shipment, $request);
        $shipment->save();

        $this->handleShipperRequest($shipment);
        return redirect()->route('agent.dashboard')
            ->with('success', 'Shipment updated successfully!');
    }

/**
     * Проверяет, существует ли шиппер в базе. Если нет - создает заявку.
     */
    private function handleShipperRequest(Shipment $shipment)
    {
        $user = Auth::user();
        $org = $user->organization;

        // 1. Ищем точное совпадение по имени среди утвержденных шипперов организации
        // (Мы предполагаем, что если имя совпало, то это тот же шиппер. 
        // Если клиент хочет строже - можно сверять и Address1)
        $exists = $org->shippers()
                      ->where('name', $shipment->shipper_name)
                      ->exists();

        // 2. Если шиппера нет в списке утвержденных
        if (!$exists) {
            
            // 3. Проверяем, не создавали ли мы уже запрос на этого шиппера (Pending),
            // чтобы не спамить админа дублями, если агент создал 5 грузов подряд с новым шиппером.
            $alreadyRequested = ShipperRequest::where('user_id', $user->id)
                ->where('name', $shipment->shipper_name)
                ->where('status', 'pending')
                ->first();

            if (!$alreadyRequested) {
                ShipperRequest::create([
                    'user_id'         => $user->id,
                    'shipment_id'     => $shipment->id, // Привязываем к текущему грузу для контекста
                    'name'    => $shipment->shipper_name,
                    'address1'        => $shipment->shipper_address1,
                    'address2'        => $shipment->shipper_address2,
                    'city'            => $shipment->shipper_city,
                    'state'           => $shipment->shipper_state,
                    'postcode'        => $shipment->shipper_postcode,
                    'country_code'    => $shipment->shipper_country,
                    'status'          => 'pending'
                ]);
            }
        }
    }

    public function destroy(Shipment $shipment)
    {
        if ($shipment->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }
        $shipment->delete();
        return redirect()->route('agent.dashboard')
            ->with('success', 'Shipment deleted successfully!');
    }

    // Вспомогательный метод для сохранения первой строки из JSON в обычные поля
    private function syncLegacyFields(Shipment $shipment, Request $request)
    {
        if ($request->has('packing_items') && count($request->packing_items) > 0) {
            $shipment->qty_value = $request->packing_items[0]['qty_value'] ?? 0;
            $shipment->qty_type = $request->packing_items[0]['qty_type'] ?? 'CTN';
            $shipment->weight_value = $request->packing_items[0]['weight_value'] ?? 0;
            $shipment->volume_qty = $request->packing_items[0]['volume_qty'] ?? 0;
        }
        if ($request->has('containers') && count($request->containers) > 0) {
            $shipment->container_number = $request->containers[0]['container_number'] ?? null;
            $shipment->container_type = $request->containers[0]['container_type'] ?? null;
            $shipment->seal_number = $request->containers[0]['seal_number'] ?? null;
        }
    }

    private function validateShipment(Request $request, $id = null)
    {
        $uniqueRef = 'unique:shipments,agent_reference';
        if ($id) $uniqueRef .= ',' . $id;

        $request->validate([
            'agent_reference'    => ['required', 'string', 'max:50', $uniqueRef],
            'shippers_reference' => 'required|string|max:255',
            'mbl'                => 'required|string|max:50',
            'mode'               => 'required|in:LCL,FCL,AIR',
            
            // Shipper (теперь обязательны, так как XML требует их)
            'shipper_name'       => 'required|string|max:255',
            'shipper_address1'   => 'required|string|max:255', 
            'shipper_city'       => 'required|string|max:100',
            'shipper_country'    => 'required|string|size:2',
            
            // Consignee
            'consignee_name'     => 'required|string|max:255',
            'consignee_address1' => 'required|string|max:255',
            'consignee_country'  => 'required|string|size:2',

            // JSON валидация
            'packing_items'                 => 'required|array|min:1',
            'packing_items.*.qty_value'     => 'required|integer|min:1',
            'packing_items.*.weight_value'  => 'required|numeric|min:0',
        ]);
    }
}



