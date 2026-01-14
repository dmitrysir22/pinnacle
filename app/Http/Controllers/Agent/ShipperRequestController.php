<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\ShipperRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShipperRequestController extends Controller
{
    /**
     * Обработка AJAX запроса на создание нового отправителя
     */
    public function store(Request $request)
    {
        // 1. Валидация данных
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'address1' => 'nullable|string|max:255',
            'city'     => 'nullable|string|max:100',
            'country'  => 'nullable|string|max:2', // В БД у нас varchar(2)
        ]);

        $user = Auth::user();

        // Проверка: привязан ли юзер к организации
        if (!$user->organization_id) {
            return response()->json([
                'message' => 'User is not associated with any organization.'
            ], 403);
        }

        // 2. Создание заявки
        $shipperRequest = ShipperRequest::create([
            'organization_id' => $user->organization_id, // Привязываем к компании юзера
            'user_id'         => $user->id,              // Кто конкретно нажал кнопку
            'status'          => 'pending',              // Статус по умолчанию
            
            'name'            => $validated['name'],
            'address1'        => $request->address1,     // Можно брать напрямую, если поле nullable
            'city'            => $request->city,
            'country'         => $request->country ? strtoupper($request->country) : null,
            
            // address2, state, postcode можно добавить, если они есть в форме
        ]);

        // 3. Ответ JSON для JavaScript
        return response()->json([
            'success' => true,
            'message' => 'Request created successfully',
            'id'      => $shipperRequest->id
        ]);
    }
}