<?php

namespace App\Http\Controllers\Admin;

use Backpack\PermissionManager\app\Http\Controllers\UserCrudController as BaseUserCrudController;
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
    }

    public function setupUpdateOperation()
    {
        parent::setupUpdateOperation();

        $this->crud->addField([
            'name'  => 'is_approved',
            'label' => 'Approved',
            'type'  => 'checkbox',
        ]);
    }
}
