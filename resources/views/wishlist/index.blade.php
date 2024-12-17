@extends('layouts.master')

@include('layouts.header')

@section('content')
<link rel="stylesheet" href="{{ asset('css/vehicle_list.css') }}">

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
    var search = '';
    var order = 'baru';
    let isProcessing = false;
    var currentPage = 1;
    var totalPages = 1;
    let loop = 0;

    document.addEventListener('DOMContentLoaded', function () {
        renderPagination();
        getWishlist();

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
                    getWishlist();
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
                    getWishlist();
                }, 1000);
            } else {
                loop += 1;
                getWishlist();
            }
        });
    });

    function getWishlist() {
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
            url: "{{ route('wishlist.getlist', 'getWishlistData') }}",
            method: "GET",
            data: {
                action: 'getWishlistData',
                orderby: order,
                search: search,
                page: currentPage
            },
            success: function (response) {
                console.log("Response from server:"+ response);
                $('#carList').html(response.wishlist); // Render car list
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
                getWishlist();
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
                getWishlist();
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
                getWishlist();
            }
        });
        paginationContainer.appendChild(nextButton);
    }

</script>

@endsection
