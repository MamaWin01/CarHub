@extends('layouts.master')

@include('layouts.header')

@section('content')
<style>
    .product-container {
        display: flex;
        flex-direction: row;
        gap: 2rem;
        padding: 2rem;
    }
    .product-image {
        width: 300px;
        height: 300px;
        background-color: #f3f3f3;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #ddd;
        border-radius: 8px;
    }
    .favorite-icon {
        position: absolute;
        top: 10px;
        left: 10px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 50%;
        padding: 5px;
        cursor: pointer;
    }
    .product-details {
        flex: 1;
    }
    .price {
        font-size: 24px;
        font-weight: bold;
        color: #000;
    }
    .chat-button {
        display: block;
        width: 100%;
        background-color: #000;
        color: #fff;
        border: none;
        padding: 10px;
        border-radius: 4px;
        margin-top: 20px;
        cursor: pointer;
    }
    .review-section {
        margin-top: 20px;
    }
    .review-form {
        margin-top: 20px;
        padding: 1rem;
        border: 1px solid #ddd;
        border-radius: 8px;
    }
    .review-list {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-top: 20px;
    }
    .review-item {
        flex: 1 1 calc(33.33% - 1rem);
        padding: 1rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        background-color: #f9f9f9;
    }
    .review-body {
        margin: 10px 0;
    }
</style>

<div class="container">
    <div class="product-container">
        <!-- Image Section -->
        <div style="position: relative;">
            <div class="product-image">
                <span>📷</span> <!-- Placeholder for image -->
            </div>
            <div class="favorite-icon">♥</div>
        </div>

        <!-- Details Section -->
        <div class="product-details">
            <h2>Nama</h2>
            <p class="price">Rp. xxx,xxx,xxx.xx</p>
            <p>Informasi Kendaraan</p>
            <button class="chat-button">Chat</button>

            <!-- Review Form -->
            <div class="review-form">
                <form>
                    <div class="form-group">
                        <label for="rating">Rating:</label>
                        <div>
                            <span>⭐⭐⭐⭐⭐</span> <!-- Replace with clickable stars in future -->
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <textarea class="form-control" name="review" rows="3" placeholder="Value"></textarea>
                    </div>
                    <button type="submit" class="btn btn-secondary mt-3">Konfirmasi</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Review Section -->
    <div class="review-section">
        <h4>Review Terbaru</h4>
        <div class="review-list">
            <!-- Example Review -->
            @for ($i = 0; $i < 3; $i++)
                <div class="review-item">
                    <div class="review-rating">⭐⭐⭐⭐⭐</div>
                    <div class="review-body">Review body</div>
                    <div class="review-author">
                        <img src="https://via.placeholder.com/50" alt="Reviewer" class="rounded-circle">
                        <span>Reviewer name</span>
                        <div>Date</div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>
@endsection
