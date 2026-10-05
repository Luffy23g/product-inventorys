document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    var form = document.getElementById('productForm');
    var tbody = document.getElementById('dataTableBody');
    var grandTotalEl = document.getElementById('grandTotal');
    var formAlert = document.getElementById('formAlert');
    var submitBtn = document.getElementById('submitBtn');

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = (str === null || str === undefined) ? '' : String(str);
        return div.innerHTML;
    }

    function formatMoney(n) {
        var num = Number(n);
        return isNaN(num) ? '0.00' : num.toFixed(2);
    }

    function formatQty(n) {
        var num = Number(n);
        return isNaN(num) ? '0' : String(num);
    }

    function showAlert(message, type) {
        formAlert.innerHTML =
            '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
            escapeHtml(message) +
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
            '</div>';
    }

    function displayRowHtml(item) {
        return '' +
            '<td class="cell-name">' + escapeHtml(item.product_name) + '</td>' +
            '<td class="cell-qty">' + escapeHtml(formatQty(item.quantity)) + '</td>' +
            '<td class="cell-price">' + formatMoney(item.price) + '</td>' +
            '<td class="cell-datetime">' + escapeHtml(item.datetime) + '</td>' +
            '<td class="cell-total">' + formatMoney(item.total) + '</td>' +
            '<td><button type="button" class="btn btn-sm btn-outline-primary btn-edit">Edit</button></td>';
    }

    function editRowHtml(item) {
        return '' +
            '<td><input type="text" class="form-control form-control-sm edit-name" value="' + escapeHtml(item.product_name) + '"></td>' +
            '<td><input type="number" min="0" step="1" class="form-control form-control-sm edit-qty" value="' + escapeHtml(formatQty(item.quantity)) + '"></td>' +
            '<td><input type="number" min="0" step="0.01" class="form-control form-control-sm edit-price" value="' + escapeHtml(item.price) + '"></td>' +
            '<td class="cell-datetime">' + escapeHtml(item.datetime) + '</td>' +
            '<td class="cell-total">' + formatMoney(item.total) + '</td>' +
            '<td>' +
            '<button type="button" class="btn btn-sm btn-success btn-save me-1">Save</button>' +
            '<button type="button" class="btn btn-sm btn-secondary btn-cancel">Cancel</button>' +
            '</td>';
    }

    function renderRows(items, grandTotal) {
        tbody.innerHTML = '';

        if (!items.length) {
            tbody.innerHTML = '<tr id="emptyRow"><td colspan="6" class="text-center text-muted">No products submitted yet.</td></tr>';
        } else {
            items.forEach(function (item) {
                var tr = document.createElement('tr');
                tr.setAttribute('data-id', item.id);
                tr.innerHTML = displayRowHtml(item);
                tbody.appendChild(tr);
            });
        }

        grandTotalEl.textContent = formatMoney(grandTotal);
    }

    function loadData() {
        fetch('api.php?action=list', { method: 'GET' })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success) {
                    renderRows(data.items, data.grand_total);
                } else {
                    showAlert(data.message || 'Failed to load data.', 'danger');
                }
            })
            .catch(function () {
                showAlert('Could not reach server to load data.', 'danger');
            });
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        formAlert.innerHTML = '';

        var params = new URLSearchParams();
        params.append('action', 'add');
        params.append('product_name', document.getElementById('product_name').value.trim());
        params.append('quantity', document.getElementById('quantity').value);
        params.append('price', document.getElementById('price').value);

        submitBtn.disabled = true;

        fetch('api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: params.toString()
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                submitBtn.disabled = false;
                if (data.success) {
                    form.reset();
                    document.getElementById('product_name').focus();
                    loadData();
                } else {
                    showAlert(data.message || 'Could not save product.', 'danger');
                }
            })
            .catch(function () {
                submitBtn.disabled = false;
                showAlert('Request failed. Please try again.', 'danger');
            });
    });

    // Event delegation for Edit / Save / Cancel buttons within the table body.
    tbody.addEventListener('click', function (e) {
        var target = e.target;
        var tr = target.closest('tr');
        if (!tr) {
            return;
        }
        var id = tr.getAttribute('data-id');

        if (target.classList.contains('btn-edit')) {
            var current = {
                product_name: tr.querySelector('.cell-name').textContent,
                quantity: tr.querySelector('.cell-qty').textContent,
                price: tr.querySelector('.cell-price').textContent,
                datetime: tr.querySelector('.cell-datetime').textContent,
                total: tr.querySelector('.cell-total').textContent
            };
            tr.innerHTML = editRowHtml(current);
            tr.querySelector('.edit-name').focus();
            return;
        }

        if (target.classList.contains('btn-cancel')) {
            loadData();
            return;
        }

        if (target.classList.contains('btn-save')) {
            var name = tr.querySelector('.edit-name').value.trim();
            var qty = tr.querySelector('.edit-qty').value;
            var price = tr.querySelector('.edit-price').value;

            var params = new URLSearchParams();
            params.append('action', 'edit');
            params.append('id', id);
            params.append('product_name', name);
            params.append('quantity', qty);
            params.append('price', price);

            target.disabled = true;

            fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: params.toString()
            })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.success) {
                        loadData();
                    } else {
                        target.disabled = false;
                        showAlert(data.message || 'Could not save changes.', 'danger');
                    }
                })
                .catch(function () {
                    target.disabled = false;
                    showAlert('Request failed while saving changes.', 'danger');
                });
        }
    });

    loadData();
});
