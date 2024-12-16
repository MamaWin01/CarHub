<header class="bg-light shadow-sm">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Bundle JS (includes Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

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
                    <button class="btn btn-outline-secondary mx-2" data-bs-toggle="modal" data-bs-target="#registerModal">Daftar</button>
                    <button class="btn btn-dark mx-2" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button>
                @else
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle text-decoration-none text-dark" data-bs-toggle="dropdown">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu" style="padding-bottom:0px">
                            <li>
                                <form method="POST" action="{{ route('user.logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Edit Profile</button>
                                </form>
                                <form method="POST" action="{{ route('user.logout') }}">
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

<!-- Register Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registerModalLabel">Register</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('user.register') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        @error('password')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Register</button>
                    @error('register')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Login Modal -->
<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Login</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('user.login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="login-email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="login-email" name="login-email" value="{{ old('login-email') }}" required>
                        @error('login-email')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="login-password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="login-password" name="login-password" required>
                        @error('login-password')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Login</button>
                    @error('login')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </form>
                <!-- Redirect to Register Modal -->
                <div class="text-center mt-3">
                    <p>Belum punya akun? <button type="button" class="btn btn-link p-0" id="redirectToRegister">Register disini</button></p>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    @if ($errors->any())
        @if ($errors->has('name') || $errors->has('email') || $errors->has('password'))
            document.addEventListener("DOMContentLoaded", function() {
                var registerModal = new bootstrap.Modal(document.getElementById('registerModal'));
                registerModal.show();
            });
        @elseif ($errors->has('login-email') || $errors->has('login-password') || $errors->has('login'))
            document.addEventListener("DOMContentLoaded", function() {
                var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                loginModal.show();
            });
        @endif
    @endif

    document.addEventListener("DOMContentLoaded", function () {
        const redirectToRegisterButton = document.getElementById('redirectToRegister');

        redirectToRegisterButton.addEventListener('click', function () {
            const loginModal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
            const registerModal = new bootstrap.Modal(document.getElementById('registerModal'));

            // Hide login modal and show register modal
            loginModal.hide();
            registerModal.show();
        });
    });
</script>
