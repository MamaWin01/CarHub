<link rel="stylesheet" href="{{ asset('css/vehicle_list_model.css') }}">

<div class="row">
    @forelse ($vehicle as $car)
        <div class="col-md-4 mb-4">
            <div class="card" onclick="openModel({{ @$car->id }},{{ @$car->owner_id }})">
                @php
                    $vehiclePath = 'storage/images/vehicles/' . $car->owner_id . '_' . @$car->id .'/'. $car->owner_id . '_' . @$car->id . '_1.png';
                    $defaultImage = asset('images/not_found.jpg');
                @endphp
                <img src="{{ file_exists(public_path($vehiclePath)) ? asset($vehiclePath) : $defaultImage }}" class="card-img-top" alt="Car Image">
                <div class="card-body">
                    <h5 class="card-title">{{ $car->name }}</h5>
                    <p class="card-text">
                        {{ $car->year }} -
                        {{ $car->kilometer }} KM -
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
                                @elseif ($i == ceil($car->total_rating) && fmod($car->total_rating, 1) >= 0.5)
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
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="mb-0"><b id="modalName">Nama Mobil</b></h4>
                        <p><span id="modalSeller" style="color:darkgray"></span></p>
                    </div>
                    <button type="button" class="btn-close" aria-label="Close" onclick="closeModel()"></button>
                </div>
                <div class="text-center position-relative">
                    <button class="btn btn-light position-absolute start-0 top-50 translate-middle-y" id="prevImage" onclick="changeImage(-1)">❮</button>
                    <img src="{{ asset('images/not_found.jpg') }}" alt="Vehicle Image" class="img-fluid" style="width:425px" id="modalImage">
                    <button class="btn btn-light position-absolute end-0 top-50 translate-middle-y" id="nextImage" onclick="changeImage(1)">❯</button>
                </div>
                <div class="rating" style="padding-top:10px">
                    <h5>Rating</h5>
                    <div id="rating-star">
                        <!-- Stars will be dynamically populated -->
                    </div>
                </div>
                <div class="d-flex justify-content-center" style="padding-top:15px;">
                    <button type="button" style="width:150px" class="btn btn-dark mx-2" id="chatButton" onclick="gotoChat()">Chat</button>
                    <button type="button" style="width:150px" class="btn btn-secondary mx-2" onclick="redirectTo({{ @$car->id }})" id="detailButton">Detail</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentImages = [];
    let currentIndex = 0;

    async function openModel(id, ownerId) {
        owner_id = ownerId;
        if (owner_id == {{ !Auth()->check() ? Auth()->user()->id : 0 }}) {
            document.getElementById('chatButton').disabled = true;
        } else {
            document.getElementById('chatButton').disabled = false;
        }

        try {
            const vehicle = await fetchVehicleDetails(id);
            console.log(vehicle);
            console.log(vehicle.name);
            // Update modal content
            document.getElementById('modalName').textContent = vehicle.name;
            document.getElementById('modalSeller').textContent = vehicle.owner_name;
            currentImages = vehicle.images;
            currentIndex = 0;

            if (currentImages.length > 0) {
                if(currentImages.length == 1) {
                    var nextBtn = document.getElementById('nextImage').style.display = 'none';
                    var prevBtn = document.getElementById('prevImage').style.display = 'none';
                } else {
                    var nextBtn = document.getElementById('nextImage').style.display = 'inline';
                    var prevBtn = document.getElementById('prevImage').style.display = 'inline';
                }
                document.getElementById('modalImage').src = currentImages[currentIndex];
            } else {
                var nextBtn = document.getElementById('nextImage').style.display = 'none';
                var prevBtn = document.getElementById('prevImage').style.display = 'none';
            }

            // Display star rating
            const rating = vehicle.rating;
            const ratingLocation = document.getElementById('rating-star');
            ratingLocation.innerHTML = '';
            for (let i = 1; i <= 5; i++) {
                const star = document.createElement('i');
                if (i <= Math.floor(rating)) {
                    star.className = 'bi bi-star-fill';
                } else if (i === Math.ceil(rating) && (rating - Math.floor(rating)) >= 0.5) {
                    star.className = 'bi bi-star-half';
                } else {
                    star.className = 'bi bi-star';
                }
                ratingLocation.appendChild(star);
            }

            document.getElementById('detailButton').setAttribute('onclick', `redirectTo(${id})`);

            // Show modal
            const modal = document.getElementById('vehicleDetailModal');
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
            document.body.insertAdjacentHTML('beforeend', '<div class="modal-backdrop show"></div>');

        } catch (error) {
            console.error("Error fetching vehicle details:", error);
        }
    }

    function changeImage(direction) {
        currentIndex += direction;

        if (currentIndex < 0) {
            currentIndex = currentImages.length - 1; // Go to the last image
        } else if (currentIndex >= currentImages.length) {
            currentIndex = 0; // Loop back to the first image
        }

        document.getElementById('modalImage').src = currentImages[currentIndex];
    }

    function closeModel() {
        document.getElementById('vehicleDetailModal').classList.remove('show');
        document.querySelector('.modal-backdrop').remove();
        document.body.style.overflow = '';
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

    function gotoChat() {
        if({{Auth()->check() ? 1 : 0}}) {
            var data = "action=autoload&owner_id="+owner_id;
            window.location.href = "{{ route('chat.index', ':data') }}".replace(':data', data);
        } else {
            document.getElementById('chatButton').disabled = true;
        }
    }
</script>

