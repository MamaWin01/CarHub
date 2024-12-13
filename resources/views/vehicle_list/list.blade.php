<link rel="stylesheet" href="{{ asset('css/vehicle_list_model.css') }}">

<div class="row">
    @forelse ($vehicle as $car)
        <div class="col-md-4 mb-4">
            <div class="card" onclick="openModel({{ $car->id }})">
                <img src="{{ asset('images/logo.jpg') }}" class="card-img-top" alt="Car Image">
                <div class="card-body">
                    <h5 class="card-title">{{ $car->name }}</h5>
                    <p class="card-text">
                        Tahun: {{ $car->year }} <br>
                        {{ $car->kilometer }} KM - {{ $car->type }} <br>
                        @if($car->status == 0)
                            Dijual/Disewa
                        @elseif ($car->status == 1)
                            Dijual
                        @else
                            Disewa
                        @endif
                    </p>
                    <p class="card-text"><strong>Rp {{ number_format($car->price, 0, ',', '.') }}</strong></p>
                    <div class="d-flex">
                        <span class="icon">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= floor($car->total_rating))
                                    <i class="bi bi-star-fill"></i>
                                @elseif ($i == ceil($car->total_rating) && fmod($car->total_rating, 1) > 0)
                                    <i class="bi bi-star-half"></i>
                                @else
                                    <i class="bi bi-star"></i>
                                @endif
                            @endfor
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <p>No cars found.</p>
    @endforelse
</div>

<!-- Modal HTML -->
<div class="modal fade" id="vehicleDetailModal" tabindex="-1" aria-labelledby="vehicleDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close-modal-btn" onclick="closeModel()">×</button>
                <h4><b><span id="modalName"></span></b></h4>
                <p><span id="modalSeller" style="color:darkgray"></span></p>
                <img src="{{ asset('images/logo.jpg') }}" alt="Vehicle Image" class="img-fluid" id="modalImage">
                <div class="rating" style="padding-top:10px">
                    <h5>Rating</h5>
                    <div id="rating-star">
                        <!-- Stars will be dynamically populated -->
                    </div>
                </div>
                <div class="d-flex justify-content-center" style="padding-top:15px;">
                    <button type="button" style="width:150px" class="btn btn-dark mx-2" id="redirectToButton">Chat</button>
                    <button type="button" style="width:150px" class="btn btn-secondary mx-2" onclick="redirectTo({{ $car->id }})" id="detailButton">Detail</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openModel(id) {
        fetchVehicleDetails(id).then(vehicle => {
            // Update modal content
            document.getElementById('modalName').textContent = vehicle.name;
            document.getElementById('modalSeller').textContent = vehicle.owner_name;
            document.getElementById('modalImage').src = "{{ asset('images/logo.jpg') }}";

            // Display star rating based on the vehicle's rating
            const rating = vehicle.rating; // Example: 4.5
            const ratingLocation = document.getElementById('rating-star');
            ratingLocation.innerHTML = ''; // Clear existing stars

            for (let i = 1; i <= 5; i++) {
                const star = document.createElement('i');
                if (i <= Math.floor(rating)) {
                    star.className = 'bi bi-star-fill'; // Full star
                } else if (i === Math.ceil(rating) && (rating - Math.floor(rating)) >= 0.5) {
                    star.className = 'bi bi-star-half'; // Half star
                } else {
                    star.className = 'bi bi-star'; // Empty star
                }
                ratingLocation.appendChild(star);
            }

            // Update the ID of the detail button with the vehicle ID
            document.getElementById('detailButton').setAttribute('onclick', `redirectTo(${id})`);

            // Show the modal
            const modal = document.getElementById('vehicleDetailModal');
            if (modal) {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden'; // Prevent scrolling
            } else {
                console.error("Modal element not found.");
            }

            // Add modal backdrop if not already present
            let backdrop = document.querySelector('.modal-backdrop');
            if (!backdrop) {
                backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop show';
                document.body.appendChild(backdrop);
            }
        }).catch(error => {
            console.error("Error fetching vehicle details:", error);
        });
    }


    function closeModel() {
        const modal = document.getElementById('vehicleDetailModal');
        if (modal) {
            modal.classList.remove('show');
        }

        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.classList.remove('show');
            backdrop.remove(); // Remove backdrop from DOM
        }

        document.body.style.overflow = ''; // Restore scrolling
    }

    async function fetchVehicleDetails(id) {
        try {
            const response = await fetch("{{ route('vehicle_detail.show', ':id') }}?action=getModel".replace(':id', id));
            if (!response.ok) {
                throw new Error("Failed to fetch vehicle details.");
            }
            return await response.json();
        } catch (error) {
            console.error("Error fetching vehicle details:", error);
            throw error;
        }
    }

    function redirectTo(id) {
        window.location.href = "{{ route('vehicle_detail.show', ':id') }}".replace(':id', id);
    }
</script>

