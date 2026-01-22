<?php

namespace App\Http\Controllers\Admin;

use Backpack\PermissionManager\app\Http\Controllers\UserCrudController as BaseUserCrudController;
use App\Models\User;
use App\Notifications\AgentApprovedNotification;
use Spatie\Permission\Models\Role; 

class UserCrudController extends BaseUserCrudController
{
    public function setupListOperation()
    {
    //die('MY USER CRUD');
		
        parent::setupListOperation();

    $this->crud->addColumn([
        'name'  => 'last_login_at',
        'label' => 'Last Login',
        'type'  => 'datetime',
        'format'=> 'DD.MM.YYYY HH:mm',
    ]);

    $this->crud->addColumn([
        'name'  => 'login_count',
        'label' => 'Logins',
        'type'  => 'number',
    ]);
	
        $this->crud->addColumn([
            'name'  => 'is_approved',
            'label' => 'Approved',
            'type'  => 'boolean',
        ]);
    }


   private function addCustomFields()
   {


$this->crud->modifyField('password', [
    'hint' => 'Leave blank unless you are manually changing the users password for them.',
]);

$entry = $this->crud->getCurrentEntry();

$this->crud->addField([
    'name' => 'last_login_at',
    'type' => 'custom_html',
    'value' => 'Last Login: ' . (
        $entry && $entry->last_login_at
            ? $entry->last_login_at->format('d.m.Y H:i')
            : '—'
    ),
]);


$this->crud->addField([
    'name'  => 'login_count',
    'type'  => 'custom_html',
    'value' => 'Login Count: '.$this->crud->getCurrentEntry()->login_count
]);

// 2. Получаем ID роли агента для JS
        $agentRole = Role::where('name', 'AgentUser')->first();
        $agentRoleId = $agentRole ? $agentRole->id : 0;

        // 3. Поле Approved
        $this->crud->addField([
            'name'  => 'is_approved',
            'label' => 'Approved',
            'type'  => 'checkbox',
			'hint' => 'If checked this user will be able to login to the portal. Once checked the system will notify the user by email that their account has been activated. '


        ]);

        // 4. Поля Агента (Добавляем класс 'agent-dependent-field')
        // Этот класс нужен, чтобы JS знал, что скрывать/показывать
$this->crud->addField([
    'label'     => "Assign to Agent",
    'type'      => 'select',
    'name'      => 'organization_id', // Новое имя колонки
    'entity'    => 'organization',    // Новое имя связи в модели User
    'model'     => "App\Models\Organization",
    'attribute' => 'name',
    'wrapper'     => ['class' => 'form-group col-md-12 agent-dependent-field'], // <--- ВАЖНО

]);

        $this->crud->addField([
            'name'        => 'access_level',
            'label'       => 'Access Level',
            'type'        => 'enum', // Или 'select_from_array' если enum глючит
            'wrapper'     => ['class' => 'form-group col-md-12 agent-dependent-field'], // <--- ВАЖНО
			'hint'        => '<b>Individual:</b> can see only their shipments. <b>Full:</b> can see all organization shipments.', // Можно использовать HTML
        ]);
		
$this->crud->addField([
        'name'  => 'email_verified_at',
        'type'  => 'hidden',
    ]);
	
   }

    public function setupCreateOperation()
    {
        parent::setupCreateOperation();
        $this->addCustomFields();

    }

    public function setupUpdateOperation()
    {
        parent::setupUpdateOperation();

        $this->addCustomFields();
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
