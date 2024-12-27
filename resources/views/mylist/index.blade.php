@extends('layouts.master')

@include('layouts.header')

@section('content')
<link rel="stylesheet" href="{{ asset('css/vehicle_list.css') }}">
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

<div class="container" style="padding-top:20px">
    <div class="row">
        <!-- Cars List -->
        <div class="col-md-12">
            <div class="d-flex justify-content-between mb-3">
                <div class="search-input col-md-4">
                    <input type="text" class="form-control" placeholder="Search" id="search-box">
                    <i class="bi bi-search search-icon" style="top:47%;left:90%"></i>
                </div>
                <div id="order-buttons">
                    <button class="btn-order active" type="button" data-value="baru">
                        Baru <span class="icon"><i class="bi bi-check-lg"></i></span>
                    </button>
                    <button class="btn-order" type="button" data-value="harga-termahal">
                        Harga Termahal <span class="icon"><i class="bi bi-caret-up-fill"></i></span>
                    </button>
                    <button class="btn-order" type="button" data-value="harga-termurah">
                        Harga Termurah <span class="icon"><i class="bi bi-caret-down-fill"></i></span>
                    </button>
                    <button class="btn-order" type="button" data-value="rating">
                        Rating <span class="icon"><i class="bi bi-star-fill"></i></span>
                    </button>
                    <button class="btn-order" type="button" data-value="tambah">
                        Tambah <span class="icon"><i class="bi bi-plus-circle"></i></span>
                    </button>
                </div>
            </div>

            <div id="carList">

            </div>

            <div class="d-flex justify-content-center" id="pagination">

            </div>
        </div>
    </div>
</div>

<!-- Add Vehicle Modal -->
<div class="modal fade" id="addVehicleModal" tabindex="-1" aria-labelledby="addVehicleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
            </div>
            <div class="modal-body">
                <!-- Error Alert -->
                <div class="alert alert-danger alert-dismissible fade show d-none" role="alert" id="errorAlert">
                    An error occurred. Please try again.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <button type="button" class="btn-close position-absolute" data-bs-dismiss="modal" aria-label="Close" style="top: 10px !important; right: 10px !important;"></button>

                <form id="addVehicleForm" method="POST" action="{{ route('mylist.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Image Upload at Top -->
                    <div class="mb-3">
                        <label>Foto Kendaraan</label>
                        <input type="file" id="photosInput" name="photos[]" class="form-control" accept="image/*" multiple>
                        <div id="image-preview-container" class="horizontal-scroll-container">
                            <!-- Images will be displayed dynamically here -->
                        </div>
                    </div>

                    <!-- Form Split into Two Sides -->
                    <div class="row gx-5 align-items-start">
                        <!-- Left Side -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Nama Mobil</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label>Kondisi Kendaraan</label>
                                <select class="form-select" name="condition">
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
                                        <input type="radio" name="status" value="1" checked> Dijual
                                    </label>
                                    <label>
                                        <input type="radio" name="status" value="2"> Disewa
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Harga</label>
                                <input type="text" placeholder="Rp. xxx,xxx,xxx" class="form-control" id="price-input" name="price" required>
                            </div>
                            <div class="mb-3">
                                <label>Tahun</label>
                                <select class="form-select" name="year">
                                    @for ($years = date('Y'); $years > 2015; $years--)
                                        <option value="{{ $years }}">{{ $years }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Kilometer</label>
                                <input type="text" class="form-control" id="kilometer-input" name="kilometer" required>
                            </div>
                            <div class="mb-3">
                                <label>No. Plat</label>
                                <input type="text" placeholder="BP1234XX" class="form-control" id="noPlat-input" name="no_plat" required>
                            </div>
                        </div>

                        <!-- Right Side -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Merek</label>
                                <select class="form-select" id="brand" name="brand">
                                    <option value="">Pilih Merek</option>
                                    @foreach ($filters['brands'] as $brand)
                                        <option value="{{ $brand }}">{{ $brand }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Model</label>
                                <select class="form-select" id="model" name="model">
                                    <option value="">Pilih Model</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Warna</label>
                                <select class="form-select" name="colour">
                                    @foreach ($filters['colour'] as $colour)
                                        <option value="{{ $colour }}">{{ $colour }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Transmisi</label>
                                <select class="form-select" name="transmission">
                                    @foreach ($filters['transmission'] as $trans)
                                        <option value="{{ $trans }}">{{ ucfirst($trans) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Bahan Bakar</label>
                                <select class="form-select" name="fuel">
                                    @foreach ($filters['fuel_type'] as $key => $fuel)
                                        <option value="{{ $key }}">{{ ucfirst($fuel) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Tipe Mobil</label>
                                <select class="form-select" name="body_type">
                                    @foreach ($filters['body_type'] as $key => $bodyType)
                                        <option value="{{ $key }}">{{ ucfirst($bodyType) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>No. Rangka</label>
                                <input type="text" placeholder="" class="form-control" id="noRangka-input" name="no_rangka" max="5" required>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-center mt-3">
                        <button type="submit" class="btn btn-dark w-100" id="submit-btn">
                            Konfirmasi
                        </button>
                        <div id="loading-spinner-update" class="spinner-border text-primary ms-3" role="status" style="display: none;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    var search = '';
    var order = 'baru';
    let isProcessing = false;
    var currentPage = 1;
    var totalPages = 1;
    let loop = 0;

    document.addEventListener('DOMContentLoaded', function () {
        renderPagination();
        getMyList();

        const Orderbuttons = document.querySelectorAll('.btn-order');

        Orderbuttons.forEach(button => {
            button.addEventListener('click', () => {
                // Only allow one active order button at a time
                Orderbuttons.forEach(b => b.classList.remove('active'));
                button.classList.add('active');

                var tempval = button.getAttribute('data-value');

                // Change icon based on active state
                const icon = button.querySelector('.icon');
                if (button.classList.contains('active')) {
                    order = tempval;
                    getMyList();
                } else {
                    icon.textContent = ''; // Reset to plus
                }
            });
        });

        $('#search-box').on('input',function(e){
            if(loop > 1) {
                if(loop > 3) {
                    loop = 0;
                } else {
                    loop += 1;
                }
                setTimeout(() => {
                    search = document.getElementById('search-box').value;
                    getMyList();
                }, 1000);
            } else {
                loop += 1;
                getMyList();
            }
        });

        const photoPreview = document.getElementById('photoPreview');
        const brandSelect = document.getElementById('brand');
        const modelSelect = document.getElementById('model');

        // Dynamic Model Options
        const carModels = @json($filters['model']); // Fetch models from $filters

        brandSelect.addEventListener('change', function () {
            modelSelect.innerHTML = '<option value="">Pilih Model</option>'; // Clear options
            const selectedBrand = this.value;
            // Add models for the selected brand
            if (carModels[selectedBrand]) {
                carModels[selectedBrand].forEach(model => {
                    const option = document.createElement('option');
                    option.value = model;
                    option.textContent = model;
                    modelSelect.appendChild(option);
                });
            }
        });

        const priceInput = document.getElementById("price-input");

        priceInput.addEventListener("input", function () {
            // Remove any non-digit characters (e.g., commas)
            let value = this.value.replace(/[^\d]/g, "");

            // Format with thousands separator
            value = Number(value).toLocaleString("id-ID"); // "id-ID" for Indonesian format

            // Set the formatted value back to the input
            this.value = value;
        });

        const kilometerInput = document.getElementById("kilometer-input");

        kilometerInput.addEventListener("input", function () {
            let value = this.value.replace(/[^\d]/g, "");

            // Set the formatted value back to the input
            this.value = value;
        });
    });

    function getMyList() {
        document.getElementById('carList').innerHTML = `
            <div id="skeleton-loader">
                @for ($i=0;$i<9;$i++)
                    <div class="skeleton-card">
                        <div class="skeleton-image"></div>
                        <div class="skeleton-text skeleton-title"></div>
                        <div class="skeleton-text skeleton-subtitle"></div>
                        <div class="skeleton-text skeleton-price"></div>
                    </div>
                @endfor
            </div>
        `;

        $.ajax({
            url: "{{ route('mylist.index', 'getMylist') }}",
            method: "GET",
            data: {
                action: 'getMylist',
                orderby: order,
                search: search,
                page: currentPage
            },
            success: function (response) {
                console.log("Response from server:"+ response);
                $('#carList').html(response.myList); // Render car list
                totalPages = response.totalPages; // Update total pages
                renderPagination();
            },
            error: function (xhr, status, error) {
                console.error('Failed to fetch car list', xhr.responseText);
            }
        });
    }

    function renderPagination() {
        const paginationContainer = document.getElementById('pagination');
        paginationContainer.innerHTML = ''; // Clear existing pagination

        // Create the "Previous" button with a larger left arrow
        const prevButton = document.createElement('button');
        prevButton.classList.add('pagination-btn', 'prev-page');
        prevButton.innerHTML = '<i class="bi bi-arrow-left"></i> Previous'; // Larger left arrow
        prevButton.disabled = currentPage === 1; // Disable if on the first page
        prevButton.addEventListener('click', () => {
            if (currentPage > 1 && !isProcessing) {
                isProcessing = true;
                setTimeout(() => {
                    isProcessing = false;
                }, 300); // 300 ms delay
                currentPage--;
                getMyList();
            }
        });
        paginationContainer.appendChild(prevButton);

        // Determine the range of page numbers to display
        const pagesToShow = 5; // Number of page numbers to show at once
        let startPage = Math.max(currentPage - Math.floor(pagesToShow / 2), 1);
        let endPage = Math.min(startPage + pagesToShow - 1, totalPages);

        // Adjust the range if it goes beyond the total pages
        if (endPage - startPage + 1 < pagesToShow && startPage > 1) {
            startPage = Math.max(endPage - pagesToShow + 1, 1);
        }

        // Add page buttons
        for (let i = startPage; i <= endPage; i++) {
            const pageButton = document.createElement('button');
            pageButton.classList.add('pagination-btn', 'page-number');
            if (i === currentPage) {
                pageButton.classList.add('active'); // Highlight current page
            }
            pageButton.textContent = i;
            pageButton.setAttribute('data-page', i);
            pageButton.addEventListener('click', () => {
                currentPage = i;
                getMyList();
            });
            paginationContainer.appendChild(pageButton);
        }

        // Create the "Next" button
        const nextButton = document.createElement('button');
        nextButton.classList.add('pagination-btn', 'next-page');
        nextButton.innerHTML = 'Next <i class="bi bi-arrow-right"></i>'; // Smaller right arrow
        nextButton.disabled = currentPage === totalPages; // Disable if on the last page
        nextButton.addEventListener('click', () => {
            if (currentPage < totalPages && !isProcessing) {
                isProcessing = true;
                setTimeout(() => {
                    isProcessing = false;
                }, 300); // 300 ms delay
                currentPage++;
                getMyList();
            }
        });
        paginationContainer.appendChild(nextButton);
    }

    document.querySelector('[data-value="tambah"]').addEventListener('click', function () {
        var addVehicleModal = new bootstrap.Modal(document.getElementById('addVehicleModal'));
        addVehicleModal.show();
    });

    @if (session('store-vehicle-error'))
        var addVehicleModal = new bootstrap.Modal(document.getElementById('addVehicleModal'));
        addVehicleModal.show();
        setTimeout(function () {
            const successAlert = document.getElementById('successAlert');
            const errorAlert = document.getElementById('errorAlert');

            if (successAlert) {
                let alertInstance = bootstrap.Alert.getOrCreateInstance(successAlert);
                alertInstance.close();
            }

            if (errorAlert) {
                let alertInstance = bootstrap.Alert.getOrCreateInstance(errorAlert);
                alertInstance.close();
            }
        }, 5000);
    @endif

    const imageInput = document.getElementById('photosInput');
    const previewContainer = document.getElementById('image-preview-container');

    let uploadedImages = []; // Array to store image data

    // Event Listener for Image Input Change
    imageInput.addEventListener('change', function () {
        const files = Array.from(this.files);

        files.forEach(file => {
            if (file) {
                const reader = new FileReader();

                reader.onload = (e) => {
                    const imgContainer = document.createElement('div');
                    imgContainer.classList.add('position-relative');
                    imgContainer.style.display = 'inline-block';

                    imgContainer.innerHTML = `
                        <img src="${e.target.result}" class="img-thumbnail" style="max-width: 100%; height: 160px; object-fit: cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-image-btn" onclick="ImageHandle('${file.name}')"
                        ><i class="bi bi-x"></i></button>
                    `;

                    // Add to preview container
                    previewContainer.appendChild(imgContainer);

                    // Store the image in the array
                    uploadedImages.push(file);

                    // Handle Remove Image
                    imgContainer.querySelector('.remove-image-btn').addEventListener('click', () => {
                        imgContainer.remove();
                        uploadedImages = uploadedImages.filter(img => img !== file);
                    });
                };

                reader.readAsDataURL(file);
            }
        });
    });

    function ImageHandle(file) {
        uploadedImages = uploadedImages.filter(img => img.name != file);
    }

    // Select the form, submit button, and spinner
    const addVehicleForm = document.querySelector('form[action="{{ route('mylist.store') }}"]');
    const submitButton = document.getElementById('submit-btn');
    var loadingSpinnerUpdate = document.getElementById('loading-spinner-update');

    // Handle Form Submission
    addVehicleForm.addEventListener('submit', function (e) {
        // Prevent default form submission
        e.preventDefault();

        // Show loading spinner and disable submit button
        submitButton.disabled = true;
        loadingSpinnerUpdate.style.display = 'inline-block';

        // Prepare form data
        let formData = new FormData(this);
        const newFormData = new FormData();
        for (const [key, value] of formData.entries()) {
            // Skip keys starting with 'photos['
            if (!key.startsWith('photos[')) {
                newFormData.append(key, value);
            }
        }
        formData = newFormData;

        uploadedImages.forEach((image, index) => {
            formData.append(`photos[${index}]`, image);
        });

        // Send AJAX request
        fetch('{{ route('mylist.store') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to submit form');
            }
            return response.json();
        })
        .then(data => {
            console.log(data);
            if(data.success) {
                location.reload();
            } else {
                const errorAlert = document.getElementById('errorAlert');
                if (errorAlert) {
                    errorAlert.textContent = data.message || 'Failed to submit the form. Please try again.';
                    errorAlert.classList.remove('d-none');
                }

                // Ensure modal remains open
                var addVehicleModal = new bootstrap.Modal(document.getElementById('addVehicleModal'));
                addVehicleModal.show();
                loadingSpinnerUpdate.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(`Error: ${error.message}`);
        })
        .finally(() => {
            submitButton.disabled = false;
            loadingSpinnerUpdate.style.display = 'none';
        });
    });
</script>

@endsection
