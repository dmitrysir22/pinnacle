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
        $user = Auth::user();
        // Фильтруем грузы по агенту
        $shipments = Shipment::where('agent_id', $user->agent_id)
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
        // 1. Валидация (обязательные поля)
        $validated = $request->validate([
            'agent_reference' => 'required|unique:shipments,agent_reference',
            'shipper_name'    => 'required|string|max:255',
            'consignee_name'  => 'required|string|max:255',
            'origin_port'     => 'required|string',
            'destination_port'=> 'required|string',
            'qty_value'       => 'required|integer',
            'weight_value'    => 'required|numeric',
        ]);

        // 2. Добавляем системные данные (кто создал)
        // Важно: берем ID агента из сессии, а не из формы (безопасность)
        $shipment = new Shipment($request->all());
        $shipment->agent_id = Auth::user()->agent_id;
        $shipment->user_id = Auth::id();
        $shipment->status = 'pending';
        
        $shipment->save();

         return redirect()->route('agent.dashboard')
           ->with('success', 'Shipment ' . $shipment->agent_reference . ' created successfully!');
    }
	
// app/Http/Controllers/Agent/ShipmentController.php

public function edit(Shipment $shipment)
{
    // Защита: агент может редактировать только свои грузы
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

    $validated = $request->validate([
        // Игнорируем ID текущей записи при проверке уникальности
        'agent_reference' => 'required|unique:shipments,agent_reference,' . $shipment->id,
        'shipper_name'    => 'required|string|max:255',
        'consignee_name'  => 'required|string|max:255',
        'origin_port'     => 'required|string',
        'destination_port'=> 'required|string',
        'qty_value'       => 'required|integer',
        'weight_value'    => 'required|numeric',
    ]);

    $shipment->update($request->all());

    return redirect()->route('agent.dashboard')
        ->with('success', 'Shipment updated successfully!');
}
    // Удаление
    public function destroy(Shipment $shipment)
    {
        if ($shipment->agent_id !== auth()->user()->agent_id) {
            abort(403);
        }

        $shipment->delete();

      return redirect()->route('agent.dashboard')
        ->with('success', 'Shipment deleted successfully!');
    }	
}