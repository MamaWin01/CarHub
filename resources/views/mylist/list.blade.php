<link rel="stylesheet" href="{{ asset('css/vehicle_list_model.css') }}">
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    .horizontal-scroll-container {
        overflow-x: auto;
        overflow-y: hidden;
        white-space: nowrap;
        gap: 10px;
        max-width: 100%;
        padding-bottom: 10px;
        scrollbar-width: thin;
        scrollbar-color: #ccc transparent;
    }

    .horizontal-scroll-container::-webkit-scrollbar {
        height: 8px;
    }

    .horizontal-scroll-container::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .horizontal-scroll-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    .horizontal-scroll-container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .horizontal-scroll-container .img-thumbnail {
        height: 150px;
        width: auto;
        object-fit: cover;
        border-radius: 4px;
    }
</style>

<div class="row">
    @forelse ($myList as $car)
        <div class="col-md-4 mb-4">
            @php
                $vehiclePath = 'storage/images/vehicles/'. $car->owner_id . '_' . @$car->id.'/' . $car->owner_id . '_' . @$car->id.'_1' . '.png';
                $defaultImage = asset('images/not_found.jpg');
                $imageUrl = file_exists(public_path($vehiclePath)) ? asset($vehiclePath) : $defaultImage;
            @endphp
            <div class="card"
                onclick="openEditModel(this)"
                data-vehicle="{{ json_encode([
                    'id' => $car->id,
                    'name' => $car->name,
                    'condition' => $car->condition,
                    'price' => $car->price,
                    'status' => $car->status,
                    'brand' => $car->brand,
                    'model' => $car->model,
                    'transmission' => $car->transmission,
                    'fuel_type' => $car->fuel_type,
                    'colour' => $car->colour,
                    'year' => $car->year,
                    'kilometer' => $car->kilometer,
                    'body_type' => $car->body_type,
                    'image_count' => $car->img_count,
                    'no_plat' => $car->no_plat
                ]) }}">
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

<!-- Edit Vehicle Modal -->
<div class="modal fade" id="editVehicleModal" tabindex="-1" aria-labelledby="editVehicleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
            </div>
            <div class="modal-body">
                <button type="button" class="btn-close position-absolute" data-bs-dismiss="modal" aria-label="Close" style="top: 10px !important; right: 10px !important;"></button>
                <!-- Success/Error Message -->
                <div id="editMessage" class="alert d-none"></div>

                <form id="editVehicleForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <!-- Photo Upload -->
                    <div class="mb-3">
                        <label>Foto Kendaraan</label>
                        <div id="image-preview-container" class="horizontal-scroll-container">
                            <!-- Images will be displayed dynamically here -->
                        </div>
                        <input type="file" id="editPhotos" name="photos[]" class="form-control mt-2" accept="image/*" multiple>
                    </div>

                    <div class="row gx-5 align-items-start">
                        <!-- Left Side -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Nama Mobil</label>
                                <input type="text" id="editName" class="form-control" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label>Kondisi Kendaraan</label>
                                <select class="form-select" id="editCondition" name="condition">
                                    <option value="0">Baru</option>
                                    <option value="1">Bekas-Baru</option>
                                    <option value="2">Bekas</option>
                                    <option value="3">Bekas-Second</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Status</label>
                                <div class="d-flex" style="margin-bottom: 21.3px">
                                    <label class="me-3">
                                        <input type="radio" id="statusDijual" name="status" value="1"> Dijual
                                    </label>
                                    <label>
                                        <input type="radio" id="statusDisewa" name="status" value="2"> Disewa
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Harga</label>
                                <input type="text" class="form-control" id="editPrice" name="price" required>
                            </div>
                            <div class="mb-3">
                                <label>Tahun</label>
                                <select class="form-select" id="editYear" name="year">
                                    @for ($years = date('Y'); $years > 2015; $years--)
                                        <option value="{{ $years }}">{{ $years }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Kilometer</label>
                                <input type="text" class="form-control" id="editKilometer" name="kilometer" required>
                            </div>
                            <div class="mb-3">
                                <label>No. Plat</label>
                                <input type="text" placeholder="" class="form-control" id="noPlat" name="no_plat" disabled>
                            </div>
                        </div>

                        <!-- Right Side -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Merek</label>
                                <select class="form-select" id="editBrand" name="brand">
                                    @foreach ($filters['brands'] as $brand)
                                        <option value="{{ $brand }}">{{ $brand }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Model</label>
                                <select class="form-select" id="editModel" name="model">
                                    <option value="">Pilih Model</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Warna</label>
                                <select class="form-select" id="editColour" name="colour">
                                    @foreach ($filters['colour'] as $colour)
                                        <option value="{{ $colour }}">{{ $colour }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Transmisi</label>
                                <select class="form-select" id="editTransmission" name="transmission">
                                    @foreach ($filters['transmission'] as $trans)
                                        <option value="{{ $trans }}">{{ ucfirst($trans) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Bahan Bakar</label>
                                <select class="form-select" id="editFuel" name="fuel">
                                    @foreach ($filters['fuel_type'] as $key => $fuel)
                                        <option value="{{ $key }}">{{ ucfirst($fuel) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Tipe Mobil</label>
                                <select class="form-select" id="editBodyType" name="body_type">
                                    @foreach ($filters['body_type'] as $key => $bodyType)
                                        <option value="{{ $key }}">{{ ucfirst($bodyType) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <button type="submit" class="btn btn-dark flex-fill me-2" id="update-btn">Simpan Perubahan</button>
                        <div id="loading-spinner" class="spinner-border text-primary ms-3" role="status" style="display: none;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <button type="button" class="btn btn-danger flex-fill" id="deleteVehicleButton">Hapus Kendaraan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus kendaraan ini?</p>
                <p class="text-danger">Data yang dihapus tidak dapat dikembalikan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Hapus</button>
            </div>
        </div>
    </div>
</div>


<script>
    var editModal = new bootstrap.Modal(document.getElementById('editVehicleModal'));
    var deleteConfirmModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    var editVehicleForm = document.getElementById('editVehicleForm');
    var editMessage = document.getElementById('editMessage');
    var editUploadedImages = []; // Store new images

    // Function to Open Edit Modal
    window.openEditModel = function (element) {
        try {
            const vehicleData = JSON.parse(element.getAttribute('data-vehicle'));

            // Populate the form fields with vehicle data
            editVehicleForm.setAttribute('action', `/vehicle/update/${vehicleData.id}`);
            document.getElementById('editName').value = vehicleData.name;
            document.getElementById('editCondition').value = vehicleData.condition;
            document.getElementById('editPrice').value = Number(vehicleData.price).toLocaleString("id-ID");;
            document.getElementById('editYear').value = vehicleData.year;
            document.getElementById('editKilometer').value = vehicleData.kilometer;

            document.getElementById('editBrand').value = vehicleData.brand;
            document.getElementById('editModel').value = vehicleData.model;
            document.getElementById('editColour').value = vehicleData.colour;
            document.getElementById('editTransmission').value = vehicleData.transmission;
            document.getElementById('editFuel').value = vehicleData.fuel_type;
            document.getElementById('editBodyType').value = vehicleData.body_type;
            document.getElementById('noPlat').value = vehicleData.no_plat;

            // Update radio buttons
            document.getElementById('statusDijual').checked = vehicleData.status == 1;
            document.getElementById('statusDisewa').checked = vehicleData.status == 2;

            // Update image preview
            var tempImage = [];
            var tempId = "{{Auth()->user()->id}}";
            for(let i = 0;i < vehicleData.image_count; i++) {
                tempImage.push(`/storage/images/vehicles/${tempId}_${vehicleData.id}/${tempId}_${vehicleData.id}_${i+1}.png`)
            }
            loadExistingImages(tempImage)

            // Show the modal
            editModal.show();
            selectBrand(vehicleData.brand, vehicleData.model);
        } catch (error) {
            console.error('Error opening edit modal:', error);
        }
    };

    var updateButton = document.getElementById('update-btn');
    var loadingSpinner = document.getElementById('loading-spinner');
    // Submit Edit Form via AJAX
    editVehicleForm.addEventListener('submit', function (e) {
        e.preventDefault();

        updateButton.disabled = true;
        loadingSpinner.style.display = 'inline-block';

        const formData = new FormData(this);

        editUploadedImages.forEach((image, index) => {
            formData.append(`photos[${index}]`, image);
        });

        existingImages.forEach((image, index) => {
            formData.append(`existing_images[${index}]`, image);
        });

        fetch(editVehicleForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                editMessage.className = 'alert alert-success';
                editMessage.textContent = 'Perubahan berhasil disimpan!';
                editMessage.classList.remove('d-none');

                setTimeout(() => {
                    editModal.hide();
                    location.reload(); // Reload the page to show updated data
                }, 1500);
            } else {
                editMessage.className = 'alert alert-danger';
                editMessage.textContent = data.message || 'Gagal menyimpan perubahan.';
                editMessage.classList.remove('d-none');
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // Delete Confirmation Button
    document.getElementById('deleteVehicleButton').addEventListener('click', function () {
        deleteConfirmModal.show();
    });

    document.getElementById('confirmDeleteButton').addEventListener('click', function () {
        const deleteUrl = editVehicleForm.getAttribute('action').replace('/update/', '/delete/');
        fetch(deleteUrl, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            window.location.reload();
        })
        .catch(error => console.error('Error:', error))
        .finally(() => deleteConfirmModal.hide());
    });

    var EditpriceInput = document.getElementById("editPrice");

    EditpriceInput.addEventListener("input", function () {
        // Remove any non-digit characters (e.g., commas)
        let value = this.value.replace(/[^\d]/g, "");

        // Format with thousands separator
        value = Number(value).toLocaleString("id-ID"); // "id-ID" for Indonesian format

        // Set the formatted value back to the input
        this.value = value;
    });

    var EditkilometerInput = document.getElementById("editKilometer");

    EditkilometerInput.addEventListener("input", function () {
        let value = this.value.replace(/[^\d]/g, "");

        // Set the formatted value back to the input
        this.value = value;
    });

    var EditbrandSelect = document.getElementById('editBrand');
    // Dynamic Model Options
    var carModels = @json($filters['model']); // Fetch models from $filters

    EditbrandSelect.addEventListener('change', function () {
        selectBrand(this.value)
    });

    function selectBrand(brand,Selectmodel=null)
    {
        const EditmodelSelect = document.getElementById('editModel');

        EditmodelSelect.innerHTML = ''; // Clear options
        const EditselectedBrand = brand;
        // Add models for the selected brand
        if (carModels[EditselectedBrand]) {
            carModels[EditselectedBrand].forEach(model => {
                const option = document.createElement('option');
                option.value = model;
                option.textContent = model;
                if (Selectmodel && model === Selectmodel) {
                    option.selected = true;
                }
                EditmodelSelect.appendChild(option);
            });
        }
    }

    var editImageInput = document.getElementById('editPhotos');
    var editPreviewContainer = document.getElementById('image-preview-container');

    // Fetch existing images when modal opens
    function loadExistingImages(images) {
        editPreviewContainer.innerHTML = ''; // Clear previous previews
        existingImages = images;

        images.forEach((url, index) => {
            const imgContainer = document.createElement('div');
            imgContainer.classList.add('position-relative');
            imgContainer.style.display = 'inline-block';

            imgContainer.innerHTML = `
                <img src="${url}" class="img-thumbnail" style="max-width: 100%; height: 150px; object-fit: cover;">
                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-image-btn" data-index="${index}">
                    <i class="bi bi-x"></i></button>
            `;

            editPreviewContainer.appendChild(imgContainer);

            // Handle Remove Existing Image
            imgContainer.querySelector('.remove-image-btn').addEventListener('click', () => {
                existingImages.splice(index, 1);
                imgContainer.remove();
                console.log('remove');
                console.log(editUploadedImages);
            });
        });
    }

    // Handle New Image Uploads
    editImageInput.addEventListener('change', function () {
        const files = Array.from(this.files);

        files.forEach(file => {
            if (file) {
                const reader = new FileReader();

                reader.onload = (e) => {
                    const imgContainer = document.createElement('div');
                    imgContainer.classList.add('position-relative');
                    imgContainer.style.display = 'inline-block';

                    imgContainer.innerHTML = `
                        <img src="${e.target.result}" class="img-thumbnail" style="max-width: 100%; height: 150px; object-fit: cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-image-btn" onclick="editImageHandle('${file.name}')">
                        <i class="bi bi-x"></i></button>
                    `;

                    editPreviewContainer.appendChild(imgContainer);

                    editUploadedImages.push(file);

                    // Handle Remove New Image
                    imgContainer.querySelector('.remove-image-btn').addEventListener('click', () => {
                        imgContainer.remove();
                        editUploadedImages = editUploadedImages.filter(img => img !== file);
                        console.log('remove');
                        console.log(editUploadedImages);
                    });
                };

                reader.readAsDataURL(file);
            }
        });
    });

    function editImageHandle(file) {
        editUploadedImages = editUploadedImages.filter(img => img.name != file);
    }

    editImageInput.value = '';
</script>

