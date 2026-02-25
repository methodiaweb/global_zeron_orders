# context.md — Global Zeron orders (MVP)

## Goal
MVP WordPress plugin that replaces WooCommerce “Orders” area in **My Account** with an obligations view coming from ERP (Zeron).

## UI rules (locked)
- My Account tab name: **„Задължения“**
- Two visual sections (one under another):
  1) **Неплатени** (top)
  2) **Платени** (bottom)
- Sorting in both sections: **by date descending**
- Paid section has **Year dropdown** (ONLY for paid)
  - Years come from a separate ERP “years nomenclature” service later
  - Default selected year = **most recent year from ERP list**
  - Year change triggers a **new API call** later (MVP uses page reload with parameter)
- Columns/fields per row:
  - Document type
  - Document number
  - Date (format from WordPress settings)
  - Amount in **EUR** only (MVP)
  - Days overdue (numeric only, no badges)
  - PDF button only for **Proforma** and **Invoice**, opens in **new tab**
- No detailed view page for a document.

## Modes
- **Dummy**: fully working UI using bundled realistic JSON (`assets/dummy-obligations.json`)
- **Live**: admin fields exist (credentials etc.) but do **nothing** yet (no ERP calls in MVP)

## Admin
- Separate top-level menu: **Global Zeron orders**
- Settings page shows Mode and Live placeholders

## WPML / i18n
- Text domain: `global-zeron-orders`
- All strings wrapped in translation functions

## Out of scope for MVP
- Real ERP API operations (Operation name, Destination, auth flow)
- User ERP Customer ID mapping UI
- Pagination / “Load more”
- Currency dual display (BGN equivalent)

## Next steps (after MVP)
1) Define ERP Operations:
   - Unpaid obligations (open)
   - Paid obligations by year (closed)
   - Available years nomenclature
   - PDF fetch for invoice/proforma
2) Implement Live provider + caching + error handling
3) Optional: rework into a bigger “Zeron” plugin module
