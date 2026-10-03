@extends('layouts.store')

@section('title', 'VUA TABLET - Cửa Hàng Máy Tính Bảng & Phụ Kiện Hàng Đầu')

@section('content')
@php
    $bannerIpadTarget = isset($bannerIpad) && $bannerIpad ? route('products.show', $bannerIpad->id) : '#products-section';
    $bannerGalaxyTarget = isset($bannerGalaxy) && $bannerGalaxy ? route('products.show', $bannerGalaxy->id) : '#products-section';
    $bannerXiaomiTarget = isset($bannerXiaomi) && $bannerXiaomi ? route('products.show', $bannerXiaomi->id) : '#products-section';
@endphp

<div class="container-fluid px-3 px-md-4 px-xl-5 my-3 my-lg-4" style="max-width: 1540px;">
    <!-- Hero Section Banner To Cực Đẹp (Chuẩn Phong Cách Trong Ảnh Mẫu) -->
    <section class="hero-section mb-4 mb-lg-5">
        <div class="row align-items-center g-4 g-lg-5">
            <!-- Cột trái: Thông điệp & Nút CTA -->
            <div class="col-12 col-lg-5">
                <div class="hero-content pe-lg-3">

                    <!-- Tiêu đề Hero siêu to cực ấn tượng -->
                    <h1 class="hero-title mb-3">
                        Thế Giới Tablet<br>
                        <span style="color: #4f46e5;">Chính Hãng</span>
                    </h1>

                    <!-- Đoạn mô tả -->
                    <p class="hero-subtitle mb-4">
                        Khám phá hàng ngàn mẫu Tablet Apple iPad, Samsung Galaxy Tab, Xiaomi Pad mới nhất và likenew. Đảm bảo bảo hành 12 tháng, hỗ trợ trả góp 0% nhanh chóng.
                    </p>

                    <!-- Nhóm nút hành động Hero: Mua sắm ngay & Xem video nhận xu -->
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <a href="#products-section" class="btn-hero-cta">
                            Mua sắm ngay
                        </a>
                        <a href="{{ route('videos.index') }}" class="btn-hero-video">
                            Xem video nhận xu
                        </a>
                    </div>
                </div>
            </div>

            <!-- Cột phải: Khung ảnh Hero Tablet bo tròn 36px siêu to ấn tượng -->
            <div class="col-12 col-lg-7">
                <div class="hero-image-card">
                    <img src="{{ asset('images/banners/hero_tablet_banner.jpg') }}" alt="Thế Giới Máy Tính Bảng Chính Hãng - VUA TABLET" loading="eager">
                </div>
            </div>
        </div>
    </section>

    <!-- Banner Lướt Video Nhận Xu - Phong cách tối giản hiện đại -->
    <div class="video-promo-strip p-3 p-md-4 mb-4 rounded-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="video-promo-icon-box d-flex align-items-center justify-content-center rounded-circle" style="width: 44px; height: 44px; background: rgba(79, 70, 229, 0.1); color: #4f46e5; flex-shrink: 0; font-size: 1.15rem;">
                <i class="fa-solid fa-play"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-danger rounded-pill px-2 py-0" style="font-size: 0.7rem; font-weight: 700;">MỚI</span>
                    <h6 class="mb-0 fw-bold" style="color: #0f172a;">Lướt Video Giải Trí • Tích Xu Giảm Tiền Mua Hàng</h6>
                </div>
                <p class="small mb-0" style="color: #64748b;">Xem video ngắn 30s nhận ngay Xu thưởng để khấu trừ trực tiếp khi đặt hàng máy tính bảng & phụ kiện.</p>
            </div>
        </div>
        <a href="{{ route('videos.index') }}" class="btn btn-hero-video py-2 px-4" style="font-size: 0.95rem;">
            Xem video nhận xu
        </a>
    </div>

    <!-- Filter & Search Bar - VUA TABLET Clean Style -->
    <div class="card card-modern p-3 mb-4 filter-card-container position-relative overflow-visible" id="products-section">
        <form id="filter-form" action="{{ route('home') }}" method="GET" class="row g-2 align-items-center">
            <input type="hidden" id="filter-category" name="category_id" value="{{ request('category_id') }}">

            <!-- 1. Input tìm kiếm & Nút Tìm kiếm -->
            <div class="col-12 col-md-7 col-lg-8">
                <div class="d-flex gap-2 align-items-center">
                    <div class="filter-input-wrap position-relative flex-grow-1">
                        <i class="fa-solid fa-magnifying-glass filter-input-icon"></i>
                        <input type="text" id="filter-search" name="search" class="form-control filter-input-custom" placeholder="Tìm kiếm tên máy tính bảng, iPad, cấu hình..." value="{{ request('search') }}" autocomplete="off">
                        <button type="button" id="filter-clear-btn" class="filter-clear-btn {{ request('search') ? '' : 'd-none' }}" aria-label="Xóa tìm kiếm">&times;</button>
                    </div>
                    <button type="submit" id="btn-search-submit" class="btn btn-search-primary" title="Tìm kiếm">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span class="d-none d-sm-inline">Tìm kiếm</span>
                    </button>
                </div>
            </div>

            <!-- 2. Dropdown Tất cả hãng (icon phễu) -->
            <div class="col-12 col-md-5 col-lg-4 d-flex gap-2">
                <div class="custom-brand-dropdown position-relative flex-grow-1" style="z-index: 1000;">
                    <input type="hidden" id="filter-brand" name="brand_id" value="{{ request('brand_id') }}">
                    
                    <button type="button" class="btn filter-dropdown-toggle w-100 d-flex align-items-center justify-content-between" 
                            id="brandDropdownBtn" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                        <span class="d-flex align-items-center text-truncate gap-2">
                            <i class="fa-solid fa-filter small text-muted"></i>
                            <span id="selected-brand-label">
                                @php
                                    $currentBrand = $brands->firstWhere('id', request('brand_id'));
                                @endphp
                                {{ $currentBrand ? $currentBrand->name : 'Tất cả hãng' }}
                            </span>
                        </span>
                        <i class="fa-solid fa-chevron-down ms-2 small dropdown-chevron text-muted"></i>
                    </button>

                    <ul class="dropdown-menu shadow-lg border-0 w-100 p-2" aria-labelledby="brandDropdownBtn" style="z-index: 9999; border-radius: 14px;">
                        <li>
                            <button type="button" class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center justify-content-between js-brand-item {{ !request('brand_id') ? 'active' : '' }}" data-brand-id="" data-brand-name="Tất cả hãng">
                                <span>Tất cả hãng</span>
                                <i class="fa-solid fa-check small check-icon {{ !request('brand_id') ? '' : 'd-none' }}" style="color: #4f46e5;"></i>
                            </button>
                        </li>
                        @foreach($brands as $brand)
                            <li>
                                <button type="button" class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center justify-content-between js-brand-item {{ request('brand_id') == $brand->id ? 'active' : '' }}" data-brand-id="{{ $brand->id }}" data-brand-name="{{ $brand->name }}">
                                    <span>{{ $brand->name }}</span>
                                    <i class="fa-solid fa-check small check-icon {{ request('brand_id') == $brand->id ? '' : 'd-none' }}" style="color: #4f46e5;"></i>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" id="btn-reset-filter" class="btn btn-modern-outline {{ (request('search') || request('category_id') || request('brand_id')) ? '' : 'd-none' }}" title="Xóa tất cả bộ lọc">
                    <i class="fa-solid fa-rotate-left"></i>
                </button>
            </div>
        </form>

        <!-- Category Chips & Live Counter Header -->
        <div class="d-flex flex-wrap gap-2 mt-3 pt-3 border-top align-items-center justify-content-between" id="category-pills-bar">
            <div class="d-flex flex-wrap gap-2 align-items-center" id="category-pills">
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
            <span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span>
            <span class="ms-1">Đang cập nhật...</span>
        </div>
        @include('products._product_list')
    </div>
</div>

<!-- Toast thông báo giỏ hàng không reload -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
    <div id="cartToast" class="toast toast-modern align-items-center border-0 shadow-lg rounded-4" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex p-2">
            <div class="toast-body d-flex align-items-center gap-2 fs-6">
                <i id="cartToastIcon" class="fa-solid fa-circle-check fs-5 text-indigo-light"></i>
                <span id="cartToastMsg">Đã thêm vào giỏ hàng thành công!</span>
            </div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
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
        cartToastEl.className = `toast toast-modern align-items-center ${isSuccess ? 'toast-modern-success' : 'toast-modern-danger'} border-0 shadow-lg rounded-4`;
        if (cartToastIcon) {
            cartToastIcon.className = isSuccess ? 'fa-solid fa-circle-check fs-5 text-indigo-light' : 'fa-solid fa-circle-exclamation fs-5 text-danger';
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
    const btnSearchSubmit = document.getElementById('btn-search-submit');
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

        const originalSearchBtnHtml = btnSearchSubmit ? btnSearchSubmit.innerHTML : '';
        if (btnSearchSubmit) {
            btnSearchSubmit.innerHTML = `<span class="spinner-border spinner-border-sm"></span><span class="d-none d-sm-inline ms-1">Đang tìm...</span>`;
            btnSearchSubmit.disabled = true;
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
                        <span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span>
                        <span class="ms-1">Đang cập nhật...</span>
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
            if (btnSearchSubmit) {
                btnSearchSubmit.innerHTML = originalSearchBtnHtml;
                btnSearchSubmit.disabled = false;
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

        const actionUrl = form.getAttribute('action');

        fetch(actionUrl, {
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