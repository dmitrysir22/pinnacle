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
            'name'  => 'is_approved',
            'label' => 'Approved',
            'type'  => 'boolean',
        ]);
    }


   private function addCustomFields()
   {


// 2. Получаем ID роли агента для JS
        $agentRole = Role::where('name', 'AgentUser')->first();
        $agentRoleId = $agentRole ? $agentRole->id : 0;

        // 3. Поле Approved
        $this->crud->addField([
            'name'  => 'is_approved',
            'label' => 'Approved',
            'type'  => 'checkbox',
        ]);

        // 4. Поля Агента (Добавляем класс 'agent-dependent-field')
        // Этот класс нужен, чтобы JS знал, что скрывать/показывать
$this->crud->addField([
    'label'     => "Assign to Organization",
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
	
$this->crud->addField([
            'name'  => 'custom_js_logic',
            'type'  => 'custom_html',
            'value' => "
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // ID роли агента из PHP
                        const agentRoleId = '{$agentRoleId}'; 
                        
                        // Находим все чекбоксы ролей (в Backpack они обычно name='roles_show[]')
                        const roleCheckboxes = document.querySelectorAll('input[name=\"roles_show[]\"]');
                        
                        // Находим поля, которые нужно прятать (по классу, который мы дали выше)
                        const agentFields = document.querySelectorAll('.agent-dependent-field');

                        function toggleAgentFields() {
                            let isAgentSelected = false;

                            // Проверяем, отмечена ли роль AgentUser
                            roleCheckboxes.forEach(cb => {
                                if (cb.value == agentRoleId && cb.checked) {
                                    isAgentSelected = true;
                                }
                            });

                            agentFields.forEach(field => {
                                const input = field.querySelector('select, input');
                                
                                if (isAgentSelected) {
                                    // ПОКАЗАТЬ
                                    field.style.display = 'block';
                                    // Сделать обязательным (браузерная проверка)
                                    if(input) input.setAttribute('required', 'required');
                                } else {
                                    // СКРЫТЬ
                                    field.style.display = 'none';
                                    // Убрать обязательность, иначе форма не отправится
                                    if(input) input.removeAttribute('required');
                                    // Опционально: очистить значение при скрытии
                                    // if(input) input.value = ''; 
                                }
                            });
                        }

                        // Вешаем обработчик на клики
                        roleCheckboxes.forEach(cb => {
                            cb.addEventListener('change', toggleAgentFields);
                        });

                        // Запускаем один раз при загрузке страницы (для редактирования)
                        toggleAgentFields();
                    });
                </script>
            "
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
