<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Laravel Model Caching - Products</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            color: #212529;
        }

        .navbar {
            background: #212529;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .navbar-brand {
            color: white;
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 8px 11px;
            border-radius: 6px;
            font-size: 13px;
        }

        .nav-links a:hover {
            background: #343a40;
        }

        .container {
            max-width: 1250px;
            margin: 35px auto;
            padding: 0 20px;
        }

        .header,
        .filter-box,
        .table-box,
        .feature-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
        }

        .header {
            padding: 25px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0 0 10px;
        }

        .header p {
            color: #6c757d;
            margin: 0;
        }

        .success {
            background: #d1e7dd;
            color: #0f5132;
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .error {
            background: #f8d7da;
            color: #842029;
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
        }

        .stat-card h3 {
            margin: 0 0 10px;
            color: #6c757d;
            font-size: 14px;
        }

        .stat-number {
            font-size: 28px;
            font-weight: bold;
        }

        .blue {
            color: #0d6efd;
        }

        .green {
            color: #198754;
        }

        .red {
            color: #dc3545;
        }

        .purple {
            color: #6f42c1;
        }

        .filter-box {
            padding: 22px;
            margin-bottom: 20px;
        }

        .filter-box h2 {
            margin-top: 0;
        }

        .form-grid {
            display: grid;
            grid-template-columns:
                repeat(5, 1fr);
            gap: 12px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 6px;
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            background: white;
        }

        .filter-buttons {
            margin-top: 15px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .btn {
            display: inline-block;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            text-decoration: none;
            color: white;
            cursor: pointer;
            font-size: 13px;
        }

        .btn-primary {
            background: #0d6efd;
        }

        .btn-success {
            background: #198754;
        }

        .btn-danger {
            background: #dc3545;
        }

        .btn-warning {
            background: #ffc107;
            color: #212529;
        }

        .btn-secondary {
            background: #6c757d;
        }

        .btn-purple {
            background: #6f42c1;
        }

        .cache-status {
            margin-top: 15px;
            padding: 12px;
            border-radius: 7px;
            font-size: 13px;
        }

        .cache-hit {
            background: #d1e7dd;
            color: #0f5132;
        }

        .cache-miss {
            background: #fff3cd;
            color: #664d03;
        }

        .cache-key {
            margin-top: 10px;
            background: #212529;
            color: white;
            padding: 10px;
            border-radius: 6px;
            font-family: monospace;
            overflow-x: auto;
        }

        .table-box {
            padding: 20px;
            overflow-x: auto;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 13px 10px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #f8f9fa;
            font-size: 13px;
        }

        td {
            font-size: 14px;
        }

        .badge {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 12px;
        }

        .available {
            background: #d1e7dd;
            color: #0f5132;
        }

        .out {
            background: #f8d7da;
            color: #842029;
        }

        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 6px;
        }

        .pagination a,
        .pagination span {
            min-width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            text-decoration: none;
            border: 1px solid #dee2e6;
            background: white;
            color: #212529;
        }

        .pagination .active {
            background: #0d6efd;
            color: white;
            border-color: #0d6efd;
        }

        .pagination .disabled {
            opacity: 0.5;
            pointer-events: none;
        }

        .feature-section {
            margin-bottom: 20px;
        }

        .feature-grid {
            display: grid;
            grid-template-columns:
                repeat(4, 1fr);
            gap: 15px;
        }

        .feature-card {
            padding: 18px;
        }

        .feature-card h3 {
            margin-top: 0;
        }

        .feature-card p {
            color: #6c757d;
            font-size: 13px;
            line-height: 1.5;
        }

        .bulk-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }

        .selected-count {
            color: #6c757d;
            font-size: 13px;
        }

        @media (max-width: 1050px) {

            .stats-grid {
                grid-template-columns:
                    repeat(2, 1fr);
            }

            .feature-grid {
                grid-template-columns:
                    repeat(2, 1fr);
            }

            .form-grid {
                grid-template-columns:
                    repeat(2, 1fr);
            }

        }

        @media (max-width: 600px) {

            .stats-grid,
            .feature-grid,
            .form-grid {
                grid-template-columns: 1fr;
            }

            .navbar {
                align-items: flex-start;
            }

        }
    </style>

</head>

<body>

    <div class="navbar">

        <a
            href="{{ url('/products') }}"
            class="navbar-brand">
            Laravel Model Caching
        </a>

        <div class="nav-links">

            <a href="{{ url('/products') }}">
                Products
            </a>

            <a href="{{ url('/products/search') }}">
                Search
            </a>

            <a href="{{ url('/cache-performance') }}">
                Performance
            </a>

            <a href="{{ url('/cache-management') }}">
                Management
            </a>

            <a href="{{ url('/cache-statistics') }}">
                Statistics
            </a>

        </div>

    </div>


    <div class="container">

        @if(session('success'))

        <div class="success">
            {{ session('success') }}
        </div>

        @endif

        @if(session('error'))

        <div class="error">
            {{ session('error') }}
        </div>

        @endif


        <!-- Header -->

        <div class="header">

            <h1>
                📦 Product Management
            </h1>

            <p>
                Cached product listing with search,
                filtering, sorting, pagination and bulk actions.
            </p>

        </div>


        <!-- Statistics -->

        <div class="stats-grid">

            <div class="stat-card">

                <h3>
                    Total Products
                </h3>

                <div class="stat-number blue">
                    {{ $totalProducts }}
                </div>

            </div>


            <div class="stat-card">

                <h3>
                    Available Products
                </h3>

                <div class="stat-number green">
                    {{ $availableProducts }}
                </div>

            </div>


            <div class="stat-card">

                <h3>
                    Out of Stock
                </h3>

                <div class="stat-number red">
                    {{ $outOfStockProducts }}
                </div>

            </div>


            <div class="stat-card">

                <h3>
                    Inventory Value
                </h3>

                <div class="stat-number purple">
                    ₹{{ number_format($inventoryValue, 2) }}
                </div>

            </div>

        </div>


        <!-- Filters -->

        <div class="filter-box">

            <h2>
                🔎 Search, Filter & Sort
            </h2>

            <form
                method="GET"
                action="{{ url('/products') }}">

                <div class="form-grid">

                    <div>

                        <label>
                            Product Name
                        </label>

                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Search product...">

                    </div>


                    <div>

                        <label>
                            Status
                        </label>

                        <select name="status">

                            <option
                                value="all"
                                {{ $status === null ? 'selected' : '' }}>
                                All
                            </option>

                            <option
                                value="1"
                                {{ $status === '1' ? 'selected' : '' }}>
                                Available
                            </option>

                            <option
                                value="0"
                                {{ $status === '0' ? 'selected' : '' }}>
                                Out of Stock
                            </option>

                        </select>

                    </div>


                    <div>

                        <label>
                            Minimum Price
                        </label>

                        <input
                            type="number"
                            name="min_price"
                            value="{{ $minPrice }}"
                            placeholder="50000">

                    </div>


                    <div>

                        <label>
                            Maximum Price
                        </label>

                        <input
                            type="number"
                            name="max_price"
                            value="{{ $maxPrice }}"
                            placeholder="80000">

                    </div>


                    <div>

                        <label>
                            Sort By
                        </label>

                        <select name="sort">

                            <option
                                value="id_desc"
                                {{ $sort === 'id_desc' ? 'selected' : '' }}>
                                ID: High to Low
                            </option>

                            <option
                                value="id_asc"
                                {{ $sort === 'id_asc' ? 'selected' : '' }}>
                                ID: Low to High
                            </option>

                            <option
                                value="name_asc"
                                {{ $sort === 'name_asc' ? 'selected' : '' }}>
                                Name: A-Z
                            </option>

                            <option
                                value="name_desc"
                                {{ $sort === 'name_desc' ? 'selected' : '' }}>
                                Name: Z-A
                            </option>

                            <option
                                value="price_asc"
                                {{ $sort === 'price_asc' ? 'selected' : '' }}>
                                Price: Low to High
                            </option>

                            <option
                                value="price_desc"
                                {{ $sort === 'price_desc' ? 'selected' : '' }}>
                                Price: High to Low
                            </option>

                            <option
                                value="newest"
                                {{ $sort === 'newest' ? 'selected' : '' }}>
                                Newest
                            </option>

                            <option
                                value="oldest"
                                {{ $sort === 'oldest' ? 'selected' : '' }}>
                                Oldest
                            </option>

                        </select>

                    </div>

                </div>


                <div class="filter-buttons">

                    <button
                        type="submit"
                        class="btn btn-primary">
                        🔍 Apply Filters
                    </button>

                    <a
                        href="{{ url('/products') }}"
                        class="btn btn-secondary">
                        Reset
                    </a>

                    <a
                        href="{{ route('products.export', request()->query()) }}"
                        class="btn btn-success">
                        📥 Export CSV
                    </a>

                </div>

            </form>


            @if($cacheHit)

            <div class="cache-status cache-hit" style="display:flex; justify-space-between; align-items:center;">

                <div>
                    ⚡
                    <strong>CACHE HIT ({{ $executionTimeMs }} ms)</strong>

                    — Query loaded from memory cache in {{ $executionTimeMs }} ms.
                </div>

                <span style="background:#0f5132; color:white; padding:3px 8px; border-radius:12px; font-size:11px;">
                    TTL: {{ $dynamicTtl }}s
                </span>

            </div>

            @else

            <div class="cache-status cache-miss" style="display:flex; justify-space-between; align-items:center;">

                <div>
                    🔥
                    <strong>CACHE MISS ({{ $executionTimeMs }} ms)</strong>

                    — Database query executed and cached in {{ $executionTimeMs }} ms.
                </div>

                <span style="background:#664d03; color:white; padding:3px 8px; border-radius:12px; font-size:11px;">
                    TTL: {{ $dynamicTtl }}s
                </span>

            </div>

            @endif


            <div class="cache-key">

                Cache Key:
                {{ $cacheKey }}

            </div>

        </div>


        <!-- Features -->

        <div class="feature-section">

            <div class="feature-grid">

                <div class="feature-card">

                    <h3>
                        📄 Pagination
                    </h3>

                    <p>
                        Cached product results are displayed
                        using numeric pagination.
                    </p>

                </div>


                <div class="feature-card">

                    <h3>
                        ↕ Sorting
                    </h3>

                    <p>
                        Sort products by ID, name,
                        price or creation date.
                    </p>

                </div>


                <div class="feature-card">

                    <h3>
                        📥 CSV Export
                    </h3>

                    <p>
                        Export the current filtered
                        product results to CSV.
                    </p>

                </div>


                <div class="feature-card">

                    <h3>
                        🗑 Bulk Delete
                    </h3>

                    <p>
                        Select multiple products and
                        delete them together.
                    </p>

                </div>

            </div>

        </div>


        <!-- Products -->

        <div class="table-box">

            <div class="table-header">

                <div>

                    <h2>
                        Product List
                    </h2>

                    <span class="selected-count">
                        Showing
                        {{ $products->count() }}
                        of
                        {{ $products->total() }}
                        filtered products
                    </span>

                </div>

                <button type="button" class="btn btn-success" onclick="document.getElementById('createProductModal').style.display='block'">
                    + Add New Product
                </button>

            </div>


            @if($products->count())

            <form
                method="POST"
                action="{{ route('products.bulk-delete') }}"
                id="bulkDeleteForm">

                @csrf


                <div class="bulk-actions">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        onclick="selectAllProducts()">
                        Select All
                    </button>

                    <button
                        type="button"
                        class="btn btn-secondary"
                        onclick="unselectAllProducts()">
                        Unselect All
                    </button>

                    <button
                        type="submit"
                        class="btn btn-danger"
                        onclick="return confirmBulkDelete()">
                        🗑 Delete Selected
                    </button>

                </div>


                <br>


                <table>

                    <thead>

                        <tr>

                            <th>
                                <input
                                    type="checkbox"
                                    id="selectAll"
                                    onclick="toggleAll(this)">
                            </th>

                            <th>
                                ID
                            </th>

                            <th>
                                Product Name
                            </th>

                            <th>
                                Price
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($products as $product)

                        <tr>

                            <td>

                                <input
                                    type="checkbox"
                                    name="ids[]"
                                    value="{{ $product->id }}"
                                    class="product-checkbox">

                            </td>


                            <td>
                                {{ $product->id }}
                            </td>


                            <td>
                                <strong>
                                    {{ $product->name }}
                                </strong>
                            </td>


                            <td>
                                ₹{{ number_format($product->price, 2) }}
                            </td>


                            <td>

                                @if($product->status)

                                <span class="badge available">
                                    Available
                                </span>

                                @else

                                <span class="badge out">
                                    Out of Stock
                                </span>

                                @endif

                            </td>


                            <td>

                                <div style="display:flex; gap:6px;">

                                    <button
                                        type="button"
                                        class="btn btn-warning"
                                        onclick="editProduct({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, {{ $product->status }})">
                                        ✏️ Edit
                                    </button>

                                    <a
                                        href="{{ route('products.delete', $product->id) }}"
                                        class="btn btn-danger"
                                        onclick="return confirm('Delete this product? Model cache will be auto-invalidated.')">
                                        🗑 Delete
                                    </a>

                                </div>

                            </td>

                        </tr>

                        @endforeach

                    </tbody>

                </table>

            </form>


            <!-- Numeric Pagination -->

            @if($products->lastPage() > 1)

            <div class="pagination">

                @for(
                $page = 1;
                $page <= $products->lastPage();
                    $page++
                    )

                    @if($page == $products->currentPage())

                    <span class="active">
                        {{ $page }}
                    </span>

                    @else

                    <a
                        href="{{ $products->url($page) }}">
                        {{ $page }}
                    </a>

                    @endif

                    @endfor

            </div>

            @endif

            @else

            <div style="
                text-align:center;
                padding:40px;
            ">

                <h2>
                    No Products Found
                </h2>

                <p>
                    Try another search or filter.
                </p>

            </div>

            @endif

        </div>

    </div>


    <script>
        function toggleAll(source) {
            const checkboxes =
                document.querySelectorAll('.product-checkbox');

            checkboxes.forEach(function(checkbox) {
                checkbox.checked = source.checked;
            });
        }


        function selectAllProducts() {
            const checkboxes =
                document.querySelectorAll('.product-checkbox');

            checkboxes.forEach(function(checkbox) {
                checkbox.checked = true;
            });

            document.getElementById('selectAll').checked = true;
        }


        function unselectAllProducts() {
            const checkboxes =
                document.querySelectorAll('.product-checkbox');

            checkboxes.forEach(function(checkbox) {
                checkbox.checked = false;
            });

            document.getElementById('selectAll').checked = false;
        }


        function confirmBulkDelete() {
            const selected =
                document.querySelectorAll(
                    '.product-checkbox:checked'
                );

            if (selected.length === 0) {

                alert(
                    'Please select at least one product.'
                );

                return false;
            }

            return confirm(
                'Are you sure you want to delete ' +
                selected.length +
                ' selected product(s)?'
            );
        }

        function editProduct(id, name, price, status) {
            document.getElementById('editProductForm').action = '/products/' + id + '/update';
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_status').value = status;
            document.getElementById('editProductModal').style.display = 'block';
        }
    </script>

    <!-- CREATE PRODUCT MODAL -->
    <div id="createProductModal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
        <div style="background:white; max-width:480px; margin:80px auto; padding:25px; border-radius:12px; box-shadow:0 10px 25px rgba(0,0,0,0.2);">
            <h3 style="margin-top:0;">➕ Add Product (Auto-Flushes Cache ⚡)</h3>
            <form method="POST" action="{{ route('products.store') }}">
                @csrf
                <div style="margin-bottom:12px;">
                    <label>Product Name</label>
                    <input type="text" name="name" required placeholder="e.g. MacBook Pro M3">
                </div>
                <div style="margin-bottom:12px;">
                    <label>Price (₹)</label>
                    <input type="number" step="0.01" name="price" required placeholder="149900">
                </div>
                <div style="margin-bottom:18px;">
                    <label>Status</label>
                    <select name="status">
                        <option value="1">Available</option>
                        <option value="0">Out of Stock</option>
                    </select>
                </div>
                <div style="display:flex; gap:10px; justify-content:flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('createProductModal').style.display='none'">Cancel</button>
                    <button type="submit" class="btn btn-success">Save & Flush Cache ⚡</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT PRODUCT MODAL -->
    <div id="editProductModal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
        <div style="background:white; max-width:480px; margin:80px auto; padding:25px; border-radius:12px; box-shadow:0 10px 25px rgba(0,0,0,0.2);">
            <h3 style="margin-top:0;">✏️ Edit Product (Auto-Flushes Cache ⚡)</h3>
            <form id="editProductForm" method="POST" action="">
                @csrf
                <div style="margin-bottom:12px;">
                    <label>Product Name</label>
                    <input type="text" id="edit_name" name="name" required>
                </div>
                <div style="margin-bottom:12px;">
                    <label>Price (₹)</label>
                    <input type="number" step="0.01" id="edit_price" name="price" required>
                </div>
                <div style="margin-bottom:18px;">
                    <label>Status</label>
                    <select id="edit_status" name="status">
                        <option value="1">Available</option>
                        <option value="0">Out of Stock</option>
                    </select>
                </div>
                <div style="display:flex; gap:10px; justify-content:flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('editProductModal').style.display='none'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update & Flush Cache ⚡</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>