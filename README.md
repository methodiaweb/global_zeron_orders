# Global Zeron orders (MVP)

WooCommerce **My Account** customization that shows **Zeron obligations** instead of WooCommerce orders.

## MVP scope (what works now)

- Adds a new My Account tab: **„Задължения“**
- Hides WooCommerce **Orders** tab (does not delete any data)
- Shows two visual sections:
  - **Неплатени** (top)
  - **Платени** (bottom) with **Year dropdown** (only for paid)
- Sorting: **date descending**
- PDF button appears only for **Invoice** and **Proforma** and opens in a **new tab**
- **Dummy mode** fills the UI with realistic bundled JSON
- **Live mode** shows credential fields in admin, but **does not call ERP yet** (MVP)

## Requirements

- WordPress **6.2+**
- PHP **8.1+**
- WooCommerce **8.0+**
- HTTPS recommended (required later for live ERP auth)

## Install

1. Upload the plugin folder `global-zeron-orders` into `wp-content/plugins/`
2. Activate the plugin in **Plugins**
3. Go to **Admin → Global Zeron orders** and select **Dummy**

## Settings

Admin → **Global Zeron orders**

- Mode: Dummy / Live
- Live fields (placeholders for later): Base URL, User, Password, Token

## Development notes

- Small OOP structure with namespaces + lightweight autoloader (no Composer).
- WPML-friendly: all strings are translatable (text domain: `global-zeron-orders`).

## Versioning

We use **SemVer**:

- `0.x.y` = MVP iteration
- `1.0.0` = first production-ready Live version
Test update 0.1.2
