@extends('layouts.layout')

@section('content')

<div class="main-container">
    <h1>Mnenja</h1>
    
    <!-- Filter and Search Form -->
    <div class="filter-bar">
        <form id="filterForm" method="GET" action="{{ url('bartender/manual-check') }}" class="filter-form">
        <div class="filter-container">
    <!-- Filter by Is Posted -->
    <div class="filter-group">
        <label for="is_posted">Filtriranje po statusu:</label>
        <select name="is_posted" id="is_posted">
            <option value="all" {{ request('is_posted') === 'all' ? 'selected' : '' }}>Vsi</option>
            <option value="1" {{ request('is_posted') === '1' ? 'selected' : '' }}>Objavljen</option>
            <option value="0" {{ request('is_posted') === '0' ? 'selected' : '' }}>Ni objavljen</option>
        </select>
    </div>

    <!-- Sort by Created At -->
    <div class="filter-group">
        <label for="sort_by">Razvrsti po datumu:</label>
        <select name="sort_by" id="sort_by">
            <option value="asc" {{ request('sort_by') == 'asc' ? 'selected' : '' }}>Najstarejše prvo</option>
            <option value="desc" {{ request('sort_by') == 'desc' ? 'selected' : '' }}>Najnovejše prvo</option>
        </select>
    </div>

    <!-- Search Field -->
    <div class="filter-group">
        <label for="search">Iskanje:</label>
        <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Iskanje po e-pošti, hashtagu ali pregledu">
    </div>

    <!-- Submit Button -->
    <div class="filter-group">
        <button type="submit" class="filter-btn">Uporabi</button>
    </div>
    
    <!-- Reset Button -->
    <div class="filter-group">
       <p> <a href="{{ route('manualCheck') }}" class="reset-btn">Ponastavitev filtrov</a></p>
    </div>
        
</div>

        </form>
    </div>
    

    <div class="table-container">
        @php
            \Carbon\Carbon::setLocale('sl');
        @endphp
        <table class="reviews-table">
            <thead>
                <tr>
                    <th>Hashtag</th>
                    <th>E-pošta uporabnika</th>
                    <th>Mnenje</th>
                    <th>Objavljeno</th>
                    <th>nagrada prevzeta</th>
                    <th>Ustvarjen</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="reviewsTableBody">
                <!-- Loop for displaying reviews dynamically -->
                @foreach ($reviews as $review)
                <tr>
                    <td>{{ $review->HASHTAG }}</td>
                    <td>{{ $review->USER_EMAIL }}</td>
                    <td>
                        <div class="review-text-container">
                            <div class="review-text" id="review-{{ $review->id }}">
                                {{ $review->REVIEW }}
                            </div>
                            <button class="read-more-btn" data-review-id="{{ $review->id }}">
                                Preveri več
                            </button>
                        </div>
                    </td>
                    <td>{{ $review->IS_POSTED ? 'Yes' : 'No' }}</td>
                    <td>{{ $review->is_scanned ? 'Yes' : 'No' }}</td>
                    <td>{{ $review->created_at->translatedFormat('d M Y; H:i') }}</td>
                    <td>
                        <!-- Conditionally display buttons -->
                        @if (!$review->IS_POSTED)
                            <!-- Button to mark the review as posted -->
                            <form action="{{ url('bartender/reviews/mark-as-posted') }}" method="POST" style="display: flex; align-items: center; gap: 10px;">
                                @csrf
                                <input type="hidden" name="hashtag" value="{{ $review->HASHTAG }}">
                            
                                <!-- Star rating select input -->
                                <label for="stars" style="margin-right: 5px;">Izberi oceno mnenja:</label>
                                <select name="stars" required style="padding: 4px;">
                                    <option value="">Izberi...</option>
                                    <option value="5" {{ $review->stars == 5 ? 'selected' : '' }}>5</option>
                                    <option value="4" {{ $review->stars == 4 ? 'selected' : '' }}>4</option>
                                    <option value="3" {{ $review->stars == 3 ? 'selected' : '' }}>3</option>
                                    <option value="2" {{ $review->stars == 2 ? 'selected' : '' }}>2</option>
                                    <option value="1" {{ $review->stars == 1 ? 'selected' : '' }}>1</option>
                                </select>
                            
                                <button type="submit" class="update-btn mark-posted-btn" style="max-width:200px">
                                    Označi kot objavljeno
                                </button>
                            </form>

                        @else
                            <!-- Button to show QR code -->
                            <form action="{{ url('bartender/reviews/show-qr-code') }}" method="POST">
                                @csrf
                                <input type="hidden" name="userId" value="{{ $review->USER_ID }}">
                                <button type="submit" class="show-qr-code-btn show-qr-code-btn" style="max-width:200px">
                                    Prikaži QR kodo
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
