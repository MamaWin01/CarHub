@extends('layouts.master')

@include('layouts.header')

@section('content')
<style>
    .rating i {
        font-size: 1.5rem;
    }
</style>
<div class="container py-5">
    <div class="row">
        <!-- Left: Vehicle Image -->
        <div class="col-md-6 position-relative">
            <!-- Wishlist Button -->
            <button id="wishlistButton" class="btn p-0 wishlist-icon position-absolute top-0" data-vehicle-id="{{ $vehicle->id }}" style="left: 18px;margin-top:10px; border: none; font-size: 2rem;">
                <i class="bi {{ $is_in_wishlist ? 'bi-heart-fill text-danger' : 'bi-heart' }}"></i>
            </button>
            <div class="border rounded bg-light">
                <img src="{{ asset('images/logo.jpg') }}" alt="{{ $vehicle->name }}" class="img-fluid rounded" style="height: 450px; object-fit: cover;">
            </div>
        </div>

        <!-- Right: Vehicle Info -->
        <div class="col-md-6">
            <h3>{{ $vehicle->name }}</h3>
            <h4 class="text-danger">Rp. {{ number_format($vehicle->price, 2, ',', '.') }}</h4>
            <p>Informasi Kendaraan</p>
            <ul>
                <li>Kondisi: {{ ucfirst($filters['condition'][$vehicle->condition]) }}</li>
                <li>Brand: {{ $vehicle->brand }} </li>
                <li>model: {{ $vehicle->model }} </li>
                <li>Transmisi: {{ $vehicle->transmision }} </li>
                <li>Bahan Bakar: {{ $filters['fuel_type'][$vehicle->fuel_type]  }} </li>
                <li>colour: {{ $vehicle->colour }} </li>
                <li>Tahun: {{ $vehicle->year }} </li>
                <li>Kilometer: {{ $vehicle->kilometer }} </li>
                <li>Tipe Mobil: {{ $filters['body_type'][$vehicle->body_type] }} </li>
            </ul>
            <button class="btn btn-dark w-100 mb-3" onclick="chatFunction()">Chat</button>

            <!-- Rating Form -->
            <form method="POST" action="{{ route('vehicle_detail.rating', $vehicle->id) }}">
                @csrf
                <div>
                    <div class="rating mb-3">
                        <span class="icon">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="bi {{ $i <= $userRating ? 'bi-star-fill' : 'bi-star' }}" data-value="{{ $i }}"></i>
                            @endfor
                        </span>
                    </div>
                    <input type="hidden" value="" id="inputRating" name="rate">
                    {{-- <p class="mt-2">Rating Anda: <span id="selectedRating">{{ $userRating }}</span></p> --}}
                </div>
                <textarea name="review" id="review" rows="3" class="form-control mb-3" placeholder="Tulis ulasan Anda...">{{ $userReview }}</textarea>
                <button type="submit" class="btn btn-secondary w-100" {{Auth()->check() ? '' : 'disabled'}}>Konfirmasi</button>
            </form>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="mt-5">
        <h4>Review Terbaru</h4>
        <div class="row">
            @foreach ($reviews as $review)
                <div class="col-md-4">
                    <div class="card shadow-sm p-3">
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('images/logo.jpg') }}" alt="Avatar" class="review-avatar me-3" style="width: 50px; height: 50px; border-radius: 50%;">
                            <div>
                                <h6 class="mb-0">{{ $review->user_name }}</h6>
                                <small class="text-muted">{{ date('d M Y', strtotime($review->created_at)) }}</small>
                            </div>
                        </div>
                        <div class="d-flex">
                            <span class="icon">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= floor($review->rating))
                                        <i class="bi bi-star-fill"></i>
                                    @elseif ($i == ceil($review->rating) && fmod($review->rating, 1) > 0)
                                        <i class="bi bi-star-half"></i>
                                    @else
                                        <i class="bi bi-star"></i>
                                    @endif
                                @endfor
                            </span>
                        </div>
                        <p class="mb-0">{{ $review->content }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Wishlist Button
        const wishlistButton = document.getElementById('wishlistButton');
        wishlistButton.addEventListener("click", function () {
            const icon = this.querySelector('i');
            const vehicleId = this.getAttribute('data-vehicle-id');

            // Check if the icon is active (has the 'bi-heart-fill' class)
            const isActive = icon.classList.contains('bi-heart-fill');

            if (!{{ Auth::check() ? 'true' : 'false' }}) {
                // Redirect to the login modal
                var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                loginModal.show();
                return; // Stop execution
            }

            // Toggle icon and make an AJAX request to update the wishlist
            icon.classList.toggle('bi-heart');
            icon.classList.toggle('bi-heart-fill');
            icon.classList.toggle('text-danger');

            // Simulate backend interaction (AJAX call to update wishlist)
            fetch(`/wishlist/${vehicleId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json', // Specify JSON data format
                    'X-CSRF-TOKEN': '{{ csrf_token()}}'
                },
                body: JSON.stringify({ wishlist: isActive ? 0 : 1 }) // Pass the data as a JSON string
            }).then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => console.log(data))
            .catch(err => console.error('Fetch error:', err));

        });

        // Star Rating
        const stars = document.querySelectorAll(".rating i");
        const selectedRatingElement = document.getElementById("selectedRating");
        const inputRating = document.getElementById("inputRating");
        let selectedRating = {{ $userRating ?? 0 }}; // Pre-fill with user rating if available

        stars.forEach(star => {
            star.addEventListener("click", function () {
                const ratingValue = parseInt(this.getAttribute("data-value"));
                updateStars(ratingValue);
                selectedRating = ratingValue;
                inputRating.value = ratingValue;
                selectedRatingElement.textContent = selectedRating;
            });
        });

        function updateStars(rating) {
            stars.forEach(star => {
                const starValue = parseInt(star.getAttribute("data-value"));
                if (starValue <= rating) {
                    star.classList.remove("bi-star");
                    star.classList.add("bi-star-fill");
                } else {
                    star.classList.remove("bi-star-fill");
                    star.classList.add("bi-star");
                }
            });
        }
    });

    function chatFunction() {
        if (!{{ Auth::check() ? 'true' : 'false' }}) {
            // Redirect to the login modal
            var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
            return; // Stop execution
        } else {
            window.location.href = "{{ route('chat.index') }}";
        }
    }
</script>
@endsection
