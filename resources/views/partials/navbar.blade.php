<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ url('/') }}">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" width="180" height="50" class="d-inline-block align-text-top p-1">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ url('/') }}">Home</a>
                </li>
                <!-- Cek jika pengguna sudah login -->
                @if(Auth::check())
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ url('/history') }}">History</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-danger text-white mx-2 p-2" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>
                </li>
                @else
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ url('/login') }}">Login</a>
                </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

<!-- Modal Logout -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin logout?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>