<!-- Header -->
<header class="admin-topbar">
    <div class="admin-topbar-titles">
        <h1>@yield('page_title', 'Dashboard')</h1>
        <p class="text-muted mb-0 small">
            Welcome back,
            @if(auth('branch')->check())
                {{ auth('branch')->user()->name ?? 'Branch Manager' }}
            @elseif(auth('admin')->check())
                {{ auth('admin')->user()->name ?? 'Admin' }}
            @else
                Guest
            @endif
        </p>
    </div>

    <div class="admin-topbar-search d-none d-md-block">
        <form action="{{ auth('branch')->check() ? '#' : route('admin.search') }}" method="GET" class="w-100 position-relative" role="search">
            <label class="visually-hidden" for="admin-header-search">Search</label>
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" aria-hidden="true"></i>
            <input type="text" name="q" id="admin-header-search" class="form-control rounded-pill ps-5 border shadow-sm py-2"
                   placeholder="{{ auth('branch')->check() ? 'Search branch…' : 'Search operations…' }}"
                   value="{{ request('q') }}"
                   @if(auth('branch')->check()) disabled @endif>
        </form>
    </div>

    <div class="d-flex align-items-center gap-2">
        @if(auth('admin')->check())
            @php $adminUnread = \App\Models\AdminNotification::where('is_read', false)->count(); @endphp
            <a href="{{ route('admin.notifications.index') }}" class="btn btn-light border rounded-circle p-2 position-relative" aria-label="Notifications{{ $adminUnread ? ', '.$adminUnread.' unread' : '' }}">
                <i class="bi bi-bell"></i>
                @if($adminUnread)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $adminUnread }}</span>
                @endif
            </a>
        @endif
        <div class="dropdown">
            <button class="btn btn-white shadow-sm border rounded-pill px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle me-1"></i>
                <span class="d-none d-sm-inline">
                    @if(auth('admin')->check())
                        {{ auth('admin')->user()->name }}
                    @elseif(auth('branch')->check())
                        {{ auth('branch')->user()->name }}
                    @else
                        Guest
                    @endif
                </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                @if(auth('admin')->check())
                    <li><a class="dropdown-item" href="{{ route('admin.settings.index') }}"><i class="bi bi-gear me-2"></i> Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                @endif
                <li>
                    <form action="{{ auth('branch')->check() ? route('branch.logout') : route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
