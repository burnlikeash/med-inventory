@php
    $user = auth()->user();
@endphp

<aside class="sidebar">
    <div class="sidebar-brand">
        <strong>Medical Inventory</strong>
        <span>Equipment &amp; asset tracking</span>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
            Dashboard
        </a>

        <div class="nav-section">Inventory</div>

        <a href="{{ route('equipment.index') }}" class="{{ request()->routeIs('equipment.index', 'equipment.show', 'equipment.edit', 'equipment.scan') ? 'is-active' : '' }}">
            Equipment
        </a>

        @if ($user->canManageInventory())
            <a href="{{ route('equipment.create') }}" class="{{ request()->routeIs('equipment.create') ? 'is-active' : '' }}">
                Add Equipment
            </a>

            <a href="{{ route('equipment.archived') }}" class="{{ request()->routeIs('equipment.archived') ? 'is-active' : '' }}">
                Archived Equipment
            </a>

            <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'is-active' : '' }}">
                Categories
            </a>
        @endif

        <div class="nav-section">Organization</div>

        <a href="{{ route('departments.index') }}" class="{{ request()->routeIs('departments.*') ? 'is-active' : '' }}">
            Departments
        </a>

        <a href="{{ route('locations.index') }}" class="{{ request()->routeIs('locations.*') ? 'is-active' : '' }}">
            Locations
        </a>

        @if ($user->canMaintainEquipment())
            <div class="nav-section">Operations</div>
        @endif

        @if ($user->canMaintainEquipment())
            <a href="{{ route('maintenance.due') }}" class="{{ request()->routeIs('maintenance.*') ? 'is-active' : '' }}">
                Maintenance Due
            </a>
        @endif

        @if ($user->canManageInventory())
            <a href="{{ route('disposal.index') }}" class="{{ request()->routeIs('disposal.*') ? 'is-active' : '' }}">
                Disposal Management
            </a>

            <div class="nav-section">Reports</div>

            <a href="{{ route('reports.equipment') }}" class="{{ request()->routeIs('reports.equipment') ? 'is-active' : '' }}">
                Equipment Report
            </a>

            <a href="{{ route('reports.maintenance') }}" class="{{ request()->routeIs('reports.maintenance') ? 'is-active' : '' }}">
                Maintenance Report
            </a>

            <a href="{{ route('reports.disposal') }}" class="{{ request()->routeIs('reports.disposal') ? 'is-active' : '' }}">
                Disposal Report
            </a>
        @endif

        @if ($user->isAdministrator())
            <div class="nav-section">Administration</div>

            <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'is-active' : '' }}">
                Users
            </a>

            <a href="{{ route('audit.index') }}" class="{{ request()->routeIs('audit.*') ? 'is-active' : '' }}">
                Audit Logs
            </a>
        @endif
    </nav>

    <div class="sidebar-user">
        <div class="user-name">{{ $user->name }}</div>
        <div class="user-role">{{ $user->role }}</div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-secondary btn-block">Log out</button>
        </form>
    </div>
</aside>
