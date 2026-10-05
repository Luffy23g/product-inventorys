# Product Inventory

A small inventory app: add products (name, quantity, price), see per-row total value and a grand total, and edit rows inline.

- **Live demo (static):** `index.html`, served by GitHub Pages. Data is stored in the visitor's browser (localStorage).
- **PHP version:** `php-version/` is the original app (PHP + vanilla JS + AJAX, data saved to `data.json`). It needs a PHP server:

```
cd php-version
php -S localhost:8000
```

Then open http://localhost:8000/index.php
