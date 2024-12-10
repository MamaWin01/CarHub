<header class="bg-light shadow-sm">
    <div class="container py-2">
        <div class="d-flex justify-content-between align-items-center">
            <!-- Logo -->
            <div class="d-flex align-items-center">
                <a href="{{ url('/') }}" class="text-decoration-none text-dark">
                    <img src="{{ asset('images/logo.jpg') }}" alt="CarHub Logo" height="40">
                </a>
            </div>

            <!-- Navigation -->
            <nav class="d-flex align-items-center">
                <a href="{{ route('vehicle_list.index') }}" class="text-decoration-none mx-3 text-dark">List Mobil</a>
                <a href="#" class="text-decoration-none mx-3 text-dark">Wishlist</a>
                <a href="#" class="text-decoration-none mx-3 text-dark">Chat</a>

                <!-- Authentication -->
                @guest
                    <a href="" class="btn btn-outline-secondary mx-2">Daftar</a>
                    <a href="" class="btn btn-dark mx-2">Register</a>
                @else
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle text-decoration-none text-dark" data-bs-toggle="dropdown">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ route('profile.show') }}" class="dropdown-item">Profile</a></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endguest
            </nav>
        </div>
    </div>
</header>
