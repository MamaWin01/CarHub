@extends('layouts.master')

@include('layouts.header')

@section('content')
<style>
.filter-container {
    padding: 1rem;
    background-color: #f8f9fa;
    border-radius: 8px;
    max-width: 320px;
}

.filter-group {
    margin-bottom: 1.5rem;
}

.scrollable {
    max-height: 150px;
    overflow-y: auto;
    border: 1px solid #ddd;
    padding: 5px;
    border-radius: 4px;
}

/* Button kondisi kendaraan */
/* Basic styles for buttons */
#filter-buttons .btn-filter {
    background-color: #f8f8f8;
    border: 1px solid #ccc;
    padding: 5px 10px;
    margin: 2px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

/* Active button style */
#filter-buttons .btn-filter.active {
    background-color: #000000;
    color: white;
    border-color: white;
}

/* Icon style */
#filter-buttons .icon {
    font-weight: bold;
}

#filter-buttons .btn-filter:hover {
    background-color: #000000;
    color: #fff;
}

/* Change the default accent color for checkboxes */
input[type="checkbox"]:checked {
    accent-color: black;
}

/* Optional: Style the checkbox container */
input[type="checkbox"] {
    width: 16px;
    height: 16px;
    cursor: pointer;
}

/* order button */
#order-buttons .btn-order {
    background-color: #f8f8f8;
    border: 1px solid #ccc;
    padding: 5px 10px;
    margin: 2px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

/* Active button style */
#order-buttons .btn-order.active {
    background-color: #000000;
    color: white;
    border-color: white;
}

#order-buttons .icon {
    font-weight: bold;
}

#order-buttons .btn-order:hover {
    background-color: #000000;
    color: #fff;
}

/* Search input styles */
.search-icon {
    position: absolute;
    top: 2.1%;
    left: 21%; /* Adjust left position as needed */
    transform: translateY(-50%);
    color: #999; /* Adjust color as needed */
    pointer-events: none;
}

.search-input {
    display: flex;
    align-items: center;
}

</style>
<div class="container" style="padding-top:20px">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-md-4">
            <form>
                <div class="filter-container">
                    <!-- Kondisi Kendaraan -->
                    <div class="filter-group">
                        <h5>Kondisi Kendaraan</h5>
                        <div class="d-flex flex-wrap">
                            <div id="filter-buttons">
                                <button class="btn-filter active" type="button" data-value="baru">
                                    Baru <span class="icon">+</span>
                                </button>
                                <button class="btn-filter active" type="button" data-value="bekas-baru">
                                    Bekas-Baru <span class="icon">+</span>
                                </button>
                                <button class="btn-filter active" type="button" data-value="bekas">
                                    Bekas <span class="icon">+</span>
                                </button>
                                <button class="btn-filter active" type="button" data-value="bekas-second">
                                    Bekas-Second <span class="icon">+</span>
                                </button>
                            </div>
                        </div>
                        <div class="form-check mt-3">
                            <input class="form-check-input vehicle-status" type="checkbox" value="dijual" id="dijual">
                            <label class="form-check-label" for="dijual">Dijual</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input vehicle-status" type="checkbox" value="disewa" id="disewa">
                            <label class="form-check-label" for="disewa">Disewa</label>
                        </div>
                    </div>

                    <!-- Harga -->
                    <div class="filter-group">
                        <h5>Harga</h5>
                        <div style="padding-bottom:5px">
                            <select class="form-control" name="price" id="price">
                                <option value="all">Semua</option>
                                <option value="100-200">100 Juta - 200 Juta</option>
                                <option value="200-300">200 Juta - 300 Juta</option>
                                <option value="300-400">300 Juta - 400 Juta</option>
                                <option value="400">400 Juta ></option>
                            </select>
                        </div>
                        <div class="d-flex gap-2">
                            <input type="text" class="form-control price-input" placeholder="Min" oninput="formatNumber(this, 'min')">
                            <div style="padding-right:5px;padding-left:5px;font-size:18px">Sampai</div>
                            <input type="text" class="form-control price-input" placeholder="Maks" oninput="formatNumber(this, 'max')">
                        </div>
                    </div>

                    <!-- Merek -->
                    <div class="filter-group">
                        <h5>Merek</h5>
                        <div class="scrollable">
                            @foreach(['Semua', 'Audi', 'BMW', 'Chevy', 'Chevrolet', 'Toyota', 'Honda'] as $brand)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="{{ $brand }}" id="brand-{{ $brand }}">
                                    <label class="form-check-label" for="brand-{{ $brand }}">{{ $brand }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Transmisi -->
                    <div class="filter-group">
                        <h5>Transmisi</h5>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="manual" id="manual">
                            <label class="form-check-label" for="manual">Manual</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="auto" id="auto">
                            <label class="form-check-label" for="auto">Auto</label>
                        </div>
                    </div>

                    <!-- Bahan Bakar -->
                    <div class="filter-group">
                        <h5>Bahan Bakar</h5>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="diesel" id="diesel">
                            <label class="form-check-label" for="diesel">Diesel</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="bensin" id="bensin">
                            <label class="form-check-label" for="bensin">Bensin</label>
                        </div>
                    </div>

                    <!-- Warna -->
                    <div class="filter-group">
                        <h5>Warna</h5>
                        <div class="scrollable">
                            @foreach(['Semua', 'Putih', 'Hitam', 'Perak', 'Abu-abu'] as $color)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="{{ $color }}" id="color-{{ $color }}">
                                    <label class="form-check-label" for="color-{{ $color }}">{{ $color }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Cars List -->
        <div class="col-md-8">
            <div class="d-flex justify-content-between mb-3">
                <div class="search-input">
                    <input type="text" class="form-control" placeholder="Search">
                    <i class="bi bi-search search-icon"></i>
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
                </div>
            </div>

            <div id="carList">

            </div>
        </div>
    </div>
</div>

<script>
    var condition = ['baru', 'bekas-baru', 'bekas', 'bekas-second'];
    var order = '';
    var price = '';
    var minPrice = 0;
    var maxPrice = 0;
    document.addEventListener('DOMContentLoaded', function () {
        var condition = ['baru', 'bekas-baru', 'bekas', 'bekas-second'];
        var order = '';

        const Filterbuttons = document.querySelectorAll('.btn-filter');

        Filterbuttons.forEach(button => {
            button.addEventListener('click', () => {
                // Toggle active class
                button.classList.toggle('active');
                var tempval = button.getAttribute('data-value');

                // Change icon based on active state
                const icon = button.querySelector('.icon');
                if (button.classList.contains('active')) {
                    condition.push(tempval);
                    icon.textContent = '×'; // Change to X when active
                } else {
                    condition = condition.filter(item => item !== tempval);
                    icon.textContent = '+'; // Change back to + when not active
                }
            });
        });

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
                } else {
                    icon.textContent = ''; // Reset to plus
                }
            });
        });

        getCarList();
    });

    document.getElementById('price').addEventListener('change', function () {
        price = this.value;
        getCarList();
    });

    function formatNumber(input, name) {
        var priceInput = document.getElementById("price");
        var value = input.value.replace(/,/g, '');
        if (!isNaN(value) && value.trim() !== '') {
            input.value = parseFloat(value).toLocaleString();
        } else {
            if(value = 0) {
                disableInput(priceInput, false);
                input.value = '';
            } else {
                disableInput(priceInput, false);
                value = value.replace(/[^0-9\.]+/g, "");
                input.value = parseFloat(value).toLocaleString();
            }
        }
        if(name == 'max') {
            maxPrice = value;
        } else {
            minPrice = value;
        }
        if(minPrice > 0 && maxPrice > 0 && maxPrice >= minPrice) {
            disableInput(priceInput, true);
            getCarList();
        } else {
            disableInput(priceInput, false);
        }
    }

    function getCarList() {
        $('#carList').load("{{ route('vehicle_list.show', 'getVehicleList') }}", '&condition='+condition.join()+"&orderby="+order);
    }

    function disableInput(input, statement) {
        input.disabled = statement;
    }

</script>
@endsection
