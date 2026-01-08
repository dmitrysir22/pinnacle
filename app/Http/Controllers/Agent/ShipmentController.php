<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShipmentController extends Controller
{
    public function index()
    {
        $shipments = Shipment::where('agent_id', Auth::user()->agent_id)
            ->latest()
            ->get();

        return view('agent.shipments.index', compact('shipments'));
    }

    public function create()
    {
        return view('agent.shipments.create');
    }

    public function store(Request $request)
    {
        $this->validateShipment($request);

        $shipment = new Shipment($request->all());
        $shipment->agent_id = Auth::user()->agent_id;
        $shipment->user_id = Auth::id();
        $shipment->status = 'pending';
        
        $shipment->save();

        return redirect()->route('agent.dashboard')
            ->with('success', 'Shipment ' . $shipment->agent_reference . ' created successfully!');
    }

    public function edit(Shipment $shipment)
    {
        if ($shipment->agent_id !== auth()->user()->agent_id) {
            abort(403);
        }
        return view('agent.shipments.create', compact('shipment'));
    }

    public function update(Request $request, Shipment $shipment)
    {
        if ($shipment->agent_id !== auth()->user()->agent_id) {
            abort(403);
        }

        $this->validateShipment($request, $shipment->id);

        $shipment->update($request->all());

        return redirect()->route('agent.dashboard')
            ->with('success', 'Shipment updated successfully!');
    }

    public function destroy(Shipment $shipment)
    {
        if ($shipment->agent_id !== auth()->user()->agent_id) {
            abort(403);
        }
        $shipment->delete();
        return redirect()->route('agent.dashboard')
            ->with('success', 'Shipment deleted successfully!');
    }

    /**
     * Валидация согласно требованиям XML скрипта
     */
    private function validateShipment(Request $request, $id = null)
    {
        $uniqueRule = 'unique:shipments,agent_reference';
        if ($id) {
            $uniqueRule .= ',' . $id;
        }

        $request->validate([
            // Основные ссылки
            'agent_reference'    => ['required', 'string', 'max:50', $uniqueRule],
            'shippers_reference' => 'required|string|max:255', // Required by XML
            'mbl'                => 'required|string|max:50',  // Required by XML validation

            // Стороны (Parties)
            'shipper_name'       => 'required|string|max:255',
            'shipper_address1'   => 'nullable|string|max:255', 
            'shipper_city'       => 'nullable|string|max:100',
            'shipper_country'    => 'nullable|string|size:2', // ISO code expected

            'consignee_name'     => 'required|string|max:255',
            'consignee_address1' => 'nullable|string|max:255',
            'consignee_country'  => 'nullable|string|size:2',

            // Маршрут и Транспорт
            'mode'             => 'required|in:LCL,FCL,AIR',
            'origin_port'      => 'required|string|max:5', // UNLOCO usually 5 chars
            'destination_port' => 'required|string|max:5',
            'carrier_name'     => 'required|string|max:100', // Required by XML
            'vessel'           => 'required|string|max:100', // Required by XML
            'voyage'           => 'required|string|max:50',  // Required by XML
            'incoterms'        => 'nullable|string|max:3',   // e.g. FOB

            // Груз
            'qty_value'        => 'required|integer|min:1',
            'weight_value'     => 'required|numeric|min:0',
            'goods_description'=> 'required|string', // Commodity
            
            // Контейнер (Если FCL, то номер обязателен, но оставим опционально для гибкости)
            'container_number' => 'nullable|string|max:20',
        ]);
    }
}