Product Inventory - PHP Skills Test
====================================

WHAT THIS IS
------------
A single-page app for recording products (name, quantity in stock, price
per item). Submitted data is stored in data.json (valid JSON) and shown
in a table below the form, ordered by the datetime it was submitted.
Each row also shows a calculated "Total value" (quantity * price), and
the last row of the table shows the grand total of all rows. Each row
can be edited in place.

The form submission, the table refresh, and the inline edit/save all
happen via AJAX (fetch calls to api.php) - no full page reloads.

FILES
-----
index.php        Main page: Bootstrap-styled form + results table.
api.php           Backend endpoint handling "list", "add", and "edit"
                  actions, reading/writing data.json.
data.json         Data store (starts as an empty JSON array: []).
assets/app.js     Front-end JavaScript: AJAX calls, rendering, inline
                  editing.
assets/style.css  Minor styling on top of Bootstrap.

HOW TO RUN
----------
Requires PHP (7.4+ recommended, tested on 8.3) and a webserver, or you
can use PHP's built-in server for a quick local test:

    cd php-skills-test
    php -S localhost:8000

Then open http://localhost:8000/index.php in a browser.

Alternatively, drop the whole "php-skills-test" folder into any
PHP-enabled webserver's document root (Apache/Nginx + PHP-FPM, etc.)
and open index.php in that location. No configuration changes,
composer install, or build step is required - it works as-is.

The only requirement is that the web server process can write to
data.json (and the folder it lives in) so the app can save submissions.
On most default Apache/PHP setups this works out of the box since the
folder is extracted with normal permissions; if your host is unusually
locked down, make data.json (and its folder) writable by the web
server user.

NOTES
-----
- Data is validated server-side (product name required; quantity and
  price must be non-negative numbers).
- The table is always re-sorted by original submission datetime, so
  editing a row's name/quantity/price does not change its position in
  the list (the original submitted datetime is preserved).
- All user-supplied text is HTML-escaped before being rendered to
  prevent XSS.
- Plain PHP + vanilla JS + Bootstrap 5 (via CDN) were used instead of
  Laravel, to keep the solution dependency-free and runnable by simply
  extracting the zip onto any PHP host with no "composer install",
  ".env" setup, or artisan commands needed.
