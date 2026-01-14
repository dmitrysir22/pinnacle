<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\OrganizationRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class AgentCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class OrganizationCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Organization::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/organization');
        CRUD::setEntityNameStrings('organization', 'Organizations');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
protected function setupListOperation()
{
    CRUD::column('name')->label('Company Name');
    CRUD::column('code')->label('Organization Code');
    CRUD::column('created_at')->label('Registered');
}

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
protected function setupCreateOperation()
{
    CRUD::setValidation(OrganizationRequest::class);

    // Название компании
    CRUD::addField([
        'name'  => 'name',
        'type'  => 'text',
        'label' => 'Company/Organization Name (e.g. Hecny Transport)',
    ]);

    // Код организации (для связи с XML/CargoWise)
    CRUD::addField([
        'name'  => 'code',
        'type'  => 'text',
        'label' => 'Organization Code (ID)',
        'hint'  => 'The unique ID used in XML files (e.g. 902204190)',
    ]);
}

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
