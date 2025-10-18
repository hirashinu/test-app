<!-- need to remove -->
<li class="nav-item">
    <a href="{{ route('home') }}" class="nav-link {{ Request::is('home') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Home</p>
    </a>
</li>
<li class="nav-item">
    <a href="{{ route('master-item.index') }}" class="nav-link {{ Request::is('master-item.index') ? 'active' : '' }}">
        <i class="nav-icon fas fa-archive"></i>
        <p>Master Item</p>
    </a>
</li>
