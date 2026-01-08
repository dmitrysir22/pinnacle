{{-- 

{{-- Dashboard Link --}}
<li class="nav-item">
    <a class="nav-link" href="{{ backpack_url('dashboard') }}">
        <i class="la la-home nav-icon"></i> 
        <span class="nav-link-title">{{ trans('backpack::base.dashboard') }}</span>
    </a>
</li>

{{-- Authentication Dropdown --}}
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#navbar-auth" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
        <i class="la la-group nav-icon"></i>
        <span class="nav-link-title">Authentication</span>
    </a>
    <div class="dropdown-menu">
        <div class="dropdown-menu-columns">
            <div class="dropdown-menu-column">
                <a class="dropdown-item" href="{{ backpack_url('user') }}">
                    <i class="la la-user nav-icon me-2"></i> Users
                </a>
                <a class="dropdown-item" href="{{ backpack_url('role') }}">
                    <i class="la la-id-badge nav-icon me-2"></i> Roles
                </a>
                <a class="dropdown-item" href="{{ backpack_url('permission') }}">
                    <i class="la la-key nav-icon me-2"></i> Permissions
                </a>
            </div>
        </div>
    </div>
</li>

<x-backpack::menu-item title="Agents" icon="la la-question" :link="backpack_url('agent')" />