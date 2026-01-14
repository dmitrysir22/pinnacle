<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shipment;
class AgentDashboardController extends Controller
{
    /**
     * Показ главной страницы портала агента.
     */
	 
	 
public function index()
{
    $user = Auth::user();

    // Если у пользователя уровень доступа "Full Access" — видит всё от компании (organization_id)
    // Если "Individual" — только свои (user_id)
    $query = Shipment::where('organization_id', $user->organization_id);

    if ($user->access_level === 'individual') {
        $query->where('user_id', $user->id);
    }

    $shipments = $query->latest()->get();

    return view('agent.dashboard', compact('shipments'));
}

    /**
     * Выход из системы.
     */
    public function logout(Request $request)
    {
        // Выход пользователя
        Auth::logout();

        // Очистка сессии для безопасности
        $request->session()->invalidate();

        // Регенерация CSRF-токена (защита от повторного использования сессии)
        $request->session()->regenerateToken();

        // Перенаправление на страницу входа
        return redirect('/login');
    }
}