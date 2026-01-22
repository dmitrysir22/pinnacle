<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Http\Requests\ShipmentRequest; // <-- Используем ТОТ ЖЕ Request, что и на портале

class ShipmentCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Shipment::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/shipment');
        CRUD::setEntityNameStrings('shipment', 'all shipments');

        // Админ видит всё, так что здесь не нужны scope (если они не глобальные)
    }

 protected function setupListOperation()
{
    // 1. ЛОГИКА ФИЛЬТРАЦИИ (БЕСПЛАТНАЯ)
    if (request()->has('status')) {
        $this->crud->addClause('where', 'status', request('status'));
    }

    if (request()->has('org_id')) {
        $this->crud->addClause('whereHas', 'user', function($query) {
            $query->where('organization_id', request('org_id'));
        });
    }

    $this->crud->orderBy('created_at', 'DESC');

    // 2. ДОБАВЛЕНИЕ ВИДЖЕТА (Используем тип 'view')
    $counts = \App\Models\Shipment::select('status', \DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();
    
    $allCount = \App\Models\Shipment::count();

    \Backpack\CRUD\app\Library\Widget::add([
        'type'      => 'view', 
        'view'      => 'admin.shipment_filters', // Путь к созданному выше файлу
        'section'   => 'before_content',
        'counts'    => $counts,    // Передаем данные в Blade
        'all_count' => $allCount,
    ]);

    // 3. КОЛОНКИ
    CRUD::column('id')->label('#');

    CRUD::column('status')
        ->type('select_from_array')
        ->options([
            'pending'    => 'Pending',
            'processing' => 'Processing',
            'completed'  => 'Completed',
            'cancelled'  => 'Cancelled',
        ])
        ->wrapper([
            'element' => 'span',
            'class' => function ($crud, $column, $entry) {
                return match($entry->status) {
                    'pending'    => 'badge bg-warning text-dark',
                    'processing' => 'badge bg-info',
                    'completed'  => 'badge bg-success',
                    'cancelled'  => 'badge bg-danger',
                    default      => 'badge bg-secondary',
                };
            }
        ]);

    // Агент
    CRUD::column('user_id')
        ->type('select')
        ->label('Agent')
        ->entity('user')
        ->attribute('name')
        ->model('App\Models\User');

    // Организация с логикой поиска
    CRUD::column('user.organization.name')
        ->label('Organization')
        ->type('text')
        ->searchLogic(function ($query, $column, $searchTerm) {
            $query->orWhereHas('user.organization', function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%'.$searchTerm.'%');
            });
        });

    CRUD::column('shipper_name')->label('Shipper');

    CRUD::column('incoterms')->label('Inco');
    
    CRUD::column('created_at')
        ->label('Date')
        ->type('datetime');
}



protected function setupUpdateOperation()
{
    // 1. Сначала вызываем все поля (создадим их ниже)
    $this->setupCreateOperation();

    // 2. Получаем текущую запись
    $entry = $this->crud->getCurrentEntry();

    // 3. Чтобы в простом 'select' отобразилось текущее значение, 
    // если у нас нет shipper_id в базе, нам нужно "подсунуть" его.
    // Но лучше просто использовать текстовое поле 'shipper_name', 
    // а список выбора сделать вспомогательным.
}

protected function setupCreateOperation()
{
    CRUD::setValidation(\App\Http\Requests\ShipmentRequest::class);

    // --- TAB: GENERAL ---
    CRUD::field('agent_reference')->tab('General')->wrapper(['class' => 'form-group col-md-4']);
    CRUD::field('shippers_reference')->tab('General')->wrapper(['class' => 'form-group col-md-4']);
    CRUD::field('mbl')->label('Master B/L')->tab('General')->wrapper(['class' => 'form-group col-md-4']);
    CRUD::field('pinnacle_office')->default('PININTLCS')->tab('General');

    // --- TAB: PARTIES ---
    $shippers = \App\Models\Shipper::pluck('name', 'name')->toArray();

    CRUD::addField([
        'name'        => 'shipper_name',
        'label'       => "Select Shipper",
        'type'        => 'select_from_array', // Бесплатно и работает
        'options'     => $shippers,
        'allows_null' => true,
        'tab'         => 'Parties (Addresses)',
        'wrapper'     => ['class' => 'form-group col-md-12'],
    ]);

    CRUD::field('shipper_address1')->label('Address')->tab('Parties (Addresses)')->wrapper(['class' => 'form-group col-md-6']);
    CRUD::field('shipper_city')->label('City')->tab('Parties (Addresses)')->wrapper(['class' => 'form-group col-md-3']);
    CRUD::field('shipper_country')->label('Country')->tab('Parties (Addresses)')->wrapper(['class' => 'form-group col-md-3']);

    CRUD::field('consignee_name')->tab('Parties (Addresses)')->wrapper(['class' => 'form-group col-md-6']);
    CRUD::field('consignee_address1')->label('Consignee Address')->tab('Parties (Addresses)')->wrapper(['class' => 'form-group col-md-6']);

    // --- TAB: TRANSPORT ---
    CRUD::field('mode')->type('select_from_array')->options(['LCL'=>'LCL','FCL'=>'FCL','AIR'=>'AIR'])->tab('Transport')->wrapper(['class' => 'form-group col-md-4']);
    CRUD::field('incoterms')->tab('Transport')->wrapper(['class' => 'form-group col-md-4']);
    CRUD::field('carrier_name')->tab('Transport')->wrapper(['class' => 'form-group col-md-4']);
    CRUD::field('vessel')->tab('Transport')->wrapper(['class' => 'form-group col-md-4']);
    CRUD::field('voyage')->tab('Transport')->wrapper(['class' => 'form-group col-md-4']);
    CRUD::field('origin_port')->tab('Transport')->wrapper(['class' => 'form-group col-md-2']);
    CRUD::field('destination_port')->tab('Transport')->wrapper(['class' => 'form-group col-md-2']);

    // --- TAB: CARGO ---
    CRUD::field('qty_value')->label('Qty')->type('number')->tab('Cargo & Specs')->wrapper(['class' => 'form-group col-md-3']);
    CRUD::field('qty_type')->label('Type')->tab('Cargo & Specs')->wrapper(['class' => 'form-group col-md-3']);
    CRUD::field('weight_value')->label('Weight (KG)')->type('number')->tab('Cargo & Specs')->wrapper(['class' => 'form-group col-md-3']);
    CRUD::field('volume_qty')->label('Volume (CBM)')->type('number')->tab('Cargo & Specs')->wrapper(['class' => 'form-group col-md-3']);

    // --- СКРЫТОЕ ПОЛЕ ДЛЯ ПОДКЛЮЧЕНИЯ СКРИПТА ---
    CRUD::addField([
        'name' => 'autofill_logic',
        'type' => 'view',
        'view' => 'admin/shipper_logic', // Наш созданный файл
    ]);
}


    protected function setupShowOperation()
    {
        $this->setupListOperation();
        // Можно добавить больше деталей, которые скрыты в таблице
        CRUD::column('pickup_address');
        CRUD::column('delivery_address');
        CRUD::column('description_of_goods');
    }

// Внутри класса ShipmentCrudController

public function getShipperInfo(\Illuminate\Http\Request $request)
{
    $name = $request->query('name');
    
    if (!$name) {
        return response()->json(null);
    }

    $shipper = \App\Models\Shipper::where('name', $name)->first();

    return response()->json($shipper);
}

}