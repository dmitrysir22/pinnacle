<?php

namespace App\Http\Controllers\Admin;

use Backpack\PermissionManager\app\Http\Controllers\UserCrudController as BaseUserCrudController;
use App\Models\User;
use App\Notifications\AgentApprovedNotification;

class UserCrudController extends BaseUserCrudController
{
    public function setupListOperation()
    {
    //die('MY USER CRUD');
		
        parent::setupListOperation();

        $this->crud->addColumn([
            'name'  => 'is_approved',
            'label' => 'Approved',
            'type'  => 'boolean',
        ]);
    }

    public function setupCreateOperation()
    {
        parent::setupCreateOperation();

        $this->crud->addField([
            'name'  => 'is_approved',
            'label' => 'Approved',
            'type'  => 'checkbox',
        ]);
		
$this->crud->addField([
    'label'     => "Assign to Agent (Organization)",
    'type'      => 'select',
    'name'      => 'agent_id',
    'entity'    => 'agent',
    'model'     => "App\Models\Agent",
    'attribute' => 'name',
]);

$this->crud->addField([
    'name'  => 'access_level',
    'label' => 'Access Level',
    'type'  => 'enum',
]);		
$this->crud->addField([
        'name'  => 'email_verified_at',
        'type'  => 'hidden',
    ]);
    }

    public function setupUpdateOperation()
    {
        parent::setupUpdateOperation();

        $this->crud->addField([
            'name'  => 'is_approved',
            'label' => 'Approved',
            'type'  => 'checkbox',
        ]);

$this->crud->addField([
    'label'     => "Assign to Agent (Organization)",
    'type'      => 'select',
    'name'      => 'agent_id',
    'entity'    => 'agent',
    'model'     => "App\Models\Agent",
    'attribute' => 'name',
]);

$this->crud->addField([
    'name'  => 'access_level',
    'label' => 'Access Level',
    'type'  => 'enum',
]);   
$this->crud->addField([
        'name'  => 'email_verified_at',
        'type'  => 'hidden',
    ]);

	}


public function store()
{
    $this->handleEmailVerification();
    return parent::store();
}

    public function update()
    {
        // 1️⃣ Получаем текущего пользователя ДО обновления
        $userId = $this->crud->getCurrentEntryId();
        $userBefore = User::find($userId);

        $wasApproved = (bool) $userBefore?->is_approved;

        // 2️⃣ Применяем email_verified_at если approved
        $this->handleEmailVerification();

        // 3️⃣ Обновляем запись
        $response = parent::update();

        // 4️⃣ Перезагружаем пользователя
        $userAfter = User::find($userId);

        // 5️⃣ Проверяем переход 0 → 1
        if (
            ! $wasApproved &&
            $userAfter &&
            $userAfter->is_approved
        ) {
            // 🔔 Отправляем письмо
            $userAfter->notify(new AgentApprovedNotification());
        }

        return $response;
    }


protected function handleEmailVerification()
{
    $request = $this->crud->getRequest();
    // Если чекбокс "Approved" нажат
    if ($request->input('is_approved') == 1) {
        // Устанавливаем текущую дату верификации в запрос, 
        // чтобы Backpack сохранил её вместе с остальными полями
        $request->merge([
            'email_verified_at' => now(),
        ]);

    } else {
        // Если администратор снял галочку одобрения, 
        // можно опционально аннулировать верификацию (по желанию)
        // $request->merge(['email_verified_at' => null]);
    }
}   
}
