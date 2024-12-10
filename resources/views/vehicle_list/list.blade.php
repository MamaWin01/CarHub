<div class="row">
    @forelse ($vehicle as $car)
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="{{ asset('images/logo.jpg') }}" class="card-img-top" alt="Car Image">
                <div class="card-body">
                    <h5 class="card-title">{{ $car->name }}</h5>
                    <p class="card-text">
                        Tahun: {{ $car->year }} <br>
                        {{ $car->kilometer }} KM - {{ $car->type }}
                    </p>
                    <p><strong>Rp {{ number_format($car->price, 0, ',', '.') }}</strong></p>
                    <div class="d-flex">
                        <span>★★★★★</span> <!-- Replace with actual rating -->
                    </div>
                </div>
            </div>
        </div>
    @empty
        <p>No cars found.</p>
    @endforelse
</div>

<!-- Pagination -->
{{-- <div class="d-flex justify-content-center">
</div> --}}
