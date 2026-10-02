@extends('layouts.store')

@section('title', 'VUA TABLET - Cửa Hàng Máy Tính Bảng & Phụ Kiện Hàng Đầu')

@section('content')
@php
    $bannerIpadTarget = isset($bannerIpad) && $bannerIpad ? route('products.show', $bannerIpad->id) : '#products-section';
    $bannerGalaxyTarget = isset($bannerGalaxy) && $bannerGalaxy ? route('products.show', $bannerGalaxy->id) : '#products-section';
    $bannerXiaomiTarget = isset($bannerXiaomi) && $bannerXiaomi ? route('products.show', $bannerXiaomi->id) : '#products-section';
@endphp

<div class="container my-5">
    <!-- Ticketbox-Style Billboard Tech Ad Carousel -->
    <div class="ticket-billboard-wrapper mb-5 position-relative">
        <div id="ticketBillboardCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
            <!-- Indicators / Navigation Dots -->
            <div class="carousel-indicators mb-n4">
                <button type="button" data-bs-target="#ticketBillboardCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#ticketBillboardCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            </div>

            <!-- Carousel Slides -->
            <div class="carousel-inner">
                <!-- Slide 1: iPad Pro M4 & Galaxy Tab S9 Ultra -->
                <div class="carousel-item active">
                    <div class="row g-3 g-lg-4">
                        <!-- Banner 1: iPad Pro M4 -->
                        <div class="col-12 col-md-6">
                            <div class="billboard-banner-card position-relative overflow-hidden rounded-4 shadow-lg" onclick="window.location.href='{{ $bannerIpadTarget }}'">
                                <img src="{{ asset('images/banners/banner_ipad_pro.jpg') }}" alt="iPad Pro M4" class="w-100 h-100 object-fit-cover">
                                <div class="billboard-banner-overlay d-flex flex-column justify-content-between p-4">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="badge bg-dark bg-opacity-75 text-emerald border border-emerald-subtle px-3 py-1 rounded-pill small fw-bold backdrop-blur">
                                            Apple Flagship
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between">
                                        <a href="{{ $bannerIpadTarget }}" class="btn billboard-btn rounded-pill px-4 py-2">
                                            Xem chi tiết
                                        </a>
                                        <span class="badge bg-black bg-opacity-60 text-white px-3 py-1 rounded-pill small backdrop-blur">
                                            Chip M4 AI
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Banner 2: Galaxy Tab S9 Ultra -->
                        <div class="col-12 col-md-6">
                            <div class="billboard-banner-card position-relative overflow-hidden rounded-4 shadow-lg" onclick="window.location.href='{{ $bannerGalaxyTarget }}'">
                                <img src="{{ asset('images/banners/banner_galaxy_tab.jpg') }}" alt="Galaxy Tab S9 Ultra & Laptop" class="w-100 h-100 object-fit-cover">
                                <div class="billboard-banner-overlay d-flex flex-column justify-content-between p-4">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="badge bg-dark bg-opacity-75 text-emerald border border-emerald-subtle px-3 py-1 rounded-pill small fw-bold backdrop-blur">
                                            Laptop & Tablet 2-in-1
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between">
                                        <a href="{{ $bannerGalaxyTarget }}" class="btn billboard-btn rounded-pill px-4 py-2">
                                            Xem chi tiết
                                        </a>
                                        <span class="badge bg-black bg-opacity-60 text-white px-3 py-1 rounded-pill small backdrop-blur">
                                            Kèm S-Pen
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 2: Xiaomi Pad 6 Pro & Ultra-Thin Laptop Sale -->
                <div class="carousel-item">
                    <div class="row g-3 g-lg-4">
                        <!-- Banner 3: Xiaomi Pad 6 Pro -->
                        <div class="col-12 col-md-6">
                            <div class="billboard-banner-card position-relative overflow-hidden rounded-4 shadow-lg" onclick="window.location.href='{{ $bannerXiaomiTarget }}'">
                                <img src="{{ asset('images/banners/banner_xiaomi_gaming.jpg') }}" alt="Xiaomi Pad 6 Pro Beast Mode" class="w-100 h-100 object-fit-cover">
                                <div class="billboard-banner-overlay d-flex flex-column justify-content-between p-4">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="badge bg-dark bg-opacity-75 text-emerald border border-emerald-subtle px-3 py-1 rounded-pill small fw-bold backdrop-blur">
                                            Gaming & Làm Việc 144Hz
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between">
                                        <a href="{{ $bannerXiaomiTarget }}" class="btn billboard-btn rounded-pill px-4 py-2">
                                            Xem chi tiết
                                        </a>
                                        <span class="badge bg-black bg-opacity-60 text-white px-3 py-1 rounded-pill small backdrop-blur">
                                            Snap 8+ Gen 1
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Banner 4: Ultra-Thin Laptop & 2-in-1 Mega Sale -->
                        <div class="col-12 col-md-6">
                            <div class="billboard-banner-card position-relative overflow-hidden rounded-4 shadow-lg" onclick="window.location.href='#products-section'">
                                <img src="{{ asset('images/banners/banner_laptop_promo.jpg') }}" alt="Laptop & Tablet Mega Sale" class="w-100 h-100 object-fit-cover">
                                <div class="billboard-banner-overlay d-flex flex-column justify-content-between p-4">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="badge bg-dark bg-opacity-75 text-emerald border border-emerald-subtle px-3 py-1 rounded-pill small fw-bold backdrop-blur">
                                            Mega Tech Sale 40% OFF
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between">
                                        <a href="#products-section" class="btn billboard-btn rounded-pill px-4 py-2">
                                            Khám phá ngay
                                        </a>
                                        <span class="badge bg-black bg-opacity-60 text-white px-3 py-1 rounded-pill small backdrop-blur">
                                            Trả Góp 0%
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Left & Right Arrow Controls (Ticketbox Style) -->
            <button class="carousel-control-prev billboard-nav-btn" type="button" data-bs-target="#ticketBillboardCarousel" data-bs-slide="prev" aria-label="Previous">
                <span class="billboard-nav-icon"><i class="fa-solid fa-chevron-left"></i></span>
            </button>
            <button class="carousel-control-next billboard-nav-btn" type="button" data-bs-target="#ticketBillboardCarousel" data-bs-slide="next" aria-label="Next">
                <span class="billboard-nav-icon"><i class="fa-solid fa-chevron-right"></i></span>
            </button>
        </div>
    </div>

    <!-- Shoppertainment Video Banner Strip -->
    <div class="video-promo-strip p-3 mb-4 rounded-4 d-flex flex-wrap align-items-center justify-content-between gap-3 shadow-sm" style="background: linear-gradient(135deg, rgba(17, 20, 28, 0.95), rgba(6, 78, 59, 0.65)); border: 1px solid rgba(0, 255, 135, 0.35);">
        <div class="d-flex align-items-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-danger rounded-pill px-2 py-0" style="font-size: 0.7rem;">MỚI</span>
                    <h6 class="mb-0 fw-bold text-white">Lướt Video Giải Trí • Tích Xu Giảm Tiền Trực Tiếp Khi Mua Hàng</h6>
                </div>
                <p class="text-white-50 small mb-0 mt-1">Xem video ngắn 30s tích lũy Xu (1 Xu = 500₫ tiền mặt), trừ thẳng vào đơn hàng iPad & phụ kiện khi thanh toán!</p>
            </div>
        </div>
        <a href="{{ route('videos.index') }}" class="btn btn-emerald-glow rounded-pill px-4 py-2 fw-bold text-decoration-none d-inline-flex align-items-center gap-2 flex-shrink-0">
            <span>Cày Xu Mua Hàng</span>
        </a>
    </div>

    <!-- Filter & Search Bar with Laser Scanning Effect -->
    <div class="card card-modern p-4 mb-4 filter-card-container position-relative overflow-visible" id="products-section">
        <!-- High-tech Scanning Laser Beam -->
        <div id="filter-scan-line" class="filter-scan-line"></div>

        <form id="filter-form" action="{{ route('home') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-4">
                <div class="filter-input-wrap position-relative">
                    <input type="text" id="filter-search" name="search" class="form-control filter-input-custom" placeholder="Tìm theo tên máy, chip, ram..." value="{{ request('search') }}" autocomplete="off">
                    <button type="button" id="filter-clear-btn" class="filter-clear-btn {{ request('search') ? '' : 'd-none' }}" aria-label="Xóa tìm kiếm">&times;</button>
                </div>
            </div>

            <div class="col-md-3">
                <!-- Custom Dark Glass Dropdown for Brand Filter -->
                <div class="custom-brand-dropdown position-relative">
                    <input type="hidden" id="filter-brand" name="brand_id" value="{{ request('brand_id') }}">
                    
                    <button type="button" class="btn filter-dropdown-toggle w-100 d-flex align-items-center justify-content-between rounded-pill" 
                            id="brandDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="d-flex align-items-center text-truncate">
                            <span id="selected-brand-label">
                                @php
                                    $currentBrand = $brands->firstWhere('id', request('brand_id'));
                                @endphp
                                {{ $currentBrand ? $currentBrand->name : '-- Tất cả thương hiệu --' }}
                            </span>
                        </span>
                        <i class="fa-solid fa-chevron-down ms-2 small dropdown-chevron text-white-50"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-dark custom-dropdown-menu shadow-lg border-0 w-100 p-2" aria-labelledby="brandDropdownBtn">
                        <li>
                            <button type="button" class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center justify-content-between js-brand-item {{ !request('brand_id') ? 'active' : '' }}" data-brand-id="" data-brand-name="-- Tất cả thương hiệu --">
                                <span>-- Tất cả thương hiệu --</span>
                                <i class="fa-solid fa-check small check-icon {{ !request('brand_id') ? '' : 'd-none' }}" style="color: #00f59b;"></i>
                            </button>
                        </li>
                        @foreach($brands as $brand)
                            <li>
                                <button type="button" class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center justify-content-between js-brand-item {{ request('brand_id') == $brand->id ? 'active' : '' }}" data-brand-id="{{ $brand->id }}" data-brand-name="{{ $brand->name }}">
                                    <span>{{ $brand->name }}</span>
                                    <i class="fa-solid fa-check small check-icon {{ request('brand_id') == $brand->id ? '' : 'd-none' }}" style="color: #00f59b;"></i>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="col-md-5 d-flex flex-wrap gap-2 align-items-center">
                <input type="hidden" id="filter-category" name="category_id" value="{{ request('category_id') }}">
                <button type="submit" id="btn-submit-filter" class="btn btn-modern-primary px-4 rounded-pill filter-action-btn">
                    Lọc
                </button>
                <button type="button" id="btn-reset-filter" class="btn btn-modern-outline rounded-pill filter-action-btn {{ (request('search') || request('category_id') || request('brand_id')) ? '' : 'd-none' }}">
                    Xóa lọc
                </button>
                <a href="{{ route('ai.advisor') }}" class="btn btn-emerald-glow rounded-pill px-3 py-2 fw-bold text-decoration-none d-inline-flex align-items-center" title="Trắc nghiệm 5 câu hỏi nhanh tìm iPad phù hợp nhất">
                    AI Chọn iPad Hộ Tôi
                </a>
            </div>
        </form>

        <!-- Category Chip Pills & Live Count Header -->
        <div class="d-flex flex-wrap gap-2 mt-3 pt-3 border-top align-items-center justify-content-between" id="category-pills-bar">
            <div class="d-flex flex-wrap gap-2 align-items-center" id="category-pills">
                <span class="text-muted fw-bold me-2 small">Danh mục:</span>
                <a href="{{ route('home', array_merge(request()->except('category_id', 'page'))) }}" 
                   class="chip-pill js-category-pill {{ !request('category_id') ? 'active' : '' }}"
                   data-cat-id="">
                    Tất cả
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('home', array_merge(request()->except('page'), ['category_id' => $cat->id])) }}" 
                       class="chip-pill js-category-pill {{ request('category_id') == $cat->id ? 'active' : '' }}"
                       data-cat-id="{{ $cat->id }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
            <div id="filter-results-badge" class="filter-count-badge small">
                <span>{{ $products->total() }} sản phẩm</span>
            </div>
        </div>
    </div>

    <!-- Products Wrapper with Loading Indicator -->
    <div id="product-list-wrapper" class="position-relative">
        <div class="product-ajax-loader">
            <span class="spinner-border spinner-border-sm text-emerald" role="status" aria-hidden="true"></span>
            <span>Đang cập nhật...</span>
        </div>
        @include('products._product_list')
    </div>
</div>

<!-- Toast thông báo giỏ hàng không reload -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
    <div id="cartToast" class="toast align-items-center text-white bg-success border-0 shadow-lg rounded-4" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex p-2">
            <div class="toast-body d-flex align-items-center gap-2 fs-6">
                <i id="cartToastIcon" class="fa-solid fa-circle-check fs-5"></i>
                <span id="cartToastMsg">Đã thêm vào giỏ hàng thành công!</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Toast Notification Setup
    const cartToastEl = document.getElementById('cartToast');
    const cartToast = cartToastEl ? new bootstrap.Toast(cartToastEl, { delay: 3000 }) : null;
    const cartToastMsg = document.getElementById('cartToastMsg');
    const cartToastIcon = document.getElementById('cartToastIcon');
    const navCartCount = document.getElementById('nav-cart-count');

    function showNotification(message, isSuccess = true) {
        if (!cartToast) return;
        cartToastEl.className = `toast align-items-center text-white ${isSuccess ? 'bg-success' : 'bg-danger'} border-0 shadow-lg rounded-4`;
        if (cartToastIcon) {
            cartToastIcon.className = isSuccess ? 'fa-solid fa-circle-check fs-5' : 'fa-solid fa-circle-exclamation fs-5';
        }
        if (cartToastMsg) {
            cartToastMsg.textContent = message;
        }
        cartToast.show();
    }

    // 2. DOM Elements for AJAX Filter
    const filterCard = document.querySelector('.filter-card-container');
    const filterForm = document.getElementById('filter-form');
    const searchInput = document.getElementById('filter-search');
    const filterClearBtn = document.getElementById('filter-clear-btn');
    const brandSelect = document.getElementById('filter-brand');
    const categoryInput = document.getElementById('filter-category');
    const btnSubmit = document.getElementById('btn-submit-filter');
    const btnReset = document.getElementById('btn-reset-filter');
    const filterResultsBadge = document.getElementById('filter-results-badge');
    const productWrapper = document.getElementById('product-list-wrapper');
    const categoryPills = document.querySelectorAll('.js-category-pill');

    let debounceTimer = null;
    let abortController = null;

    // Helper: Build URL from filter form state
    function buildFilterUrl(page = null) {
        const params = new URLSearchParams();
        const searchVal = searchInput ? searchInput.value.trim() : '';
        const brandVal = brandSelect ? brandSelect.value : '';
        const catVal = categoryInput ? categoryInput.value : '';

        if (searchVal) params.set('search', searchVal);
        if (brandVal) params.set('brand_id', brandVal);
        if (catVal) params.set('category_id', catVal);
        if (page) params.set('page', page);

        const queryString = params.toString();
        return '{{ route('home') }}' + (queryString ? '?' + queryString : '');
    }

    // Helper: Update active UI state for category pills & reset button
    function updateFilterUI() {
        const catVal = categoryInput ? categoryInput.value : '';
        categoryPills.forEach(pill => {
            const pillCatId = pill.getAttribute('data-cat-id') || '';
            if (pillCatId === catVal) {
                pill.classList.add('active');
            } else {
                pill.classList.remove('active');
            }
        });

        const hasFilter = (searchInput && searchInput.value.trim() !== '') || 
                          (brandSelect && brandSelect.value !== '') || 
                          (categoryInput && categoryInput.value !== '');
        if (btnReset) {
            if (hasFilter) {
                btnReset.classList.remove('d-none');
            } else {
                btnReset.classList.add('d-none');
            }
        }

        if (filterClearBtn && searchInput) {
            if (searchInput.value.trim() !== '') {
                filterClearBtn.classList.remove('d-none');
            } else {
                filterClearBtn.classList.add('d-none');
            }
        }
    }

    // Core AJAX Fetch function
    function fetchProducts(url, updateHistory = true, shouldScroll = false) {
        if (abortController) {
            abortController.abort();
        }
        abortController = new AbortController();

        if (filterCard) {
            filterCard.classList.add('is-filtering');
        }
        if (productWrapper) {
            productWrapper.classList.add('is-loading');
        }

        const originalBtnHtml = btnSubmit ? btnSubmit.innerHTML : '';
        if (btnSubmit) {
            btnSubmit.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Đang lọc...`;
            btnSubmit.disabled = true;
        }

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            signal: abortController.signal
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (productWrapper && data.html !== undefined) {
                productWrapper.innerHTML = `
                    <div class="product-ajax-loader">
                        <span class="spinner-border spinner-border-sm text-emerald" role="status" aria-hidden="true"></span>
                        <span>Đang cập nhật...</span>
                    </div>` + data.html;
            }

            if (filterResultsBadge && data.total !== undefined) {
                filterResultsBadge.innerHTML = `<span>${data.total} sản phẩm</span>`;
                filterResultsBadge.classList.add('badge-pulse');
                setTimeout(() => filterResultsBadge.classList.remove('badge-pulse'), 600);
            }

            if (updateHistory) {
                window.history.pushState({ path: url }, '', url);
            }

            updateFilterUI();

            if (shouldScroll) {
                const section = document.getElementById('products-section');
                if (section) {
                    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        })
        .catch(err => {
            if (err.name !== 'AbortError') {
                console.error('AJAX Filter error:', err);
                showNotification('Có lỗi khi tải danh sách sản phẩm. Vui lòng thử lại!', false);
            }
        })
        .finally(() => {
            if (filterCard) {
                filterCard.classList.remove('is-filtering');
            }
            if (productWrapper) {
                productWrapper.classList.remove('is-loading');
            }
            if (btnSubmit) {
                btnSubmit.innerHTML = originalBtnHtml;
                btnSubmit.disabled = false;
            }
        });
    }

    // Event: Form Submit (Click "Lọc" or Enter in search)
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            clearTimeout(debounceTimer);
            const url = buildFilterUrl();
            fetchProducts(url, true, false);
        });
    }

    // Event: Live Search with Debounce (typing)
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            if (filterClearBtn) {
                filterClearBtn.classList.toggle('d-none', !this.value.trim());
            }
            clearTimeout(debounceTimer);
            if (filterCard) filterCard.classList.add('is-filtering');
            debounceTimer = setTimeout(() => {
                const url = buildFilterUrl();
                fetchProducts(url, true, false);
            }, 300);
        });
    }

    // Event: Click clear search button (&times;)
    if (filterClearBtn && searchInput) {
        filterClearBtn.addEventListener('click', function() {
            searchInput.value = '';
            filterClearBtn.classList.add('d-none');
            searchInput.focus();
            const url = buildFilterUrl();
            fetchProducts(url, true, false);
        });
    }

    // Event: Click Brand Dropdown Item
    const brandItems = document.querySelectorAll('.js-brand-item');
    const selectedBrandLabel = document.getElementById('selected-brand-label');

    brandItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            clearTimeout(debounceTimer);
            const brandId = this.getAttribute('data-brand-id') || '';
            const brandName = this.getAttribute('data-brand-name') || '-- Tất cả thương hiệu --';

            if (brandSelect) {
                brandSelect.value = brandId;
            }

            if (selectedBrandLabel) {
                selectedBrandLabel.textContent = brandName;
            }

            // Update active state and checkmarks
            brandItems.forEach(i => {
                const isSelected = (i === this);
                i.classList.toggle('active', isSelected);
                const check = i.querySelector('.check-icon');
                if (check) {
                    check.classList.toggle('d-none', !isSelected);
                }
            });

            updateFilterUI();

            // Đóng menu dropdown sau khi chọn để không che khuất danh mục
            const dropdownBtn = document.getElementById('brandDropdownBtn');
            if (dropdownBtn && typeof bootstrap !== 'undefined') {
                const bsDropdown = bootstrap.Dropdown.getOrCreateInstance(dropdownBtn);
                if (bsDropdown) {
                    bsDropdown.hide();
                }
            }

            const url = buildFilterUrl();
            fetchProducts(url, true, false);
        });
    });

    // Event: Click Category Chip Pills
    categoryPills.forEach(pill => {
        pill.addEventListener('click', function(e) {
            e.preventDefault();
            clearTimeout(debounceTimer);
            const catId = this.getAttribute('data-cat-id') || '';
            if (categoryInput) {
                categoryInput.value = catId;
            }
            const url = buildFilterUrl();
            fetchProducts(url, true, false);
        });
    });

    // Event: Click Reset Filter button
    function resetAllFilters() {
        clearTimeout(debounceTimer);
        if (searchInput) searchInput.value = '';
        if (filterClearBtn) filterClearBtn.classList.add('d-none');
        if (brandSelect) brandSelect.value = '';
        if (categoryInput) categoryInput.value = '';
        if (selectedBrandLabel) selectedBrandLabel.textContent = '-- Tất cả thương hiệu --';

        brandItems.forEach((i, idx) => {
            const isFirst = (idx === 0);
            i.classList.toggle('active', isFirst);
            const check = i.querySelector('.check-icon');
            if (check) check.classList.toggle('d-none', !isFirst);
        });

        const cleanUrl = '{{ route('home') }}';
        fetchProducts(cleanUrl, true, false);
    }

    if (btnReset) {
        btnReset.addEventListener('click', function(e) {
            e.preventDefault();
            resetAllFilters();
        });
    }

    // Event: Click Reset Button from Empty State via delegation
    document.addEventListener('click', function(e) {
        const resetBtn = e.target.closest('.js-btn-reset-filter');
        if (resetBtn) {
            e.preventDefault();
            resetAllFilters();
        }
    });

    // Event: Click Pagination Links via delegation
    document.addEventListener('click', function(e) {
        const pageLink = e.target.closest('.ajax-pagination-wrapper a.page-link');
        if (pageLink && pageLink.href) {
            e.preventDefault();
            fetchProducts(pageLink.href, true, true);
        }
    });

    // Event: Browser Back / Forward buttons (popstate)
    window.addEventListener('popstate', function() {
        const params = new URLSearchParams(window.location.search);
        if (searchInput) searchInput.value = params.get('search') || '';
        if (brandSelect) brandSelect.value = params.get('brand_id') || '';
        if (categoryInput) categoryInput.value = params.get('category_id') || '';
        updateFilterUI();
        fetchProducts(window.location.href, false, false);
    });

    // 3. Event Delegation for Add to Cart without page reload
    document.addEventListener('submit', function(e) {
        const form = e.target.closest('.ajax-add-cart-form');
        if (!form) return;

        e.preventDefault();

        const submitBtn = form.querySelector('.btn-ajax-add');
        const originalHtml = submitBtn ? submitBtn.innerHTML : '';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Đang thêm...`;
        }

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            return response.json().then(data => ({
                status: response.status,
                data: data
            }));
        })
        .then(({ status, data }) => {
            if (data.redirect) {
                window.location.href = data.redirect;
                return;
            }

            if (data.success) {
                if (navCartCount && data.cart_count !== undefined) {
                    navCartCount.textContent = data.cart_count;
                    navCartCount.classList.remove('d-none');
                }

                showNotification(data.message || 'Đã thêm sản phẩm vào giỏ hàng thành công!', true);

                if (submitBtn) {
                    submitBtn.innerHTML = `Đã thêm!`;
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalHtml;
                    }, 1200);
                }
            } else {
                showNotification(data.message || 'Có lỗi xảy ra, vui lòng thử lại!', false);
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }
            }
        })
        .catch(error => {
            console.error('Lỗi thêm giỏ hàng:', error);
            showNotification('Không thể kết nối máy chủ. Vui lòng thử lại!', false);
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            }
        });
    });
});
</script>
@endpush