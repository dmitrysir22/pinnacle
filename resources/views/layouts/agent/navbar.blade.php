<header class="navbar navbar-expand-md navbar-dark d-print-none">
    <div class="container-xl">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <h1 class="navbar-brand navbar-brand-autodark d-none-xs pr-0 pr-md-3">
            <a href="{{ url('/dashboard') }}" class="text-white text-decoration-none">
               <img src="/agent/assets/logo.png" class="toplogo">
				
				
				
				<span class="badge bg-blue-lt ms-2" style="display:none">AGENT</span>
            </a>
        </h1>

        <div class="navbar-nav flex-row order-md-last">
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
                    <div class="d-none d-xl-block ps-2">
                        <div>{{ auth()->user()->name }}</div>
                        <div class="mt-1 small text-muted text-uppercase">
                            {{ auth()->user()->agent->name ?? 'No Agent' }}
                        </div>
                    </div>
                </a>

                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="#" class="dropdown-item"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Logout
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>

        <div class="collapse navbar-collapse" id="navbar-menu">
            <ul class="navbar-nav">
                <li class="nav-item {{ request()->is('*/dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('/dashboard') }}">
                        <span class="nav-link-title">Dashboard</span>
                    </a>
                </li>

                <li class="nav-item {{ request()->is('*/shipments*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('/shipments') }}">
                        <span class="nav-link-title">Shipments</span>
                    </a>
                </li>

                <li class="nav-item {{ request()->is('*/shippers*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('/shippers/create') }}">
                        <span class="nav-link-title">Add Shipper</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>
