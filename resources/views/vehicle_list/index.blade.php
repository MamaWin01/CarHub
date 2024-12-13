@extends('layouts.master')

@include('layouts.header')

@section('content')
<link rel="stylesheet" href="{{ asset('css/vehicle_list.css') }}">

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
                            <input class="form-check-input vehicle-status" checked type="checkbox" value="jual" id="dijual" onclick="clickFunction('jual','status',this)">
                            <label class="form-check-label" for="dijual">Dijual</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input vehicle-status" checked type="checkbox" value="sewa" id="disewa" onclick="clickFunction('sewa','status',this)">
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
                            <div class="form-check">
                                <input class="form-check-input" checked type="checkbox" value="all" id="brand-all" onclick="clickFunction('all','brand',this)">
                                <label class="form-check-label" for="brand-all">Semua</label>
                            </div>
                            @foreach($brands as $key => $brand)
                                <div class="form-check">
                                    <input class="form-check-input other-brand" type="checkbox" value="{{ $key }}" id="brand-{{ $brand }}" onclick="clickFunction('{{$key}}','brand',this)">
                                    <label class="form-check-label" for="brand-{{ $brand }}">{{ $brand }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Model -->
                    <div class="filter-group">
                        <h5>Model</h5>
                        <div class="scrollable">
                            <div class="form-check">
                                <input class="form-check-input" checked type="checkbox" value="all" id="model-all" onclick="clickFunction('all','model',this)">
                                <label class="form-check-label" for="model-all">Semua</label>
                            </div>
                            <div id="model-location">

                            </div>
                        </div>
                    </div>

                    <!-- Transmisi -->
                    <div class="filter-group">
                        <h5>Transmisi</h5>
                        @foreach ($transmision as $key => $trans)
                            <div class="form-check">
                                <input class="form-check-input" checked type="checkbox" value="{{ $key }}" id="{{ $trans }}" onclick="clickFunction('{{ $key }}','transmision',this)">
                                <label class="form-check-label" for="{{ $trans }}">{{ ucfirst($trans) }}</label>
                            </div>
                        @endforeach
                    </div>

                    <!-- Bahan Bakar -->
                    <div class="filter-group">
                        <h5>Bahan Bakar</h5>
                        @foreach ($fuel_type as $key => $fuel)
                            <div class="form-check">
                                <input class="form-check-input" checked type="checkbox" value="{{ $key }}" id="{{ $fuel }}" onclick="clickFunction('{{ $key }}','fuel',this)">
                                <label class="form-check-label" for="{{ $fuel }}">{{ ucfirst($fuel) }}</label>
                            </div>
                        @endforeach
                    </div>

                    <!-- Warna -->
                    <div class="filter-group">
                        <h5>Warna</h5>
                        <div class="scrollable">
                            <div class="form-check">
                                <input class="form-check-input" checked type="checkbox" value="all" id="colour-all" onclick="clickFunction('all','colour',this)">
                                <label class="form-check-label" for="colour-all">Semua</label>
                            </div>
                            @foreach ($colour as $key => $col)
                                <div class="form-check">
                                    <input class="form-check-input other-colour" type="checkbox" value="{{ $key }}" id="{{ $col }}" onclick="clickFunction('{{ $key }}','colour',this)">
                                    <label class="form-check-label" for="{{ $col }}">{{ ucfirst($col) }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Tahun -->
                    <div class="filter-group">
                        <h5>Tahun</h5>
                        <div style="padding-bottom:5px">
                            <select class="form-control" name="years" id="years">
                                <option value="all">Semua</option>
                                @for ($years = date('Y');$years > 2015;$years-=1)
                                    <option value="{{ $years }}">{{ $years }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="d-flex gap-2">
                            <input type="text" class="form-control year-input" placeholder="Dari" oninput="formatYear(this, 'from')">
                            <div style="padding-right:5px;padding-left:5px;font-size:18px">Sampai</div>
                            <input type="text" class="form-control year-input" placeholder="Hingga" oninput="formatYear(this, 'to')">
                        </div>
                    </div>

                    <!-- Kilometer -->
                    <div class="filter-group">
                        <h5>Kilometer</h5>
                        <div style="padding-bottom:5px">
                            <select class="form-control" name="kilometer" id="kilometer">
                                <option value="all">Semua</option>
                                <option value="100-200">100 KM - 200 KM</option>
                                <option value="200-300">200 KM - 300 KM</option>
                                <option value="300-400">300 KM - 400 KM</option>
                                <option value="400">400 KM ></option>
                            </select>
                        </div>
                        <div class="d-flex gap-2">
                            <input type="text" class="form-control year-input" placeholder="minRange" oninput="formatKilometer(this, 'minRange')">
                            <div style="padding-right:5px;padding-left:5px;font-size:18px">Sampai</div>
                            <input type="text" class="form-control year-input" placeholder="maxRange" oninput="formatKilometer(this, 'maxRange')">
                        </div>
                    </div>

                    <!-- Tipe body -->
                    <div class="filter-group">
                        <h5>Tipe mobil</h5>
                        <div class="scrollable">
                            <div class="form-check">
                                <input class="form-check-input" checked type="checkbox" value="all" id="bodyType-all" onclick="clickFunction('all','bodyType',this)">
                                <label class="form-check-label" for="bodyType-all">Semua</label>
                            </div>
                            @foreach ($body_type as $key => $type)
                                <div class="form-check">
                                    <input class="form-check-input other-bodyType" type="checkbox" value="{{ $key }}" id="{{ $type }}" onclick="clickFunction('{{ $key }}','bodyType',this)">
                                    <label class="form-check-label" for="{{ $type }}">{{ ucfirst($type) }}</label>
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
                    <input type="text" class="form-control" placeholder="Search" id="search-box">
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

            <!-- Pagination -->
            <div class="d-flex justify-content-center" id="pagination">

            </div>
        </div>
    </div>
</div>

<script>
    // Global Variables
    var condition = ['baru', 'bekas-baru', 'bekas', 'bekas-second'];
    var search = '';
    var order = 'baru';
    var price = 'all';
    var minPrice = 0;
    var maxPrice = 0;
    var status = 0;
    var brand = ['all'];
    var transmision = [0,1,2,3];
    var fuel = [0,1,2,3];
    var colour = ['all'];
    var from = 0;
    var to = 0;
    var year = 'all';
    var maxRange = 0;
    var minRange = 0;
    var kilometer = 'all';
    var bodyType = ['all'];
    var model = ['all'];
    var currentPage = 1; // Current page
    var totalPages = 1;
    let loop = 0;
    let jual = 1;
    let sewa = 1;
    let isProcessing = false; // Flag to prevent multiple clicks

    document.addEventListener('DOMContentLoaded', function () {
        renderPagination();
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
                    condition = condition.filter(item => item != tempval);
                    icon.textContent = '+'; // Change back to + when not active
                }
                getCarList();
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
                    getCarList();
                } else {
                    icon.textContent = ''; // Reset to plus
                }
            });
        });

        $('#search-box').on('input',function(e){
            if(loop == 0) {
                setTimeout(() => {
                    search = document.getElementById('search-box').value;
                    getCarList();
                }, 3000);
                console.log('a');
                loop += 1;
            } else {
                console.log('b');
                if(loop > 3) {
                    loop = 0;
                } else {
                    loop += 1;
                }
            }
        });

        document.getElementById('price').addEventListener('change', function () {
            price = this.value;
            getCarList();
        });

        document.getElementById('years').addEventListener('change', function () {
            year = this.value;
            getCarList();
        });

        document.getElementById('kilometer').addEventListener('change', function () {
            kilometer = this.value;
            getCarList();
        });

        document.getElementById('pagination').addEventListener('click', function (event) {
            const target = event.target;

            if (target.classList.contains('page-number')) {
                currentPage = parseInt(target.getAttribute('data-page'));
            } else if (target.classList.contains('prev-page') && currentPage > 1) {
                if (!isProcessing) {
                    isProcessing = true;
                    setTimeout(() => {
                        isProcessing = false;
                    }, 300); // 300 ms delay
                    currentPage--;
                    getCarList();
                }
            } else if (target.classList.contains('next-page') && currentPage < totalPages) {
                if (!isProcessing) {
                    isProcessing = true;
                    setTimeout(() => {
                        isProcessing = false;
                    }, 300); // 300 ms delay
                    currentPage++;
                    getCarList();
                }
            }
        });

        getCarList();
    });


    function formatNumber(input, name) {
        var priceInput = document.getElementById("price");
        var value = input.value.replace(/,/g, '');
        if (!isNaN(value) && value.trim() != '') {
            input.value = parseFloat(value).toLocaleString();
        } else {
            if (value > 0) {
                disableInput(priceInput, false);
                value = input.value.replace(/[^0-9\.]+/g, "");
                input.value = parseFloat(value).toLocaleString();
            } else {
                disableInput(priceInput, false);
                input.value = '';
            }
        }
        if(name == 'max') {
            maxPrice = value;
        } else {
            minPrice = value;
        }
        if((Number(maxPrice) >= Number(minPrice)) && !isNaN(value)) {
            disableInput(priceInput, true);
            getCarList();
        } else {
            disableInput(priceInput, false);
        }
    }

    function getCarList() {
        console.log("========= devider =========");
        console.log('kondisi =' + condition);
        console.log('search =' + search);
        console.log('order =' + order);
        console.log('price =' + price);
        console.log('minPrice =' + minPrice);
        console.log('maxPrice =' + maxPrice);
        console.log('status =' + status);
        console.log('brand =' + brand);
        console.log('transmision =' + transmision);
        console.log('fuel =' + fuel);
        console.log('colour =' + colour);
        console.log('year =' + year);
        console.log('from =' + from);
        console.log('to =' + to);
        console.log('kilometer =' + kilometer);
        console.log('minRange =' + minRange);
        console.log('maxRange =' + maxRange);
        console.log('bodyType =' + bodyType);
        console.log('model =' + model);
        console.log('currentPage =' + currentPage);

        $.ajax({
            url: "{{ route('vehicle_list.show', 'getVehicleList') }}",
            method: "GET",
            data: {
                condition: condition.join(),
                orderby: order,
                search: search,
                price: price,
                minPrice: minPrice,
                maxPrice: maxPrice,
                status: status,
                brand: brand,
                transmision: transmision,
                fuel: fuel,
                colour: colour,
                year: year,
                from: from,
                to: to,
                bodyType: bodyType,
                model: model,
                page: currentPage
            },
            success: function (response) {
                console.log("Response from server:"+ response);
                $('#carList').html(response.cars); // Render car list
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
                getCarList();
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
                getCarList();
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
                getCarList();
            }
        });
        paginationContainer.appendChild(nextButton);
    }

    function disableInput(input, statement) {
        input.disabled = statement;
    }

    function clickFunction(value, name, element=null) {
        if(element != null) {
            if(name == 'status') {
                if (element.checked) {
                    (value === 'jual') ? jual = 1 : sewa = 1;
                } else {
                    (value === 'sewa') ? jual = 0 : sewa = 0;
                }
                status = (jual && sewa) ? 0 : (!sewa ? 1 : 2);
                if(jual == 0 && sewa == 0) {
                    status = 0;
                }
            } else if(name == 'brand') {
                if (element.checked && value == 'all') {
                    brand = ['all'];

                    var tempval = document.getElementsByClassName("other-brand");
                    for (var i = 0; i < tempval.length; i++) {
                        tempval[i].checked = false;
                    }
                    document.getElementById('model-location').remove();
                } else {
                    document.getElementById('brand-all').checked = false;

                    if (element.checked) {
                        brand.push(value);
                    } else {
                        brand = brand.filter(item => item != value);
                    }
                    brand = brand.filter(item => item != 'all');

                    $('#model-location').load("{{ route('vehicle_list.getBrandModel', 'getBrandModel') }}",
                        "&brand="+brand
                    );
                }
            } else if(name == 'transmision') {
                if (element.checked) {
                    transmision.push(value);
                } else {
                    transmision = transmision.filter(item => item != value);
                }
            } else if(name == 'fuel') {
                if (element.checked) {
                    fuel.push(value);
                } else {
                    fuel = fuel.filter(item => item != value);
                }
            } else if(name == 'colour') {
                if (element.checked && value == 'all') {
                    colour = ['all'];

                    var tempval = document.getElementsByClassName("other-colour");
                    for (var i = 0; i < tempval.length; i++) {
                        tempval[i].checked = false;
                    }
                } else {
                    document.getElementById('colour-all').checked = false;

                    if (element.checked) {
                        colour.push(value);
                    } else {
                        colour = colour.filter(item => item != value);
                    }
                    colour = colour.filter(item => item != 'all');
                }
            } else if(name == 'bodyType') {
                if (element.checked && value == 'all') {
                    bodyType = ['all'];

                    var tempval = document.getElementsByClassName("other-bodyType");
                    for (var i = 0; i < tempval.length; i++) {
                        tempval[i].checked = false;
                    }
                } else {
                    document.getElementById('bodyType-all').checked = false;

                    if (element.checked) {
                        bodyType.push(value);
                    } else {
                        bodyType = bodyType.filter(item => item != value);
                    }
                    bodyType = bodyType.filter(item => item != 'all');
                }
            } else if(name == 'model') {
                if (element.checked && value == 'all') {
                    model = ['all'];

                    var tempval = document.getElementsByClassName("other-model");
                    for (var i = 0; i < tempval.length; i++) {
                        tempval[i].checked = false;
                    }
                } else {
                    document.getElementById('model-all').checked = false;

                    if (element.checked) {
                        model.push(value);
                    } else {
                        model = model.filter(item => item != value);
                    }
                    model = model.filter(item => item != 'all');
                }
            }
            getCarList();
        } else {
            console.log('else :)');
        }
    }

    function formatYear(input, name) {
        var yearInput = document.getElementById("years");
        var tempval = input.value.replace(/,/g, '');

        if (!isNaN(tempval) && tempval.trim() != '') {
            input.value = tempval;
        } else {
            if(tempval > 0) {
                disableInput(yearInput, false);
                tempval = input.value.replace(/[^0-9\.]+/g, "");
                input.value = tempval;
            } else {
                disableInput(yearInput, false);
                input.value = '';
            }
        }

        if (name == 'from') {
            console.log(tempval);
            from = tempval;
        } else {
            to = tempval;
        }

        if (!isNaN(to) && !isNaN(from) && (Number(to) >= Number(from))) {
            disableInput(yearInput, true);
            getCarList();
        } else {
            disableInput(yearInput, false);
        }
    }

    function formatKilometer(input, name) {
        var kilometerInput = document.getElementById("kilometer");
        var value = input.value.replace(/,/g, '');
        if (!isNaN(value) && value.trim() != '') {
            input.value = parseFloat(value).toLocaleString();
        } else {
            if (value > 0) {
                disableInput(kilometerInput, false);
                value = input.value.replace(/[^0-9\.]+/g, "");
                input.value = parseFloat(value).toLocaleString();
            } else {
                disableInput(kilometerInput, false);
                input.value = '';
            }
        }
        if(name == 'maxRange') {
            maxRange = value;
        } else {
            minRange = value;
        }
        if((Number(maxRange) >= Number(minRange)) && !isNaN(value)) {
            disableInput(kilometerInput, true);
            getCarList();
        } else {
            disableInput(kilometerInput, false);
        }
    }
</script>
@endsection
