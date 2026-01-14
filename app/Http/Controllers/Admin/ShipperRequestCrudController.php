<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class ShipperRequestCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\ShipperRequest::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/shipper-request');
        CRUD::setEntityNameStrings('shipper request', 'shipper requests');
    }

    protected function setupListOperation()
    {
        $this->crud->orderBy('created_at', 'DESC');

        // 1. Обновленные статусы в списке
        CRUD::column('status')->type('select_from_array')->options($this->getStatusOptions())
        ->wrapper([
            'element' => 'span',
            'class' => function ($crud, $column, $entry) {
                return match($entry->status) {
                    'pending' => 'badge bg-warning text-dark',
                    'created_in_cw' => 'badge bg-success',
                    'rejected' => 'badge bg-danger',
                    default => 'badge bg-secondary',
                };
            }
        ]);

        CRUD::column('name')->label('New Shipper Name');
        
        // 2. Показываем имя Агента (через связь user)
        CRUD::column('user_id')
            ->type('select')
            ->label('Requested by Agent')
            ->entity('user')      // имя метода связи в модели ShipperRequest
            ->attribute('name')   // поле из таблицы users
            ->model('App\Models\User');

       /* CRUD::column('organization_id')
            ->type('select')
            ->label('Agent Organization')
            ->entity('organization')
            ->attribute('name');
*/
        CRUD::column('created_at')->label('Date');
    }

    protected function setupUpdateOperation()
    {
        // 3. Обновленные статусы в форме редактирования
        CRUD::field('status')
            ->type('select_from_array')
            ->options($this->getStatusOptions())
            ->wrapper(['class' => 'form-group col-md-6']);
        
        // 4. Внутренние заметки (только для админов)
        CRUD::addField([
            'name'  => 'admin_comment',
            'label' => 'Pinnacle Internal Notes',
            'type'  => 'textarea',
            'hint'  => 'This information is only visible to Pinnacle staff.',
            'wrapper' => ['class' => 'form-group col-md-12'],
        ]);

        // Группируем данные заявки (только для чтения)
        CRUD::field('name')->label('Requested Name')->attributes(['readonly' => 'true'])->wrapper(['class' => 'form-group col-md-6']);
        CRUD::field('address1')->attributes(['readonly' => 'true'])->wrapper(['class' => 'form-group col-md-6']);
        CRUD::field('city')->attributes(['readonly' => 'true'])->wrapper(['class' => 'form-group col-md-4']);
        CRUD::field('country')->attributes(['readonly' => 'true'])->wrapper(['class' => 'form-group col-md-4']);
    }

    /**
     * Выносим опции статуса в отдельный метод для удобства
     */
    private function getStatusOptions()
    {
        return [
            'pending'       => 'Pending',
            'created_in_cw' => 'Created In CargoWise',
            'rejected'      => 'Rejected'
        ];
    }
}