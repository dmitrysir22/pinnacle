<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class ShipperCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    // ДОБАВЛЯЕМ ЭТУ ТРЕЙТ-ОПЕРАЦИЮ ДЛЯ ВОЗМОЖНОСТИ РЕДАКТИРОВАНИЯ
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Shipper::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/shipper');
        CRUD::setEntityNameStrings('cargowise shipper', 'cargowise shippers');
    }

    protected function setupListOperation()
    {
        CRUD::column('external_code')->label('CW Code');
        CRUD::column('name');
        
        CRUD::column('organizations')
        ->type('select') // В списке 'select' умеет выводить связи N:M через запятую
        ->label('Available to Orgs')
        ->attribute('name')
        ->entity('organizations');
            
        CRUD::column('city');
        CRUD::column('country');
    }

protected function setupUpdateOperation() 
{
    CRUD::field('external_code')->label('CW Code')->attributes(['readonly' => true]);
    CRUD::field('name'); 
    
    CRUD::addField([
        'label'     => "Assign to Organizations",
        'type'      => 'select_multiple', // Используем стандартный тип для N:M
        'name'      => 'organizations',    // Метод связи в модели
        'entity'    => 'organizations',    // Метод связи в модели
        'attribute' => 'name',             // Поле для отображения
        'pivot'     => true,               // Обязательно для belongsToMany
    ]);

    CRUD::field('city')->size(6);
    CRUD::field('country')->size(6);
}

    protected function setupShowOperation()
    {
        $this->setupListOperation();
    }
}