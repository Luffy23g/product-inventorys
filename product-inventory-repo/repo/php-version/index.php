<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Product Inventory</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/style.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">

    <h1 class="mb-4">Product Inventory</h1>

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Add Product</h5>

            <div id="formAlert"></div>

            <form id="productForm" class="row g-3" autocomplete="off">
                <div class="col-md-4">
                    <label for="product_name" class="form-label">Product name</label>
                    <input type="text" class="form-control" id="product_name" name="product_name" required>
                </div>
                <div class="col-md-3">
                    <label for="quantity" class="form-label">Quantity in stock</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" min="0" step="1" required>
                </div>
                <div class="col-md-3">
                    <label for="price" class="form-label">Price per item</label>
                    <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100" id="submitBtn">Add</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Submitted Products</h5>

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle mb-0" id="dataTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Product name</th>
                            <th>Quantity in stock</th>
                            <th>Price per item</th>
                            <th>Datetime submitted</th>
                            <th>Total value</th>
                            <th style="width:160px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="dataTableBody">
                        <tr id="emptyRow"><td colspan="6" class="text-center text-muted">Loading...</td></tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">Grand Total</th>
                            <th id="grandTotal">0.00</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/app.js"></script>
</body>
</html>
