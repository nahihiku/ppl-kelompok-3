<x-filament-panels::page>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap');

        /* Hide default Filament chrome so our Figma Admin layout renders full-bleed without overlapping */
        .fi-topbar,
        .fi-sidebar,
        .fi-sidebar-close-overlay,
        .fi-header {
            display: none !important;
        }

        .fi-layout,
        .fi-main-ctn,
        .fi-main,
        .fi-page,
        .fi-page > section,
        .fi-page-content {
            padding: 0 !important;
            margin: 0 !important;
            max-width: 100% !important;
            width: 100% !important;
            gap: 0 !important;
        }

        body {
            background: #fafafa !important;
        }

        .casin-admin-shell {
            display: flex;
            min-height: 100vh;
            width: 100%;
            background: #fafafa;
            font-family: 'Poppins', 'Inter', ui-sans-serif, system-ui, sans-serif;
            color: #0f172b;
        }

        /* ==========================================================================
           Figma Admin Sidebar (Node 230:831 / 217:949 / 230:1549)
           ========================================================================== */
        .casin-admin-sidebar {
            width: 290px;
            min-height: 100vh;
            background: #ffffff;
            border-right: 1px solid #f1f5f9;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 28px 26px 32px;
            flex-shrink: 0;
            transition: width 0.2s ease, padding 0.2s ease;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 40;
        }

        .casin-admin-shell.is-sidebar-collapsed .casin-admin-sidebar {
            width: 86px;
            padding: 28px 14px 32px;
        }

        .casin-admin-shell.is-sidebar-collapsed .casin-brand-wordmark,
        .casin-admin-shell.is-sidebar-collapsed .casin-nav-label {
            display: none;
        }

        .casin-admin-shell.is-sidebar-collapsed .casin-sidebar-top {
            justify-content: center;
            gap: 8px;
        }

        .casin-admin-shell.is-sidebar-collapsed .casin-nav-btn,
        .casin-admin-shell.is-sidebar-collapsed .casin-logout-btn {
            justify-content: center;
            padding: 12px;
        }

        .casin-sidebar-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 44px;
        }

        .casin-brand-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .casin-brand-circle {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            padding: 2.5px;
            background: linear-gradient(135deg, #2d378e 0%, #00c2cb 100%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .casin-brand-circle-inner {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .casin-brand-circle-inner svg {
            width: 24px;
            height: 24px;
        }

        .casin-brand-wordmark {
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: -0.2px;
            line-height: 1;
            white-space: nowrap;
        }

        .casin-brand-wordmark .cas {
            color: #00c2cb;
        }

        .casin-brand-wordmark .in {
            color: #2d378e;
        }

        .casin-hamburger-btn {
            width: 36px;
            height: 36px;
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: #0f172a;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.15s ease;
        }

        .casin-hamburger-btn:hover {
            background: #f1f5f9;
        }

        .casin-hamburger-btn svg {
            width: 24px;
            height: 24px;
        }

        .casin-sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 14px;
            flex: 1;
        }

        .casin-nav-btn {
            display: flex;
            align-items: center;
            gap: 11px;
            width: 100%;
            min-height: 46px;
            padding: 10px 14px;
            border: 0;
            border-radius: 6px;
            background: transparent;
            color: rgba(0, 0, 0, 0.9);
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
            font-weight: 600;
            text-align: left;
            cursor: pointer;
            transition: background 0.15s ease, color 0.15s ease;
        }

        .casin-nav-btn svg {
            width: 21px;
            height: 21px;
            flex-shrink: 0;
            color: currentColor;
        }

        .casin-nav-btn:hover {
            background: #f0fdff;
            color: #0095a8;
        }

        .casin-nav-btn.is-active {
            background: #00c2cb;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 194, 203, 0.25);
        }

        .casin-sidebar-footer {
            padding-top: 24px;
            border-top: 1px solid #f1f5f9;
        }

        .casin-logout-form {
            margin: 0;
        }

        .casin-logout-btn {
            display: flex;
            align-items: center;
            gap: 11px;
            width: 100%;
            min-height: 44px;
            padding: 10px 14px;
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: #0f172a;
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s ease, color 0.15s ease;
        }

        .casin-logout-btn svg {
            width: 21px;
            height: 21px;
            flex-shrink: 0;
        }

        .casin-logout-btn:hover {
            background: #fef2f2;
            color: #dc2626;
        }

        /* ==========================================================================
           Main Content Area & Header
           ========================================================================== */
        .casin-admin-main {
            flex: 1;
            min-width: 0;
            padding: 34px 40px 56px;
            background: #fafafa;
        }

        .casin-view-header {
            margin-bottom: 28px;
        }

        .casin-view-title {
            margin: 0 0 18px;
            color: #2d378e;
            font-family: 'Poppins', sans-serif;
            font-size: 38px;
            font-weight: 600;
            line-height: 1.2;
            letter-spacing: -0.4px;
        }

        .casin-view-divider {
            height: 1px;
            width: 100%;
            background: #cbd5e1;
            border: 0;
            margin: 0;
        }

        /* ==========================================================================
           Dashboard View (Figma Node 230:830)
           ========================================================================== */
        .casin-stats-grid {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .casin-stat-card {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.85);
            border-radius: 12px;
            padding: 14px 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 114px;
        }

        .casin-stat-icon {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
        }

        .casin-stat-icon svg {
            width: 16px;
            height: 16px;
        }

        .casin-stat-label {
            margin: 12px 0 4px;
            color: #62748e;
            font-family: 'Inter', sans-serif;
            font-size: 11.5px;
            font-weight: 500;
            line-height: 1.3;
        }

        .casin-stat-bottom {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 8px;
        }

        .casin-stat-value {
            margin: 0;
            color: #020618;
            font-family: 'Inter', sans-serif;
            font-size: 20px;
            font-weight: 700;
            line-height: 1.2;
        }

        .casin-stat-trend {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            color: #009966;
            font-family: 'Inter', sans-serif;
            font-size: 11px;
            font-weight: 700;
        }

        .casin-stat-trend svg {
            width: 11px;
            height: 11px;
        }

        .casin-mid-grid {
            display: grid;
            grid-template-columns: 1.48fr 1fr;
            gap: 22px;
            margin-bottom: 28px;
        }

        .casin-panel-card {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.85);
            border-radius: 12px;
            padding: 20px 22px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .casin-panel-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .casin-panel-title {
            margin: 0;
            color: #0f172b;
            font-family: 'Inter', sans-serif;
            font-size: 14.5px;
            font-weight: 700;
            line-height: 1.35;
        }

        .casin-panel-sub {
            margin: 3px 0 0;
            color: #62748e;
            font-family: 'Inter', sans-serif;
            font-size: 11.5px;
            font-weight: 400;
        }

        .casin-growth-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 9999px;
            background: #ecfdf5;
            color: #007a55;
            font-family: 'Inter', sans-serif;
            font-size: 11.5px;
            font-weight: 700;
        }

        .casin-growth-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #007a55;
            opacity: 0.75;
        }

        .casin-chart-wrap {
            margin: 18px 0 10px;
            width: 100%;
            height: 180px;
        }

        .casin-chart-wrap svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        .casin-chart-months {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #90a1b9;
            font-family: 'Inter', sans-serif;
            font-size: 11.5px;
            font-weight: 600;
            padding: 0 4px;
        }

        /* Ringkasan Laporan White Card (Figma Node 230:1263) */
        .casin-report-flag-icon {
            width: 18px;
            height: 18px;
            color: #00d3f2;
            flex-shrink: 0;
        }

        .casin-report-bars {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin: 20px 0 18px;
        }

        .casin-report-row-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
            font-family: 'Inter', sans-serif;
            font-size: 12px;
            color: #0f172b;
        }

        .casin-report-row-top strong {
            font-weight: 700;
            color: #0f172b;
        }

        .casin-report-track {
            width: 100%;
            height: 7px;
            border-radius: 9999px;
            background: rgba(0, 0, 0, 0.21);
            overflow: hidden;
        }

        .casin-report-fill {
            height: 100%;
            border-radius: 9999px;
        }

        .casin-report-footer {
            display: flex;
            justify-content: center;
            padding-top: 8px;
        }

        .casin-report-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 0;
            background: transparent;
            color: #00d3f2;
            font-family: 'Inter', sans-serif;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .casin-report-link:hover {
            color: #0095a8;
        }

        .casin-report-link svg {
            width: 13px;
            height: 13px;
        }

        /* Bottom Grid: Recent Stations & Recent Reviews */
        .casin-bottom-grid {
            display: grid;
            grid-template-columns: 1.05fr 1fr;
            gap: 22px;
        }

        .casin-list-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .casin-list-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
        }

        .casin-link-blue {
            border: 0;
            background: transparent;
            color: #1447e6;
            font-family: 'Inter', sans-serif;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .casin-link-blue:hover {
            text-decoration: underline;
        }

        .casin-list-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 20px;
            border-bottom: 1px solid #f1f5f9;
        }

        .casin-list-item:last-child {
            border-bottom: 0;
        }

        .casin-station-avatar {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: #ecfeff;
            color: #0092b8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .casin-station-avatar svg {
            width: 17px;
            height: 17px;
        }

        .casin-item-body {
            flex: 1;
            min-width: 0;
        }

        .casin-item-title {
            margin: 0;
            color: #0f172b;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .casin-item-sub {
            margin: 2px 0 0;
            color: #90a1b9;
            font-family: 'Inter', sans-serif;
            font-size: 11.5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .casin-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 9999px;
            font-family: 'Inter', sans-serif;
            font-size: 11px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .casin-status-pill.is-active {
            background: #ecfdf5;
            color: #007a55;
        }

        .casin-status-pill.is-inactive {
            background: #fff1f2;
            color: #c70036;
        }

        .casin-status-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: currentColor;
            opacity: 0.75;
        }

        .casin-review-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #eff6ff;
            color: #1447e6;
            font-family: 'Inter', sans-serif;
            font-size: 11px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .casin-review-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .casin-review-date {
            color: #90a1b9;
            font-family: 'Inter', sans-serif;
            font-size: 11px;
            flex-shrink: 0;
        }

        .casin-stars {
            display: inline-flex;
            align-items: center;
            gap: 2px;
            margin-top: 4px;
        }

        .casin-stars svg {
            width: 11px;
            height: 11px;
        }

        /* ==========================================================================
           Data Stasiun & Data Kendaraan Views (Figma Nodes 217:948 & 230:1549)
           ========================================================================== */
        .casin-data-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 28px;
        }

        .casin-search-filter-group {
            display: flex;
            align-items: center;
            gap: 14px;
            flex: 1;
            max-width: 780px;
        }

        .casin-pill-search {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            height: 54px;
            padding: 0 20px;
            background: #ffffff;
            border: 1px solid #000000;
            border-radius: 19px;
        }

        .casin-pill-search svg {
            width: 24px;
            height: 24px;
            color: #0f172a;
            flex-shrink: 0;
        }

        .casin-pill-search input {
            width: 100%;
            border: 0;
            outline: none;
            background: transparent;
            color: #000000;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            font-weight: 400;
        }

        .casin-pill-search input::placeholder {
            color: #1e293b;
        }

        .casin-filter-square-btn {
            width: 54px;
            height: 54px;
            border: 1px solid #000000;
            border-radius: 10px;
            background: #ffffff;
            color: #000000;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
            transition: background 0.15s ease;
        }

        .casin-filter-square-btn:hover {
            background: #f1f5f9;
        }

        .casin-filter-square-btn svg {
            width: 24px;
            height: 24px;
        }

        .casin-btn-tambah-data {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            height: 54px;
            padding: 0 22px;
            border: 0;
            border-radius: 6px;
            background: linear-gradient(90deg, #2d378e 0%, #00c2cb 100%);
            color: #ffffff;
            font-family: 'Poppins', sans-serif;
            font-size: 15.5px;
            font-weight: 500;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(45, 55, 142, 0.2);
            transition: transform 0.15s ease, opacity 0.15s ease;
        }

        .casin-btn-tambah-data:hover {
            transform: translateY(-1px);
            opacity: 0.96;
        }

        .casin-btn-tambah-data svg {
            width: 16px;
            height: 16px;
        }

        .casin-table-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 2px 12px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .casin-table-scroll {
            width: 100%;
            overflow-x: auto;
        }

        .casin-admin-table {
            width: 100%;
            border-collapse: collapse;
            font-family: 'Poppins', sans-serif;
        }

        .casin-admin-table thead tr {
            background: rgba(217, 217, 217, 0.65);
            height: 66px;
        }

        .casin-admin-table th {
            padding: 14px 16px;
            color: #000000;
            font-size: 13.5px;
            font-weight: 600;
            text-transform: uppercase;
            text-align: left;
            white-space: nowrap;
        }

        .casin-admin-table td {
            padding: 18px 16px;
            color: #000000;
            font-size: 13.5px;
            font-weight: 400;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .casin-admin-table th.is-center,
        .casin-admin-table td.is-center {
            text-align: center;
        }

        .casin-vehicle-type-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 9999px;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
        }

        .casin-vehicle-type-badge.is-car {
            background: #eff6ff;
            color: #1447e6;
        }

        .casin-vehicle-type-badge.is-motor {
            background: #d0e9d1;
            color: #3c9840;
        }

        .casin-vehicle-type-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: currentColor;
            opacity: 0.75;
        }

        .casin-aksi-group {
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .casin-aksi-btn {
            width: 30px;
            height: 30px;
            border: 0;
            border-radius: 5px;
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.12s ease, opacity 0.12s ease;
        }

        .casin-aksi-btn:hover {
            transform: translateY(-1px);
            opacity: 0.92;
        }

        .casin-aksi-btn svg {
            width: 16px;
            height: 16px;
        }

        .casin-aksi-view {
            background: #1976d2;
        }

        .casin-aksi-edit {
            background: #ffc107;
        }

        .casin-aksi-delete {
            background: #ff3d00;
        }

        .casin-table-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 24px;
            font-family: 'Inter', sans-serif;
            font-size: 12px;
            color: #62748e;
        }

        .casin-table-footer strong {
            color: #1d293d;
            font-weight: 700;
        }

        .casin-pagination {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .casin-page-btn {
            padding: 7px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 9px;
            background: #ffffff;
            color: #314158;
            font-family: 'Inter', sans-serif;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
        }

        .casin-page-btn.is-disabled {
            opacity: 0.5;
            cursor: default;
        }

        .casin-page-indicator {
            min-width: 28px;
            height: 28px;
            padding: 0 8px;
            border-radius: 6px;
            background: linear-gradient(180deg, #1447e6 0%, #00c2cb 100%);
            color: #ffffff;
            font-family: 'Inter', sans-serif;
            font-size: 11px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* ==========================================================================
           Figma Admin Modals: Tambah / Edit Data Stasiun & Kendaraan
           (Nodes 227:936, 227:1833, 236:1873, 236:2177)
           ========================================================================== */
        .casin-admin-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 100;
            background: rgba(217, 217, 217, 0.52);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .casin-admin-modal-backdrop[hidden] {
            display: none !important;
        }

        .casin-admin-modal-card {
            width: min(100%, 960px);
            max-height: calc(100vh - 48px);
            overflow-y: auto;
            background: #ffffff;
            border-radius: 6px;
            padding: 36px 46px 42px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
            font-family: 'Poppins', sans-serif;
        }

        .casin-modal-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 28px;
        }

        .casin-modal-heading {
            margin: 0;
            color: #000000;
            font-size: 26px;
            font-weight: 500;
            line-height: 1.3;
        }

        .casin-modal-close {
            width: 34px;
            height: 34px;
            border: 0;
            background: transparent;
            color: #000000;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .casin-modal-close svg {
            width: 26px;
            height: 26px;
        }

        .casin-modal-form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .casin-modal-field label {
            display: block;
            margin-bottom: 8px;
            color: #000000;
            font-size: 16px;
            font-weight: 500;
        }

        .casin-pill-input,
        .casin-pill-select {
            width: 100%;
            height: 50px;
            padding: 0 22px;
            border: 1px solid #000000;
            border-radius: 70px;
            background: #ffffff;
            color: #000000;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            font-weight: 400;
            outline: none;
        }

        .casin-pill-input:focus,
        .casin-pill-select:focus {
            border-color: #00c2cb;
            box-shadow: 0 0 0 3px rgba(0, 194, 203, 0.15);
        }

        .casin-modal-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 22px;
        }

        .casin-modal-row-4 {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }

        .casin-modal-submit-btn {
            margin-top: 12px;
            width: 100%;
            height: 58px;
            border: 0;
            border-radius: 10px;
            background: linear-gradient(90deg, #2d378e 0%, #00c2cb 100%);
            color: #ffffff;
            font-family: 'Poppins', sans-serif;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.15s ease, transform 0.15s ease;
        }

        .casin-modal-submit-btn:hover {
            opacity: 0.96;
            transform: translateY(-1px);
        }

        @media (max-width: 1280px) {
            .casin-stats-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .casin-mid-grid,
            .casin-bottom-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .casin-admin-shell {
                flex-direction: column;
            }

            .casin-admin-sidebar {
                width: 100% !important;
                height: auto;
                min-height: auto;
                position: relative;
                padding: 18px 20px;
            }

            .casin-sidebar-nav {
                flex-direction: row;
                flex-wrap: wrap;
                gap: 8px;
            }

            .casin-nav-btn {
                width: auto;
                font-size: 14px;
                min-height: 40px;
            }

            .casin-admin-main {
                padding: 24px 20px 40px;
            }

            .casin-modal-row-2,
            .casin-modal-row-4 {
                grid-template-columns: 1fr;
            }
        }

        .casin-toast-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 13px 18px;
            margin-bottom: 22px;
            border-radius: 12px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            font-size: 13px;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(5, 150, 105, 0.08);
        }

        .casin-toast-banner.is-error {
            background: #fef2f2;
            border-color: #fecaca;
            color: #991b1b;
        }

        .casin-toast-banner[hidden] {
            display: none !important;
        }

        .casin-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }

        .casin-badge-pill.is-green {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        .casin-badge-pill.is-amber {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .casin-badge-pill.is-red {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .casin-badge-pill.is-blue {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }

        .casin-badge-pill.is-slate {
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
        }

        .casin-inline-select {
            height: 32px;
            padding: 0 10px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            color: #0f172a;
            font-family: 'Inter', sans-serif;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }

        .casin-role-btn {
            padding: 5px 11px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #1e293b;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s ease, border-color 0.15s ease;
        }

        .casin-role-btn:hover:not(:disabled) {
            background: #f0fdff;
            border-color: #00c2cb;
            color: #007580;
        }

        .casin-role-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .casin-modal-error {
            padding: 10px 14px;
            border-radius: 10px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            font-size: 13px;
            font-weight: 500;
        }

        .casin-modal-error[hidden] {
            display: none !important;
        }

        .casin-profile-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 32px;
            max-width: 720px;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
        }

        .casin-filter-select {
            height: 44px;
            padding: 0 14px;
            border: 1px solid #e2e8f0;
            border-radius: 9999px;
            background: #ffffff;
            color: #0f172a;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 500;
            outline: none;
            cursor: pointer;
        }
    </style>

    <div class="casin-admin-shell" id="casin-admin-shell">
        {{-- Left Sidebar (Figma Nodes 230:831 / 217:949 / 230:1549) --}}
        <aside class="casin-admin-sidebar" aria-label="Navigasi Admin CasIn.id">
            <div>
                <div class="casin-sidebar-top">
                    <a href="{{ route('home') }}" class="casin-brand-link" aria-label="CasIn.id Beranda">
                        <span class="casin-brand-circle" aria-hidden="true">
                            <span class="casin-brand-circle-inner">
                                <svg viewBox="0 0 40 40" fill="none">
                                    <rect x="9" y="8" width="15" height="24" rx="2.5" fill="#2D378E"/>
                                    <rect x="12" y="11" width="9" height="5" rx="1" fill="#FFFFFF"/>
                                    <path d="M17.2 18.5L13.5 23.5H16.5L15.8 27.5L19.5 22.5H16.5L17.2 18.5Z" fill="#00C2CB"/>
                                    <path d="M24 14H26.5C27.6046 14 28.5 14.8954 28.5 16V24.5C28.5 25.8807 29.6193 27 31 27C32.3807 27 33.5 25.8807 33.5 24.5V15.5L30.5 12.5" stroke="#2D378E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M7.5 32H25.5" stroke="#2D378E" stroke-width="2.4" stroke-linecap="round"/>
                                </svg>
                            </span>
                        </span>
                        <span class="casin-brand-wordmark">
                            <span class="cas">Cas</span><span class="in">In.id</span>
                        </span>
                    </a>

                    <button type="button" class="casin-hamburger-btn" data-admin-sidebar-toggle aria-label="Ciutkan atau perluas sidebar">
                        <svg viewBox="0 0 25 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M3 6h19M3 12h19M3 18h19"/>
                        </svg>
                    </button>
                </div>

                <nav class="casin-sidebar-nav">
                    <button type="button" class="casin-nav-btn is-active" data-admin-nav="dashboard">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                            <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                            <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                            <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                        </svg>
                        <span class="casin-nav-label">Dashboard</span>
                    </button>

                    <button type="button" class="casin-nav-btn" data-admin-nav="users">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <span class="casin-nav-label">Data User</span>
                    </button>

                    <button type="button" class="casin-nav-btn" data-admin-nav="stations">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="5" y="3" width="10" height="18" rx="2"/>
                            <path d="M15 8h2a2 2 0 0 1 2 2v6a2 2 0 0 0 2 2"/>
                            <path d="M10 10l-2 3h4l-2 3"/>
                            <path d="M3 21h14"/>
                        </svg>
                        <span class="casin-nav-label">Data Stasiun</span>
                    </button>

                    <button type="button" class="casin-nav-btn" data-admin-nav="vehicles">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/>
                            <circle cx="7" cy="17" r="2"/>
                            <path d="M9 17h6"/>
                            <circle cx="17" cy="17" r="2"/>
                        </svg>
                        <span class="casin-nav-label">Data Kendaraan</span>
                    </button>

                    <button type="button" class="casin-nav-btn" data-admin-nav="connectors">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22v-5"/>
                            <path d="M9 8V2"/>
                            <path d="M15 8V2"/>
                            <path d="M18 8v5a4 4 0 0 1-4 4h-4a4 4 0 0 1-4-4V8Z"/>
                        </svg>
                        <span class="casin-nav-label">Data Konektor</span>
                    </button>

                    <button type="button" class="casin-nav-btn" data-admin-nav="reports">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/>
                            <line x1="4" x2="4" y1="22" y2="15"/>
                        </svg>
                        <span class="casin-nav-label">Data Laporan</span>
                    </button>

                    <button type="button" class="casin-nav-btn" data-admin-nav="reviews">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            <line x1="8" x2="16" y1="9" y2="9"/>
                            <line x1="8" x2="13" y1="13" y2="13"/>
                        </svg>
                        <span class="casin-nav-label">Data Ulasan</span>
                    </button>

                    <button type="button" class="casin-nav-btn" data-admin-nav="profile">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        <span class="casin-nav-label">Profil Admin</span>
                    </button>
                </nav>
            </div>

            <div class="casin-sidebar-footer">
                <form method="POST" action="{{ url('/admin/logout') }}" class="casin-logout-form">
                    @csrf
                    <button type="submit" class="casin-logout-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" x2="9" y1="12" y2="12"/>
                        </svg>
                        <span class="casin-nav-label">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Right Main Content Area --}}
        <main class="casin-admin-main">
            <div class="casin-toast-banner" id="casin-admin-toast" hidden role="status" aria-live="polite">
                <span data-toast-message></span>
                <button type="button" class="casin-modal-close" style="width: 24px; height: 24px;" data-close-toast aria-label="Tutup notifikasi">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- ==============================================================
               VIEW 1: DASHBOARD ADMIN (Figma Node 230:830)
               ============================================================== --}}
            <section data-admin-section="dashboard">
                <header class="casin-view-header">
                    <h1 class="casin-view-title">Dashboard</h1>
                    <span class="sr-only">Dashboard Overview - Ringkasan performa dan aktivitas terbaru platform CasIn.id. Tambah Stasiun</span>
                    <hr class="casin-view-divider">
                </header>

                {{-- 6 Stat Cards --}}
                <div class="casin-stats-grid">
                    @foreach ($stats as $stat)
                        <article class="casin-stat-card">
                            <div class="casin-stat-icon" style="background: {{ $stat['gradient'] }};">
                                @switch($stat['icon'])
                                    @case('users')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <circle cx="9" cy="7" r="4"/>
                                            <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        @break
                                    @case('stations')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="5" y="3" width="10" height="18" rx="2"/>
                                            <path d="M15 8h2a2 2 0 0 1 2 2v6a2 2 0 0 0 2 2"/>
                                        </svg>
                                        @break
                                    @case('vehicles')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M5 17h14v-5l-2-5H7l-2 5v5Z" stroke-linecap="round" stroke-linejoin="round"/>
                                            <circle cx="7.5" cy="17.5" r="1.5"/>
                                            <circle cx="16.5" cy="17.5" r="1.5"/>
                                        </svg>
                                        @break
                                    @case('reports')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z" stroke-linecap="round" stroke-linejoin="round"/>
                                            <line x1="4" x2="4" y1="22" y2="15"/>
                                        </svg>
                                        @break
                                    @case('reviews')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        @break
                                    @default
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                @endswitch
                            </div>

                            <p class="casin-stat-label">{{ $stat['label'] }}</p>

                            <div class="casin-stat-bottom">
                                <p class="casin-stat-value">{{ $stat['value'] }}</p>
                                <span class="casin-stat-trend">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <path d="M7 17L17 7M17 7H9M17 7V15" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    {{ $stat['trend'] }}
                                </span>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Middle Row: Pertumbuhan Pengguna & Ringkasan Laporan (White Card per Figma 230:1263) --}}
                <div class="casin-mid-grid">
                    <article class="casin-panel-card">
                        <div class="casin-panel-top">
                            <div>
                                <h2 class="casin-panel-title">Pertumbuhan pengguna</h2>
                                <p class="casin-panel-sub">Pengguna baru dalam 7 bulan terakhir</p>
                            </div>
                            <span class="casin-growth-badge">
                                <span class="casin-growth-dot"></span>
                                {{ $growthBadge }}
                            </span>
                        </div>

                        <div class="casin-chart-wrap" aria-hidden="true">
                            <svg viewBox="0 0 680 180" preserveAspectRatio="none">
                                <defs>
                                    <linearGradient id="casinGrowthArea" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#00b8db" stop-opacity="0.28"/>
                                        <stop offset="100%" stop-color="#00b8db" stop-opacity="0.0"/>
                                    </linearGradient>
                                </defs>
                                <line x1="10" y1="35" x2="670" y2="35" stroke="#f1f5f9" stroke-width="1"/>
                                <line x1="10" y1="80" x2="670" y2="80" stroke="#f1f5f9" stroke-width="1"/>
                                <line x1="10" y1="125" x2="670" y2="125" stroke="#f1f5f9" stroke-width="1"/>
                                <line x1="10" y1="165" x2="670" y2="165" stroke="#f1f5f9" stroke-width="1"/>

                                <path d="{{ $growthAreaPath }}" fill="url(#casinGrowthArea)"/>
                                <path d="{{ $growthLinePath }}" fill="none" stroke="#0092b8" stroke-width="3" stroke-linecap="round"/>
                            </svg>
                        </div>

                        <div class="casin-chart-months">
                            @foreach ($growthMonths as $month)
                                <span>{{ $month }}</span>
                            @endforeach
                        </div>
                    </article>

                    <article class="casin-panel-card">
                        <div>
                            <div class="casin-panel-top">
                                <div>
                                    <h2 class="casin-panel-title">Ringkasan laporan</h2>
                                    <p class="casin-panel-sub">Status laporan komunitas</p>
                                </div>
                                <svg class="casin-report-flag-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z" stroke-linecap="round" stroke-linejoin="round"/>
                                    <line x1="4" x2="4" y1="22" y2="15"/>
                                </svg>
                            </div>

                            <div class="casin-report-bars">
                                @foreach ($reportSummary as $item)
                                    <div>
                                        <div class="casin-report-row-top">
                                            <span>{{ $item['label'] }}</span>
                                            <strong>{{ $item['count'] }}</strong>
                                        </div>
                                        <div class="casin-report-track">
                                            <div class="casin-report-fill" style="width: {{ $item['percentage'] }}%; background: {{ $item['color'] }};"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="casin-report-footer">
                            <button type="button" class="casin-report-link" data-admin-switch-nav="reports">
                                <span>Kelola semua laporan</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <path d="M9 18l6-6-6-6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </article>
                </div>

                {{-- Bottom Row: Stasiun Terbaru & Aktivitas Ulasan Terbaru --}}
                <div class="casin-bottom-grid">
                    <article class="casin-list-card">
                        <div class="casin-list-header">
                            <div>
                                <h2 class="casin-panel-title">Stasiun terbaru</h2>
                                <p class="casin-panel-sub">Lokasi yang baru ditambahkan</p>
                            </div>
                            <button type="button" class="casin-link-blue" data-admin-switch-nav="stations">Lihat semua</button>
                        </div>

                        <div>
                            @foreach ($recentStations as $station)
                                <div class="casin-list-item">
                                    <div class="casin-station-avatar">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="5" y="3" width="10" height="18" rx="2"/>
                                            <path d="M15 8h2a2 2 0 0 1 2 2v6a2 2 0 0 0 2 2"/>
                                        </svg>
                                    </div>
                                    <div class="casin-item-body">
                                        <p class="casin-item-title">{{ $station['name'] }}</p>
                                        <p class="casin-item-sub">{{ $station['address'] }}</p>
                                    </div>
                                    <span class="casin-status-pill {{ $station['is_active'] ? 'is-active' : 'is-inactive' }}">
                                        <span class="casin-status-dot"></span>
                                        {{ $station['is_active'] ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </article>

                    <article class="casin-list-card">
                        <div class="casin-list-header">
                            <div>
                                <h2 class="casin-panel-title">Aktivitas &amp; ulasan terbaru</h2>
                                <p class="casin-panel-sub">Pembaruan dari pengguna CasIn.id</p>
                            </div>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0092b8" stroke-width="2">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>

                        <div>
                            @foreach ($recentReviews as $review)
                                <div class="casin-list-item">
                                    <div class="casin-review-avatar">{{ $review['initials'] }}</div>
                                    <div class="casin-item-body">
                                        <div class="casin-review-top">
                                            <p class="casin-item-title" style="font-size: 12px;">{{ $review['title'] }}</p>
                                            <span class="casin-review-date">{{ $review['date'] }}</span>
                                        </div>
                                        <div class="casin-stars" aria-label="Rating {{ $review['rating'] }} dari 5">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg viewBox="0 0 20 20" fill="{{ $i <= $review['rating'] ? '#FFB900' : '#E2E8F0' }}">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </article>
                </div>
            </section>

            {{-- ==============================================================
               VIEW 2: DATA STASIUN (Terhubung Database)
               ============================================================== --}}
            <section data-admin-section="stations" hidden>
                <header class="casin-view-header">
                    <h1 class="casin-view-title">Data Stasiun</h1>
                    <hr class="casin-view-divider">
                </header>

                <div class="casin-data-toolbar">
                    <div class="casin-search-filter-group">
                        <div class="casin-pill-search">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="7"/>
                                <path d="M20 20l-3.5-3.5" stroke-linecap="round"/>
                            </svg>
                            <input type="search" placeholder="Cari nama stasiun atau lokasi...." data-admin-station-search>
                        </div>

                        <select class="casin-filter-select" data-admin-station-operator aria-label="Filter Tipe Operator">
                            <option value="">Semua Operator</option>
                            <option value="government">PLN</option>
                            <option value="private">Swasta</option>
                            <option value="commercial">Komersial</option>
                        </select>

                        <select class="casin-filter-select" data-admin-station-status aria-label="Filter Status Stasiun">
                            <option value="">Semua Status</option>
                            <option value="normal">Normal</option>
                            <option value="busy">Antrean Padat</option>
                            <option value="technical_issue">Kendala Teknis</option>
                        </select>
                    </div>

                    <button type="button" class="casin-btn-tambah-data" data-open-station-modal="create">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                        <span>Tambah Data</span>
                    </button>
                </div>

                <div class="casin-table-card">
                    <div class="casin-table-scroll">
                        <table class="casin-admin-table" data-admin-stations-table>
                            <thead>
                                <tr>
                                    <th class="is-center" style="width: 64px;">NO.</th>
                                    <th>NAMA STASIUN</th>
                                    <th class="is-center">TIPE OPERATOR</th>
                                    <th>ALAMAT</th>
                                    <th>TIPE KONEKTOR</th>
                                    <th>TARIF/KWH</th>
                                    <th>JAM OPERASIONAL</th>
                                    <th class="is-center">STATUS</th>
                                    <th class="is-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody data-admin-stations-tbody>
                                @forelse ($stationRows as $idx => $row)
                                    <tr
                                        data-station-row
                                        data-row-id="{{ $row['id'] }}"
                                        data-row-name="{{ $row['name'] }}"
                                        data-row-operator="{{ $row['operator'] }}"
                                        data-row-operator-type="{{ $row['operator_type'] }}"
                                        data-row-address="{{ $row['address'] }}"
                                        data-row-connectors="{{ $row['connectors'] }}"
                                        data-row-tariff="{{ $row['tariff'] }}"
                                        data-row-price="{{ $row['price_per_kwh'] }}"
                                        data-row-hours="{{ $row['hours'] }}"
                                        data-row-lat="{{ $row['latitude'] }}"
                                        data-row-lng="{{ $row['longitude'] }}"
                                        data-row-app="{{ $row['app_name'] }}"
                                        data-row-status="{{ $row['status'] }}"
                                        data-row-current-status="{{ $row['current_status'] }}"
                                    >
                                        <td class="is-center" data-row-index>{{ $idx + 1 }}</td>
                                        <td style="font-weight: 500;">{{ $row['name'] }}</td>
                                        <td class="is-center">
                                            <span class="casin-badge-pill {{ $row['operator_type'] === 'government' ? 'is-blue' : ($row['operator_type'] === 'private' ? 'is-amber' : 'is-slate') }}">
                                                {{ $row['operator'] }}
                                            </span>
                                        </td>
                                        <td>{{ $row['address'] }}</td>
                                        <td>{{ $row['connectors'] }}</td>
                                        <td>{{ $row['tariff'] }}</td>
                                        <td>{{ $row['hours'] }}</td>
                                        <td class="is-center">
                                            <span class="casin-badge-pill {{ $row['current_status'] === 'normal' ? 'is-green' : ($row['current_status'] === 'busy' ? 'is-amber' : 'is-red') }}">
                                                {{ $row['status'] }}
                                            </span>
                                        </td>
                                        <td class="is-center">
                                            <div class="casin-aksi-group">
                                                <a href="{{ route('stations.show', $row['id']) }}" class="casin-aksi-btn casin-aksi-view" title="Lihat Stasiun">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                        <circle cx="12" cy="12" r="3"/>
                                                    </svg>
                                                </a>
                                                <button type="button" class="casin-aksi-btn casin-aksi-edit" data-edit-station-row title="Edit Data Stasiun">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="casin-aksi-btn casin-aksi-delete" data-delete-station-row title="Hapus Data Stasiun">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="3 6 5 6 21 6"/>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr data-empty-row>
                                        <td colspan="9" class="is-center" style="color: #64748b; padding: 32px;">Belum ada data stasiun di basis data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="casin-table-footer">
                        <span>Menampilkan <strong data-stations-table-count>{{ $stationsTotalCount }}</strong> data</span>
                        <div class="casin-pagination">
                            <button type="button" class="casin-page-btn is-disabled" data-page-prev="stations">Sebelumnya</button>
                            <span class="casin-page-indicator" data-page-indicator="stations">1/{{ max(1, (int) ceil($stationsTotalCount / 10)) }}</span>
                            <button type="button" class="casin-page-btn {{ $stationsTotalCount <= 10 ? 'is-disabled' : '' }}" data-page-next="stations">Berikutnya</button>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ==============================================================
               VIEW 3: DATA KENDARAAN (Terhubung Database)
               ============================================================== --}}
            <section data-admin-section="vehicles" hidden>
                <header class="casin-view-header">
                    <h1 class="casin-view-title">Data Kendaraan</h1>
                    <hr class="casin-view-divider">
                </header>

                <div class="casin-data-toolbar">
                    <div class="casin-search-filter-group">
                        <div class="casin-pill-search">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="7"/>
                                <path d="M20 20l-3.5-3.5" stroke-linecap="round"/>
                            </svg>
                            <input type="search" placeholder="Cari nama kendaraan" data-admin-vehicle-search>
                        </div>

                        <select class="casin-filter-select" data-admin-vehicle-category aria-label="Filter Jenis Kendaraan">
                            <option value="">Semua Jenis</option>
                            <option value="Mobil Listrik">Mobil Listrik</option>
                            <option value="Motor Listrik">Motor Listrik</option>
                        </select>
                    </div>

                    <button type="button" class="casin-btn-tambah-data" data-open-vehicle-modal="create">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                        <span>Tambah Data</span>
                    </button>
                </div>

                <div class="casin-table-card">
                    <div class="casin-table-scroll">
                        <table class="casin-admin-table" data-admin-vehicles-table>
                            <thead>
                                <tr>
                                    <th class="is-center" style="width: 64px;">NO.</th>
                                    <th class="is-center">MEREK KENDARAAN</th>
                                    <th class="is-center">MODEL KENDARAAN</th>
                                    <th>JENIS KENDARAAN</th>
                                    <th>KAPASITAS BATERAI</th>
                                    <th>TIPE KONEKTOR</th>
                                    <th class="is-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody data-admin-vehicles-tbody>
                                @forelse ($vehicleRows as $idx => $vRow)
                                    <tr
                                        data-vehicle-row
                                        data-row-id="{{ $vRow['id'] }}"
                                        data-row-brand="{{ $vRow['brand'] }}"
                                        data-row-model="{{ $vRow['model'] }}"
                                        data-row-category="{{ $vRow['category'] }}"
                                        data-row-battery="{{ $vRow['battery'] }}"
                                        data-row-battery-kwh="{{ $vRow['battery_kwh'] }}"
                                        data-row-connector="{{ $vRow['connector'] }}"
                                    >
                                        <td class="is-center" data-row-index>{{ $idx + 1 }}</td>
                                        <td class="is-center" style="font-weight: 500;">{{ $vRow['brand'] }}</td>
                                        <td class="is-center">{{ $vRow['model'] }}</td>
                                        <td>
                                            <span class="casin-vehicle-type-badge {{ $vRow['category'] === 'Motor Listrik' ? 'is-motor' : 'is-car' }}">
                                                <span class="casin-vehicle-type-dot"></span>
                                                {{ $vRow['category'] }}
                                            </span>
                                        </td>
                                        <td>{{ $vRow['battery'] }}</td>
                                        <td>{{ $vRow['connector'] }}</td>
                                        <td class="is-center">
                                            <div class="casin-aksi-group">
                                                <button type="button" class="casin-aksi-btn casin-aksi-view" data-edit-vehicle-row title="Lihat Detail Kendaraan">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                        <circle cx="12" cy="12" r="3"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="casin-aksi-btn casin-aksi-edit" data-edit-vehicle-row title="Edit Data Kendaraan">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="casin-aksi-btn casin-aksi-delete" data-delete-vehicle-row title="Hapus Data Kendaraan">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="3 6 5 6 21 6"/>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr data-empty-row>
                                        <td colspan="7" class="is-center" style="color: #64748b; padding: 32px;">Belum ada data kendaraan di basis data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="casin-table-footer">
                        <span>Menampilkan <strong data-vehicles-table-count>{{ count($vehicleRows) }}</strong> data</span>
                        <div class="casin-pagination">
                            <button type="button" class="casin-page-btn is-disabled" data-page-prev="vehicles">Sebelumnya</button>
                            <span class="casin-page-indicator" data-page-indicator="vehicles">1/1</span>
                            <button type="button" class="casin-page-btn is-disabled" data-page-next="vehicles">Berikutnya</button>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ==============================================================
               VIEW 4: DATA USER (Terhubung Database)
               ============================================================== --}}
            <section data-admin-section="users" hidden>
                <header class="casin-view-header">
                    <h1 class="casin-view-title">Data User</h1>
                    <hr class="casin-view-divider">
                </header>

                <div class="casin-data-toolbar">
                    <div class="casin-search-filter-group">
                        <div class="casin-pill-search">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="7"/>
                                <path d="M20 20l-3.5-3.5" stroke-linecap="round"/>
                            </svg>
                            <input type="search" placeholder="Cari nama atau email pengguna..." data-admin-user-search>
                        </div>

                        <select class="casin-filter-select" data-admin-user-role aria-label="Filter Peran Pengguna">
                            <option value="">Semua Peran</option>
                            <option value="admin">Admin</option>
                            <option value="user">Pengguna</option>
                        </select>
                    </div>
                </div>

                <div class="casin-table-card">
                    <div class="casin-table-scroll">
                        <table class="casin-admin-table" data-admin-users-table>
                            <thead>
                                <tr>
                                    <th class="is-center" style="width: 64px;">NO.</th>
                                    <th>NAMA</th>
                                    <th>EMAIL</th>
                                    <th>NO. HP</th>
                                    <th class="is-center">PERAN</th>
                                    <th>BERGABUNG</th>
                                    <th class="is-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody data-admin-users-tbody>
                                @forelse ($userRows as $idx => $uRow)
                                    @php $isSelf = ($adminId && (int) $uRow['id'] === (int) $adminId); @endphp
                                    <tr
                                        data-user-row
                                        data-row-id="{{ $uRow['id'] }}"
                                        data-row-role="{{ $uRow['role'] }}"
                                    >
                                        <td class="is-center" data-row-index>{{ $idx + 1 }}</td>
                                        <td style="font-weight: 500;">{{ $uRow['name'] }}</td>
                                        <td>{{ $uRow['email'] }}</td>
                                        <td>{{ $uRow['phone'] }}</td>
                                        <td class="is-center">
                                            <span class="casin-badge-pill {{ $uRow['role'] === 'admin' ? 'is-blue' : 'is-slate' }}">
                                                {{ $uRow['role'] === 'admin' ? 'Admin' : 'Pengguna' }}
                                            </span>
                                        </td>
                                        <td>{{ $uRow['joined_at'] }}</td>
                                        <td class="is-center">
                                            <div class="casin-aksi-group">
                                                <button
                                                    type="button"
                                                    class="casin-role-btn"
                                                    data-toggle-user-role
                                                    data-user-id="{{ $uRow['id'] }}"
                                                    data-next-role="{{ $uRow['role'] === 'admin' ? 'user' : 'admin' }}"
                                                    @disabled($isSelf)
                                                    title="{{ $isSelf ? 'Akun Anda sedang aktif' : ($uRow['role'] === 'admin' ? 'Ubah menjadi Pengguna' : 'Jadikan Admin') }}"
                                                >
                                                    {{ $isSelf ? 'Akun Aktif' : ($uRow['role'] === 'admin' ? 'Turunkan User' : 'Jadikan Admin') }}
                                                </button>
                                                <button
                                                    type="button"
                                                    class="casin-aksi-btn casin-aksi-delete"
                                                    data-delete-user-row
                                                    data-user-id="{{ $uRow['id'] }}"
                                                    data-user-name="{{ $uRow['name'] }}"
                                                    @disabled($isSelf)
                                                    style="{{ $isSelf ? 'opacity: 0.4; cursor: not-allowed;' : '' }}"
                                                    title="{{ $isSelf ? 'Tidak dapat menghapus akun sendiri' : 'Hapus Pengguna' }}"
                                                >
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="3 6 5 6 21 6"/>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr data-empty-row>
                                        <td colspan="7" class="is-center" style="color: #64748b; padding: 32px;">Belum ada data pengguna.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="casin-table-footer">
                        <span>Menampilkan <strong data-users-table-count>{{ count($userRows) }}</strong> data</span>
                        <div class="casin-pagination">
                            <button type="button" class="casin-page-btn is-disabled" data-page-prev="users">Sebelumnya</button>
                            <span class="casin-page-indicator" data-page-indicator="users">1/1</span>
                            <button type="button" class="casin-page-btn is-disabled" data-page-next="users">Berikutnya</button>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ==============================================================
               VIEW 5: DATA KONEKTOR (Terhubung Database)
               ============================================================== --}}
            <section data-admin-section="connectors" hidden>
                <header class="casin-view-header">
                    <h1 class="casin-view-title">Data Konektor</h1>
                    <hr class="casin-view-divider">
                </header>

                <div class="casin-data-toolbar">
                    <div class="casin-search-filter-group">
                        <div class="casin-pill-search">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="7"/>
                                <path d="M20 20l-3.5-3.5" stroke-linecap="round"/>
                            </svg>
                            <input type="search" placeholder="Cari nama stasiun atau tipe konektor..." data-admin-connector-search>
                        </div>

                        <select class="casin-filter-select" data-admin-connector-type aria-label="Filter Tipe Konektor">
                            <option value="">Semua Konektor</option>
                            <option value="CCS2">CCS2</option>
                            <option value="AC Type 2">AC Type 2</option>
                            <option value="DC Connector">DC Connector</option>
                            <option value="AC Connector">AC Connector</option>
                            <option value="CHAdeMO">CHAdeMO</option>
                        </select>
                    </div>

                    <button type="button" class="casin-btn-tambah-data" data-open-connector-modal="create">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                        <span>Tambah Data</span>
                    </button>
                </div>

                <div class="casin-table-card">
                    <div class="casin-table-scroll">
                        <table class="casin-admin-table" data-admin-connectors-table>
                            <thead>
                                <tr>
                                    <th class="is-center" style="width: 64px;">NO.</th>
                                    <th>NAMA STASIUN</th>
                                    <th>TIPE KONEKTOR</th>
                                    <th class="is-center">DAYA</th>
                                    <th class="is-center">KECEPATAN</th>
                                    <th class="is-center">ARUS</th>
                                    <th class="is-center">TERSEDIA / TOTAL</th>
                                    <th class="is-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody data-admin-connectors-tbody>
                                @forelse ($connectorRows as $idx => $cRow)
                                    <tr
                                        data-connector-row
                                        data-row-id="{{ $cRow['id'] }}"
                                        data-row-station-id="{{ $cRow['station_id'] }}"
                                        data-row-station-name="{{ $cRow['station_name'] }}"
                                        data-row-connector-type="{{ $cRow['connector_type'] }}"
                                        data-row-power-kw="{{ $cRow['power_kw'] }}"
                                        data-row-speed="{{ $cRow['charging_speed_category'] }}"
                                        data-row-current-type="{{ $cRow['current_type'] }}"
                                        data-row-available="{{ $cRow['available_connectors'] }}"
                                        data-row-total="{{ $cRow['total_connectors'] }}"
                                    >
                                        <td class="is-center" data-row-index>{{ $idx + 1 }}</td>
                                        <td style="font-weight: 500;">{{ $cRow['station_name'] }}</td>
                                        <td>{{ $cRow['connector_type'] }}</td>
                                        <td class="is-center">{{ rtrim(rtrim(number_format((float) $cRow['power_kw'], 2, '.', ''), '0'), '.') }} kW</td>
                                        <td class="is-center">
                                            <span class="casin-badge-pill {{ $cRow['charging_speed_category'] === 'ultra' ? 'is-blue' : ($cRow['charging_speed_category'] === 'fast' ? 'is-green' : 'is-slate') }}">
                                                {{ $cRow['charging_speed_category'] === 'ultra' ? 'Ultra Fast' : ($cRow['charging_speed_category'] === 'fast' ? 'Fast' : 'Standard') }}
                                            </span>
                                        </td>
                                        <td class="is-center">{{ $cRow['current_type'] }}</td>
                                        <td class="is-center">
                                            <span class="casin-badge-pill {{ $cRow['available_connectors'] > 0 ? 'is-green' : 'is-red' }}">
                                                {{ $cRow['available_connectors'] }} / {{ $cRow['total_connectors'] }}
                                            </span>
                                        </td>
                                        <td class="is-center">
                                            <div class="casin-aksi-group">
                                                <button type="button" class="casin-aksi-btn casin-aksi-edit" data-edit-connector-row title="Edit Data Konektor">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                    </svg>
                                                </button>
                                                <button type="button" class="casin-aksi-btn casin-aksi-delete" data-delete-connector-row title="Hapus Data Konektor">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="3 6 5 6 21 6"/>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr data-empty-row>
                                        <td colspan="8" class="is-center" style="color: #64748b; padding: 32px;">Belum ada data konektor di basis data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="casin-table-footer">
                        <span>Menampilkan <strong data-connectors-table-count>{{ $connectorsTotalCount }}</strong> data</span>
                        <div class="casin-pagination">
                            <button type="button" class="casin-page-btn is-disabled" data-page-prev="connectors">Sebelumnya</button>
                            <span class="casin-page-indicator" data-page-indicator="connectors">1/{{ max(1, (int) ceil($connectorsTotalCount / 10)) }}</span>
                            <button type="button" class="casin-page-btn {{ $connectorsTotalCount <= 10 ? 'is-disabled' : '' }}" data-page-next="connectors">Berikutnya</button>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ==============================================================
               VIEW 6: DATA LAPORAN (Terhubung Database)
               ============================================================== --}}
            <section data-admin-section="reports" hidden>
                <header class="casin-view-header">
                    <h1 class="casin-view-title">Data Laporan</h1>
                    <hr class="casin-view-divider">
                </header>

                <div class="casin-data-toolbar">
                    <div class="casin-search-filter-group">
                        <div class="casin-pill-search">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="7"/>
                                <path d="M20 20l-3.5-3.5" stroke-linecap="round"/>
                            </svg>
                            <input type="search" placeholder="Cari stasiun, konektor, atau catatan..." data-admin-report-search>
                        </div>

                        <select class="casin-filter-select" data-admin-report-filter aria-label="Filter Status Laporan">
                            <option value="">Semua Status</option>
                            <option value="normal">Normal</option>
                            <option value="antre">Antrean</option>
                            <option value="rusak">Rusak</option>
                        </select>
                    </div>
                </div>

                <div class="casin-table-card">
                    <div class="casin-table-scroll">
                        <table class="casin-admin-table" data-admin-reports-table>
                            <thead>
                                <tr>
                                    <th class="is-center" style="width: 64px;">NO.</th>
                                    <th>STASIUN</th>
                                    <th>KONEKTOR</th>
                                    <th class="is-center">STATUS LAPORAN</th>
                                    <th>CATATAN KOMUNITAS</th>
                                    <th>WAKTU LAPOR</th>
                                    <th class="is-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody data-admin-reports-tbody>
                                @forelse ($reportRows as $idx => $rRow)
                                    <tr
                                        data-report-row
                                        data-row-id="{{ $rRow['id'] }}"
                                        data-row-status="{{ $rRow['status'] }}"
                                    >
                                        <td class="is-center" data-row-index>{{ $idx + 1 }}</td>
                                        <td style="font-weight: 500;">{{ $rRow['station_name'] }}</td>
                                        <td>{{ $rRow['connector_type'] }} ({{ rtrim(rtrim(number_format((float) $rRow['power_kw'], 2, '.', ''), '0'), '.') }} kW)</td>
                                        <td class="is-center">
                                            <select
                                                class="casin-inline-select"
                                                data-report-status-select
                                                data-report-id="{{ $rRow['id'] }}"
                                                aria-label="Ubah status laporan stasiun {{ $rRow['station_name'] }}"
                                            >
                                                <option value="normal" @selected($rRow['status'] === 'normal')>Normal</option>
                                                <option value="antre" @selected($rRow['status'] === 'antre')>Antrean</option>
                                                <option value="rusak" @selected($rRow['status'] === 'rusak')>Rusak</option>
                                            </select>
                                        </td>
                                        <td>{{ $rRow['notes'] }}</td>
                                        <td>{{ $rRow['reported_at'] }}</td>
                                        <td class="is-center">
                                            <div class="casin-aksi-group">
                                                <button type="button" class="casin-aksi-btn casin-aksi-delete" data-delete-report-row data-report-id="{{ $rRow['id'] }}" title="Hapus Data Laporan">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="3 6 5 6 21 6"/>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr data-empty-row>
                                        <td colspan="7" class="is-center" style="color: #64748b; padding: 32px;">Belum ada laporan kondisi stasiun di basis data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="casin-table-footer">
                        <span>Menampilkan <strong data-reports-table-count>{{ count($reportRows) }}</strong> data</span>
                        <div class="casin-pagination">
                            <button type="button" class="casin-page-btn is-disabled" data-page-prev="reports">Sebelumnya</button>
                            <span class="casin-page-indicator" data-page-indicator="reports">1/1</span>
                            <button type="button" class="casin-page-btn is-disabled" data-page-next="reports">Berikutnya</button>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ==============================================================
               VIEW 7: DATA ULASAN (Terhubung Database)
               ============================================================== --}}
            <section data-admin-section="reviews" hidden>
                <header class="casin-view-header">
                    <h1 class="casin-view-title">Data Ulasan</h1>
                    <hr class="casin-view-divider">
                </header>

                <div class="casin-data-toolbar">
                    <div class="casin-search-filter-group">
                        <div class="casin-pill-search">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="7"/>
                                <path d="M20 20l-3.5-3.5" stroke-linecap="round"/>
                            </svg>
                            <input type="search" placeholder="Cari pengguna, stasiun, atau komentar..." data-admin-review-search>
                        </div>

                        <select class="casin-filter-select" data-admin-review-rating aria-label="Filter Rating Ulasan">
                            <option value="">Semua Rating</option>
                            <option value="5">5 Bintang</option>
                            <option value="4">4 Bintang</option>
                            <option value="3">3 Bintang</option>
                            <option value="2">2 Bintang</option>
                            <option value="1">1 Bintang</option>
                        </select>
                    </div>
                </div>

                <div class="casin-table-card">
                    <div class="casin-table-scroll">
                        <table class="casin-admin-table" data-admin-reviews-table>
                            <thead>
                                <tr>
                                    <th class="is-center" style="width: 64px;">NO.</th>
                                    <th>PENGGUNA</th>
                                    <th>STASIUN</th>
                                    <th class="is-center">RATING</th>
                                    <th>KOMENTAR</th>
                                    <th>TANGGAL</th>
                                    <th class="is-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody data-admin-reviews-tbody>
                                @forelse ($reviewRows as $idx => $rvRow)
                                    <tr
                                        data-review-row
                                        data-row-id="{{ $rvRow['id'] }}"
                                        data-row-rating="{{ $rvRow['rating'] }}"
                                    >
                                        <td class="is-center" data-row-index>{{ $idx + 1 }}</td>
                                        <td style="font-weight: 500;">{{ $rvRow['user_name'] }}</td>
                                        <td>{{ $rvRow['station_name'] }}</td>
                                        <td class="is-center">
                                            <div class="casin-stars" style="justify-content: center;" aria-label="Rating {{ $rvRow['rating'] }} dari 5">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <svg viewBox="0 0 20 20" fill="{{ $i <= $rvRow['rating'] ? '#FFB900' : '#E2E8F0' }}">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                        </td>
                                        <td>{{ $rvRow['comment'] }}</td>
                                        <td>{{ $rvRow['created_at'] }}</td>
                                        <td class="is-center">
                                            <div class="casin-aksi-group">
                                                <button type="button" class="casin-aksi-btn casin-aksi-delete" data-delete-review-row data-review-id="{{ $rvRow['id'] }}" title="Hapus Ulasan">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="3 6 5 6 21 6"/>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr data-empty-row>
                                        <td colspan="7" class="is-center" style="color: #64748b; padding: 32px;">Belum ada ulasan pengguna di basis data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="casin-table-footer">
                        <span>Menampilkan <strong data-reviews-table-count>{{ count($reviewRows) }}</strong> data</span>
                        <div class="casin-pagination">
                            <button type="button" class="casin-page-btn is-disabled" data-page-prev="reviews">Sebelumnya</button>
                            <span class="casin-page-indicator" data-page-indicator="reviews">1/1</span>
                            <button type="button" class="casin-page-btn is-disabled" data-page-next="reviews">Berikutnya</button>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ==============================================================
               VIEW 8: PROFIL ADMIN (Terhubung Database)
               ============================================================== --}}
            <section data-admin-section="profile" hidden>
                <header class="casin-view-header">
                    <h1 class="casin-view-title">Profil Admin</h1>
                    <hr class="casin-view-divider">
                </header>

                <div class="casin-profile-card">
                    <h2 class="casin-panel-title" style="font-size: 18px;">Pengaturan Profil Administrator</h2>
                    <p class="casin-panel-sub" style="font-size: 13px; margin: 6px 0 24px;">
                        Perbarui informasi akun administrator CasIn.id Anda. Perubahan disimpan langsung ke basis data.
                    </p>

                    <div class="casin-modal-error" data-profile-error hidden style="margin-bottom: 18px;"></div>

                    <form class="casin-modal-form" data-admin-profile-form>
                        <div class="casin-modal-field">
                            <label for="adm-name">Nama Lengkap</label>
                            <input type="text" id="adm-name" name="name" class="casin-pill-input" value="{{ $adminName }}" required>
                        </div>

                        <div class="casin-modal-field">
                            <label for="adm-email">Email Terdaftar</label>
                            <input type="email" id="adm-email" class="casin-pill-input" value="{{ $adminEmail }}" readonly style="background: #f8fafc; color: #64748b; border-color: #cbd5e1;">
                        </div>

                        <div class="casin-modal-field">
                            <label for="adm-phone">Nomor HP</label>
                            <input type="text" id="adm-phone" name="phone" class="casin-pill-input" value="{{ $adminPhone }}" placeholder="Contoh: 081234567890">
                        </div>

                        <div class="casin-modal-row-2">
                            <div class="casin-modal-field">
                                <label for="adm-password">Kata Sandi Baru (Opsional)</label>
                                <input type="password" id="adm-password" name="password" class="casin-pill-input" placeholder="Minimal 8 karakter">
                            </div>
                            <div class="casin-modal-field">
                                <label for="adm-password-confirm">Konfirmasi Kata Sandi</label>
                                <input type="password" id="adm-password-confirm" name="password_confirmation" class="casin-pill-input" placeholder="Ulangi kata sandi baru">
                            </div>
                        </div>

                        <button type="submit" class="casin-modal-submit-btn" data-profile-submit-btn>Simpan Perubahan</button>
                    </form>
                </div>
            </section>
        </main>
    </div>

    {{-- ======================================================================
       MODAL 1: TAMBAH / EDIT DATA STASIUN
       ====================================================================== --}}
    <div class="casin-admin-modal-backdrop" id="casin-station-modal" hidden role="dialog" aria-modal="true" aria-labelledby="casin-station-modal-title">
        <div class="casin-admin-modal-card">
            <div class="casin-modal-top">
                <h2 class="casin-modal-heading" id="casin-station-modal-title" data-station-modal-heading>Tambah Data Stasiun</h2>
                <button type="button" class="casin-modal-close" data-close-station-modal aria-label="Tutup modal">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form class="casin-modal-form" data-station-modal-form>
                <div class="casin-modal-error" data-station-modal-error hidden></div>

                <div class="casin-modal-field">
                    <label for="st-name">Nama Stasiun</label>
                    <input type="text" id="st-name" name="name" class="casin-pill-input" placeholder="Contoh: PLN SPKLU Sudirman" required>
                </div>

                <div class="casin-modal-field">
                    <label for="st-operator-type">Tipe Operator</label>
                    <select id="st-operator-type" name="operator_type" class="casin-pill-select" required>
                        <option value="government">PLN (Pemerintah)</option>
                        <option value="private">Swasta</option>
                        <option value="commercial">Komersial</option>
                    </select>
                </div>

                <div class="casin-modal-field">
                    <label for="st-address">Alamat</label>
                    <input type="text" id="st-address" name="address" class="casin-pill-input" placeholder="Alamat lengkap stasiun" required>
                </div>

                <div class="casin-modal-row-2">
                    <div class="casin-modal-field">
                        <label for="st-lat">Latitude</label>
                        <input type="number" step="any" id="st-lat" name="latitude" class="casin-pill-input" placeholder="-6.2088" required>
                    </div>
                    <div class="casin-modal-field">
                        <label for="st-lng">Longitude</label>
                        <input type="number" step="any" id="st-lng" name="longitude" class="casin-pill-input" placeholder="106.8456" required>
                    </div>
                </div>

                <div class="casin-modal-row-4">
                    <div class="casin-modal-field">
                        <label for="st-price">Harga/kwh (Rp)</label>
                        <input type="number" min="0" step="1" id="st-price" name="price_per_kwh" class="casin-pill-input" placeholder="2466" required>
                    </div>
                    <div class="casin-modal-field">
                        <label for="st-hours">Jam Operasional</label>
                        <input type="text" id="st-hours" name="hours" class="casin-pill-input" placeholder="24 Jam" required>
                    </div>
                    <div class="casin-modal-field">
                        <label for="st-app">Nama Aplikasi</label>
                        <input type="text" id="st-app" name="app_name" class="casin-pill-input" placeholder="PLN Mobile">
                    </div>
                    <div class="casin-modal-field">
                        <label for="st-current-status">Status Charger</label>
                        <select id="st-current-status" name="current_status" class="casin-pill-select" required>
                            <option value="normal">Normal</option>
                            <option value="busy">Antrean Padat</option>
                            <option value="technical_issue">Kendala Teknis</option>
                        </select>
                    </div>
                </div>

                <div class="casin-modal-row-4" data-station-initial-connector-fields>
                    <div class="casin-modal-field">
                        <label for="st-conn-type">Konektor Awal</label>
                        <select id="st-conn-type" name="connector_type" class="casin-pill-select">
                            <option value="CCS2">CCS2</option>
                            <option value="AC Type 2">AC Type 2</option>
                            <option value="CHAdeMO">CHAdeMO</option>
                        </select>
                    </div>
                    <div class="casin-modal-field">
                        <label for="st-conn-power">Daya (kW)</label>
                        <input type="number" min="1" step="0.5" id="st-conn-power" name="power_kw" class="casin-pill-input" value="50">
                    </div>
                    <div class="casin-modal-field">
                        <label for="st-conn-available">Unit Tersedia</label>
                        <input type="number" min="0" id="st-conn-available" name="available_connectors" class="casin-pill-input" value="2">
                    </div>
                    <div class="casin-modal-field">
                        <label for="st-conn-total">Total Unit</label>
                        <input type="number" min="1" id="st-conn-total" name="total_connectors" class="casin-pill-input" value="2">
                    </div>
                </div>

                <button type="submit" class="casin-modal-submit-btn" data-station-modal-submit>Tambah Data</button>
            </form>
        </div>
    </div>

    {{-- ======================================================================
       MODAL 2: TAMBAH / EDIT DATA KENDARAAN
       ====================================================================== --}}
    <div class="casin-admin-modal-backdrop" id="casin-vehicle-modal" hidden role="dialog" aria-modal="true" aria-labelledby="casin-vehicle-modal-title">
        <div class="casin-admin-modal-card">
            <div class="casin-modal-top">
                <h2 class="casin-modal-heading" id="casin-vehicle-modal-title" data-vehicle-modal-heading>Tambah Data Kendaraan</h2>
                <button type="button" class="casin-modal-close" data-close-vehicle-modal aria-label="Tutup modal">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form class="casin-modal-form" data-vehicle-modal-form>
                <div class="casin-modal-error" data-vehicle-modal-error hidden></div>

                <div class="casin-modal-field">
                    <label for="vh-brand">Merek Kendaraan</label>
                    <input type="text" id="vh-brand" name="brand" class="casin-pill-input" placeholder="Contoh: Hyundai" required>
                </div>

                <div class="casin-modal-field">
                    <label for="vh-model">Model Kendaraan</label>
                    <input type="text" id="vh-model" name="model" class="casin-pill-input" placeholder="Contoh: Ioniq 5" required>
                </div>

                <div class="casin-modal-field">
                    <label for="vh-category">Jenis Kendaraan</label>
                    <select id="vh-category" name="category" class="casin-pill-select" required>
                        <option value="Mobil Listrik">Mobil Listrik</option>
                        <option value="Motor Listrik">Motor Listrik</option>
                    </select>
                </div>

                <div class="casin-modal-row-2">
                    <div class="casin-modal-field">
                        <label for="vh-battery">Kapasitas Baterai (kWh)</label>
                        <input type="number" step="0.01" min="0.1" id="vh-battery" name="battery" class="casin-pill-input" placeholder="72.6" required>
                    </div>
                    <div class="casin-modal-field">
                        <label for="vh-connector">Tipe Konektor</label>
                        <select id="vh-connector" name="connector" class="casin-pill-select" required>
                            <option value="CCS2">CCS2</option>
                            <option value="AC Type 2">AC Type 2</option>
                            <option value="CHAdeMO">CHAdeMO</option>
                            <option value="Type 2">Type 2</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="casin-modal-submit-btn" data-vehicle-modal-submit>Tambah Data</button>
            </form>
        </div>
    </div>

    {{-- ======================================================================
       MODAL 3: TAMBAH / EDIT DATA KONEKTOR
       ====================================================================== --}}
    <div class="casin-admin-modal-backdrop" id="casin-connector-modal" hidden role="dialog" aria-modal="true" aria-labelledby="casin-connector-modal-title">
        <div class="casin-admin-modal-card">
            <div class="casin-modal-top">
                <h2 class="casin-modal-heading" id="casin-connector-modal-title" data-connector-modal-heading>Tambah Data Konektor</h2>
                <button type="button" class="casin-modal-close" data-close-connector-modal aria-label="Tutup modal">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form class="casin-modal-form" data-connector-modal-form>
                <div class="casin-modal-error" data-connector-modal-error hidden></div>

                <div class="casin-modal-field">
                    <label for="cn-station">Stasiun Pengisian</label>
                    @if ($stationsUseRemotePagination)
                        <input
                            type="search"
                            id="cn-station-search"
                            class="casin-pill-input"
                            placeholder="Ketik minimal 2 huruf untuk mencari nama stasiun..."
                            data-connector-station-search
                            style="margin-bottom: 10px;"
                        >
                    @endif
                    <select id="cn-station" name="station_id" class="casin-pill-select" required>
                        @foreach ($stationOptions as $stOpt)
                            <option value="{{ $stOpt['id'] }}">{{ $stOpt['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="casin-modal-row-2">
                    <div class="casin-modal-field">
                        <label for="cn-type">Tipe Konektor</label>
                        <select id="cn-type" name="connector_type" class="casin-pill-select" required>
                            <option value="CCS2">CCS2</option>
                            <option value="AC Type 2">AC Type 2</option>
                            <option value="DC Connector">DC Connector</option>
                            <option value="AC Connector">AC Connector</option>
                            <option value="CHAdeMO">CHAdeMO</option>
                        </select>
                    </div>
                    <div class="casin-modal-field">
                        <label for="cn-power">Daya (kW)</label>
                        <input type="number" step="0.5" min="1" id="cn-power" name="power_kw" class="casin-pill-input" value="50" required>
                    </div>
                </div>

                <div class="casin-modal-row-4">
                    <div class="casin-modal-field">
                        <label for="cn-speed">Kategori Kecepatan</label>
                        <select id="cn-speed" name="charging_speed_category" class="casin-pill-select" required>
                            <option value="standard">Standard</option>
                            <option value="fast">Fast</option>
                            <option value="ultra" selected>Ultra Fast</option>
                        </select>
                    </div>
                    <div class="casin-modal-field">
                        <label for="cn-current">Jenis Arus</label>
                        <select id="cn-current" name="current_type" class="casin-pill-select" required>
                            <option value="DC">DC</option>
                            <option value="AC">AC</option>
                        </select>
                    </div>
                    <div class="casin-modal-field">
                        <label for="cn-available">Unit Tersedia</label>
                        <input type="number" min="0" id="cn-available" name="available_connectors" class="casin-pill-input" value="2" required>
                    </div>
                    <div class="casin-modal-field">
                        <label for="cn-total">Total Unit</label>
                        <input type="number" min="1" id="cn-total" name="total_connectors" class="casin-pill-input" value="2" required>
                    </div>
                </div>

                <button type="submit" class="casin-modal-submit-btn" data-connector-modal-submit>Tambah Data</button>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const shell = document.getElementById('casin-admin-shell');
            if (!shell) return;

            const csrfToken = @json(csrf_token());
            const apiBase = @json(url('/admin/api'));
            const stationsRemoteEndpoint = @json($stationsUseRemotePagination ? url('/admin/api/stations') : '');
            const connectorsRemoteEndpoint = @json($connectorsUseRemotePagination ? url('/admin/api/connectors') : '');
            const stationOptionsEndpoint = @json(url('/admin/api/station-options'));
            const stationsInitialTotal = @json((int) $stationsTotalCount);
            const connectorsInitialTotal = @json((int) $connectorsTotalCount);
            const TOAST_STORAGE_KEY = 'casin_admin_toast';
            let activeSection = 'dashboard';

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            window.addEventListener('pageshow', (event) => {
                if (event.persisted) {
                    window.location.reload();
                }
            });

            shell.querySelector('.casin-logout-form')?.addEventListener('submit', () => {
                try {
                    localStorage.removeItem('casin.auth_token');
                    localStorage.removeItem('casin.auth_user');
                } catch (_) {}
            });

            // Toast notification helper
            const toastEl = document.getElementById('casin-admin-toast');
            const toastMsgEl = toastEl?.querySelector('[data-toast-message]');

            function showToast(message, isError = false) {
                if (!toastEl || !toastMsgEl) return;
                toastMsgEl.textContent = message;
                toastEl.classList.toggle('is-error', isError);
                toastEl.removeAttribute('hidden');
            }

            toastEl?.querySelector('[data-close-toast]')?.addEventListener('click', () => {
                toastEl.setAttribute('hidden', '');
            });

            try {
                const pendingToast = sessionStorage.getItem(TOAST_STORAGE_KEY);
                if (pendingToast) {
                    sessionStorage.removeItem(TOAST_STORAGE_KEY);
                    const parsed = JSON.parse(pendingToast);
                    if (parsed?.message) {
                        showToast(parsed.message, Boolean(parsed.isError));
                    }
                }
            } catch (_) {}

            function reloadWithToast(message, targetHash) {
                try {
                    sessionStorage.setItem(TOAST_STORAGE_KEY, JSON.stringify({ message, isError: false }));
                } catch (_) {}
                if (targetHash) {
                    window.location.hash = targetHash;
                }
                window.location.reload();
            }

            async function apiRequest(endpoint, method, payload = null) {
                const options = {
                    method,
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                };

                if (payload !== null) {
                    options.headers['Content-Type'] = 'application/json';
                    options.body = JSON.stringify(payload);
                }

                const response = await fetch(`${apiBase}${endpoint}`, options);
                let data = {};
                try {
                    data = await response.json();
                } catch (_) {}

                if (!response.ok) {
                    const validationErrors = data?.errors
                        ? Object.values(data.errors).flat().join(' ')
                        : '';
                    throw new Error(validationErrors || data?.message || 'Terjadi kesalahan saat memproses permintaan.');
                }

                return data;
            }

            // Sidebar & Navigation
            const sidebarToggle = shell.querySelector('[data-admin-sidebar-toggle]');
            sidebarToggle?.addEventListener('click', () => {
                shell.classList.toggle('is-sidebar-collapsed');
            });

            const navButtons = shell.querySelectorAll('[data-admin-nav]');
            const sections = shell.querySelectorAll('[data-admin-section]');

            function activateSection(targetKey) {
                activeSection = targetKey;
                navButtons.forEach((btn) => {
                    btn.classList.toggle('is-active', btn.dataset.adminNav === targetKey);
                });
                sections.forEach((sec) => {
                    if (sec.dataset.adminSection === targetKey) {
                        sec.removeAttribute('hidden');
                    } else {
                        sec.setAttribute('hidden', '');
                    }
                });
            }

            navButtons.forEach((btn) => {
                btn.addEventListener('click', () => {
                    const key = btn.dataset.adminNav;
                    activateSection(key);
                    history.replaceState(null, '', '#' + key);
                });
            });

            shell.querySelectorAll('[data-admin-switch-nav]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const key = btn.dataset.adminSwitchNav;
                    activateSection(key);
                    history.replaceState(null, '', '#' + key);
                });
            });

            const initialHash = (window.location.hash || '').replace('#', '').trim();
            if (initialHash && shell.querySelector(`[data-admin-section="${initialHash}"]`)) {
                activateSection(initialHash);
            }

            function renderRemoteStationRows(items, startOffset) {
                if (!items.length) {
                    return '<tr data-empty-row><td colspan="9" class="is-center" style="color: #64748b; padding: 32px;">Tidak ada data stasiun yang sesuai dengan filter pencarian.</td></tr>';
                }

                return items.map((row, idx) => {
                    const opBadgeClass = row.operator_type === 'government' ? 'is-blue' : (row.operator_type === 'private' ? 'is-amber' : 'is-slate');
                    const statusBadgeClass = row.current_status === 'normal' ? 'is-green' : (row.current_status === 'busy' ? 'is-amber' : 'is-red');
                    const showUrl = escapeHtml(row.show_url || `/stasiun/${row.id}`);

                    return `
                        <tr
                            data-station-row
                            data-row-id="${escapeHtml(row.id)}"
                            data-row-name="${escapeHtml(row.name)}"
                            data-row-operator="${escapeHtml(row.operator)}"
                            data-row-operator-type="${escapeHtml(row.operator_type)}"
                            data-row-address="${escapeHtml(row.address)}"
                            data-row-connectors="${escapeHtml(row.connectors)}"
                            data-row-tariff="${escapeHtml(row.tariff)}"
                            data-row-price="${escapeHtml(row.price_per_kwh)}"
                            data-row-hours="${escapeHtml(row.hours)}"
                            data-row-lat="${escapeHtml(row.latitude)}"
                            data-row-lng="${escapeHtml(row.longitude)}"
                            data-row-app="${escapeHtml(row.app_name)}"
                            data-row-status="${escapeHtml(row.status)}"
                            data-row-current-status="${escapeHtml(row.current_status)}"
                        >
                            <td class="is-center" data-row-index>${startOffset + idx + 1}</td>
                            <td style="font-weight: 500;">${escapeHtml(row.name)}</td>
                            <td class="is-center"><span class="casin-badge-pill ${opBadgeClass}">${escapeHtml(row.operator)}</span></td>
                            <td>${escapeHtml(row.address)}</td>
                            <td>${escapeHtml(row.connectors)}</td>
                            <td>${escapeHtml(row.tariff)}</td>
                            <td>${escapeHtml(row.hours)}</td>
                            <td class="is-center"><span class="casin-badge-pill ${statusBadgeClass}">${escapeHtml(row.status)}</span></td>
                            <td class="is-center">
                                <div class="casin-aksi-group">
                                    <a href="${showUrl}" class="casin-aksi-btn casin-aksi-view" title="Lihat Stasiun">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </a>
                                    <button type="button" class="casin-aksi-btn casin-aksi-edit" data-edit-station-row title="Edit Data Stasiun">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                    </button>
                                    <button type="button" class="casin-aksi-btn casin-aksi-delete" data-delete-station-row title="Hapus Data Stasiun">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('');
            }

            function renderRemoteConnectorRows(items, startOffset) {
                if (!items.length) {
                    return '<tr data-empty-row><td colspan="8" class="is-center" style="color: #64748b; padding: 32px;">Tidak ada data konektor yang sesuai dengan filter pencarian.</td></tr>';
                }

                return items.map((row, idx) => {
                    const speedClass = row.charging_speed_category === 'ultra' ? 'is-blue' : (row.charging_speed_category === 'fast' ? 'is-green' : 'is-slate');
                    const speedLabel = row.charging_speed_category === 'ultra' ? 'Ultra Fast' : (row.charging_speed_category === 'fast' ? 'Fast' : 'Standard');
                    const availClass = Number(row.available_connectors) > 0 ? 'is-green' : 'is-red';
                    const powerFormatted = String(Number(row.power_kw || 0));

                    return `
                        <tr
                            data-connector-row
                            data-row-id="${escapeHtml(row.id)}"
                            data-row-station-id="${escapeHtml(row.station_id)}"
                            data-row-station-name="${escapeHtml(row.station_name)}"
                            data-row-connector-type="${escapeHtml(row.connector_type)}"
                            data-row-power-kw="${escapeHtml(row.power_kw)}"
                            data-row-speed="${escapeHtml(row.charging_speed_category)}"
                            data-row-current-type="${escapeHtml(row.current_type)}"
                            data-row-available="${escapeHtml(row.available_connectors)}"
                            data-row-total="${escapeHtml(row.total_connectors)}"
                        >
                            <td class="is-center" data-row-index>${startOffset + idx + 1}</td>
                            <td style="font-weight: 500;">${escapeHtml(row.station_name)}</td>
                            <td>${escapeHtml(row.connector_type)}</td>
                            <td class="is-center">${escapeHtml(powerFormatted)} kW</td>
                            <td class="is-center"><span class="casin-badge-pill ${speedClass}">${escapeHtml(speedLabel)}</span></td>
                            <td class="is-center">${escapeHtml(row.current_type)}</td>
                            <td class="is-center"><span class="casin-badge-pill ${availClass}">${escapeHtml(row.available_connectors)} / ${escapeHtml(row.total_connectors)}</span></td>
                            <td class="is-center">
                                <div class="casin-aksi-group">
                                    <button type="button" class="casin-aksi-btn casin-aksi-edit" data-edit-connector-row title="Edit Data Konektor">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                    </button>
                                    <button type="button" class="casin-aksi-btn casin-aksi-delete" data-delete-connector-row title="Hapus Data Konektor">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('');
            }

            // Reusable search + filter + pagination table manager (mendukung DOM lokal dan paginasi API jarak jauh)
            function initTablePagination({
                sectionKey,
                tbodySelector,
                rowSelector,
                searchSelector,
                countSelector,
                filters = [],
                pageSize = 10,
                remoteEndpoint = '',
                initialTotal = 0,
                buildParams = null,
                renderRows = null,
            }) {
                const tbody = shell.querySelector(tbodySelector);
                if (!tbody) return;

                const searchInput = shell.querySelector(searchSelector);
                const countEl = shell.querySelector(countSelector);
                const prevBtn = shell.querySelector(`[data-page-prev="${sectionKey}"]`);
                const nextBtn = shell.querySelector(`[data-page-next="${sectionKey}"]`);
                const indicatorEl = shell.querySelector(`[data-page-indicator="${sectionKey}"]`);

                let currentPage = 1;
                let totalPages = Math.max(1, Math.ceil((initialTotal || 0) / pageSize));

                if (remoteEndpoint && typeof renderRows === 'function') {
                    let requestSeq = 0;
                    let searchTimer = null;

                    const updatePagerControls = (total, page, lastPage) => {
                        currentPage = page;
                        totalPages = Math.max(1, lastPage);
                        if (countEl) countEl.textContent = String(total);
                        if (indicatorEl) indicatorEl.textContent = `${currentPage}/${totalPages}`;
                        if (prevBtn) {
                            prevBtn.disabled = currentPage <= 1;
                            prevBtn.classList.toggle('is-disabled', currentPage <= 1);
                        }
                        if (nextBtn) {
                            nextBtn.disabled = currentPage >= totalPages;
                            nextBtn.classList.toggle('is-disabled', currentPage >= totalPages);
                        }
                    };

                    const fetchPage = async () => {
                        const seq = ++requestSeq;
                        const params = new URLSearchParams({
                            page: String(currentPage),
                            per_page: String(pageSize),
                            search: (searchInput?.value || '').trim(),
                        });
                        if (typeof buildParams === 'function') {
                            const extra = buildParams();
                            Object.entries(extra).forEach(([k, v]) => {
                                if (v !== '' && v !== null && v !== undefined) {
                                    params.set(k, String(v));
                                }
                            });
                        }

                        try {
                            const response = await fetch(`${remoteEndpoint}?${params.toString()}`, {
                                credentials: 'same-origin',
                                headers: { 'Accept': 'application/json' },
                            });
                            if (!response.ok || seq !== requestSeq) return;
                            const json = await response.json();
                            if (seq !== requestSeq || !json?.data) return;

                            const { items = [], total = 0, current_page = 1, last_page = 1 } = json.data;
                            const startOffset = (current_page - 1) * pageSize;
                            tbody.innerHTML = renderRows(items, startOffset);
                            updatePagerControls(total, current_page, last_page);
                        } catch (_) {}
                    };

                    updatePagerControls(initialTotal, 1, totalPages);

                    searchInput?.addEventListener('input', () => {
                        window.clearTimeout(searchTimer);
                        searchTimer = window.setTimeout(() => {
                            currentPage = 1;
                            fetchPage();
                        }, 200);
                    });

                    filters.forEach((f) => {
                        f.el?.addEventListener('change', () => {
                            currentPage = 1;
                            fetchPage();
                        });
                    });

                    prevBtn?.addEventListener('click', () => {
                        if (currentPage > 1) {
                            currentPage -= 1;
                            fetchPage();
                        }
                    });

                    nextBtn?.addEventListener('click', () => {
                        if (currentPage < totalPages) {
                            currentPage += 1;
                            fetchPage();
                        }
                    });

                    return;
                }

                const rows = Array.from(tbody.querySelectorAll(rowSelector));

                function apply() {
                    const query = (searchInput?.value || '').trim().toLowerCase();

                    const filtered = rows.filter((row) => {
                        if (query !== '' && !(row.textContent || '').toLowerCase().includes(query)) {
                            return false;
                        }
                        for (const f of filters) {
                            const val = f.el?.value || '';
                            if (val !== '' && !f.match(row, val)) {
                                return false;
                            }
                        }
                        return true;
                    });

                    const totalFiltered = filtered.length;
                    const calcPages = Math.max(1, Math.ceil(totalFiltered / pageSize));
                    if (currentPage > calcPages) currentPage = calcPages;

                    const start = (currentPage - 1) * pageSize;
                    const end = start + pageSize;
                    const visibleSet = new Set(filtered.slice(start, end));

                    rows.forEach((row) => {
                        row.style.display = visibleSet.has(row) ? '' : 'none';
                    });

                    filtered.slice(start, end).forEach((row, idx) => {
                        const cell = row.querySelector('[data-row-index]');
                        if (cell) cell.textContent = String(start + idx + 1);
                    });

                    if (countEl) countEl.textContent = String(totalFiltered);
                    if (indicatorEl) indicatorEl.textContent = `${currentPage}/${calcPages}`;

                    if (prevBtn) {
                        prevBtn.disabled = currentPage <= 1;
                        prevBtn.classList.toggle('is-disabled', currentPage <= 1);
                    }
                    if (nextBtn) {
                        nextBtn.disabled = currentPage >= calcPages;
                        nextBtn.classList.toggle('is-disabled', currentPage >= calcPages);
                    }
                }

                searchInput?.addEventListener('input', () => {
                    currentPage = 1;
                    apply();
                });

                filters.forEach((f) => {
                    f.el?.addEventListener('change', () => {
                        currentPage = 1;
                        apply();
                    });
                });

                prevBtn?.addEventListener('click', () => {
                    if (currentPage > 1) {
                        currentPage -= 1;
                        apply();
                    }
                });

                nextBtn?.addEventListener('click', () => {
                    currentPage += 1;
                    apply();
                });

                apply();
            }

            const stationOperatorFilter = shell.querySelector('[data-admin-station-operator]');
            const stationStatusFilter = shell.querySelector('[data-admin-station-status]');

            initTablePagination({
                sectionKey: 'stations',
                tbodySelector: '[data-admin-stations-tbody]',
                rowSelector: '[data-station-row]',
                searchSelector: '[data-admin-station-search]',
                countSelector: '[data-stations-table-count]',
                filters: [
                    {
                        el: stationOperatorFilter,
                        match: (row, val) => row.dataset.rowOperatorType === val,
                    },
                    {
                        el: stationStatusFilter,
                        match: (row, val) => row.dataset.rowCurrentStatus === val,
                    },
                ],
                remoteEndpoint: stationsRemoteEndpoint,
                initialTotal: stationsInitialTotal,
                buildParams: () => ({
                    operator_type: stationOperatorFilter?.value || '',
                    current_status: stationStatusFilter?.value || '',
                }),
                renderRows: renderRemoteStationRows,
            });

            initTablePagination({
                sectionKey: 'vehicles',
                tbodySelector: '[data-admin-vehicles-tbody]',
                rowSelector: '[data-vehicle-row]',
                searchSelector: '[data-admin-vehicle-search]',
                countSelector: '[data-vehicles-table-count]',
                filters: [
                    {
                        el: shell.querySelector('[data-admin-vehicle-category]'),
                        match: (row, val) => row.dataset.rowCategory === val,
                    },
                ],
            });

            initTablePagination({
                sectionKey: 'users',
                tbodySelector: '[data-admin-users-tbody]',
                rowSelector: '[data-user-row]',
                searchSelector: '[data-admin-user-search]',
                countSelector: '[data-users-table-count]',
                filters: [
                    {
                        el: shell.querySelector('[data-admin-user-role]'),
                        match: (row, val) => row.dataset.rowRole === val,
                    },
                ],
            });

            const connectorTypeFilter = shell.querySelector('[data-admin-connector-type]');

            initTablePagination({
                sectionKey: 'connectors',
                tbodySelector: '[data-admin-connectors-tbody]',
                rowSelector: '[data-connector-row]',
                searchSelector: '[data-admin-connector-search]',
                countSelector: '[data-connectors-table-count]',
                filters: [
                    {
                        el: connectorTypeFilter,
                        match: (row, val) => row.dataset.rowConnectorType === val,
                    },
                ],
                remoteEndpoint: connectorsRemoteEndpoint,
                initialTotal: connectorsInitialTotal,
                buildParams: () => ({
                    connector_type: connectorTypeFilter?.value || '',
                }),
                renderRows: renderRemoteConnectorRows,
            });

            initTablePagination({
                sectionKey: 'reports',
                tbodySelector: '[data-admin-reports-tbody]',
                rowSelector: '[data-report-row]',
                searchSelector: '[data-admin-report-search]',
                countSelector: '[data-reports-table-count]',
                filters: [
                    {
                        el: shell.querySelector('[data-admin-report-filter]'),
                        match: (row, val) => row.dataset.rowStatus === val,
                    },
                ],
            });

            initTablePagination({
                sectionKey: 'reviews',
                tbodySelector: '[data-admin-reviews-tbody]',
                rowSelector: '[data-review-row]',
                searchSelector: '[data-admin-review-search]',
                countSelector: '[data-reviews-table-count]',
                filters: [
                    {
                        el: shell.querySelector('[data-admin-review-rating]'),
                        match: (row, val) => row.dataset.rowRating === val,
                    },
                ],
            });

            // ==============================================================
            // STATION MODAL & CRUD
            // ==============================================================
            const stationModal = document.getElementById('casin-station-modal');
            const stationForm = stationModal?.querySelector('[data-station-modal-form]');
            const stationHeading = stationModal?.querySelector('[data-station-modal-heading]');
            const stationSubmit = stationModal?.querySelector('[data-station-modal-submit]');
            const stationError = stationModal?.querySelector('[data-station-modal-error]');
            const stationConnFields = stationModal?.querySelector('[data-station-initial-connector-fields]');
            let editingStationId = null;

            function setStationModalError(msg) {
                if (!stationError) return;
                if (msg) {
                    stationError.textContent = msg;
                    stationError.removeAttribute('hidden');
                } else {
                    stationError.setAttribute('hidden', '');
                }
            }

            shell.querySelector('[data-open-station-modal="create"]')?.addEventListener('click', () => {
                editingStationId = null;
                stationForm?.reset();
                setStationModalError('');
                if (stationHeading) stationHeading.textContent = 'Tambah Data Stasiun';
                if (stationSubmit) stationSubmit.textContent = 'Tambah Data';
                stationConnFields?.removeAttribute('hidden');
                stationModal?.removeAttribute('hidden');
            });

            stationModal?.querySelector('[data-close-station-modal]')?.addEventListener('click', () => {
                stationModal.setAttribute('hidden', '');
            });

            stationModal?.addEventListener('click', (e) => {
                if (e.target === stationModal) stationModal.setAttribute('hidden', '');
            });

            shell.querySelector('[data-admin-stations-tbody]')?.addEventListener('click', async (e) => {
                const editBtn = e.target.closest('[data-edit-station-row]');
                if (editBtn) {
                    const row = editBtn.closest('[data-station-row]');
                    if (!row || !stationForm) return;
                    editingStationId = row.dataset.rowId;
                    setStationModalError('');
                    if (stationHeading) stationHeading.textContent = 'Edit Data Stasiun';
                    if (stationSubmit) stationSubmit.textContent = 'Simpan Perubahan';
                    stationConnFields?.setAttribute('hidden', '');

                    stationForm.elements.name.value = row.dataset.rowName || '';
                    stationForm.elements.operator_type.value = row.dataset.rowOperatorType || 'government';
                    stationForm.elements.address.value = row.dataset.rowAddress || '';
                    stationForm.elements.latitude.value = row.dataset.rowLat || '';
                    stationForm.elements.longitude.value = row.dataset.rowLng || '';
                    stationForm.elements.price_per_kwh.value = row.dataset.rowPrice || '0';
                    stationForm.elements.hours.value = row.dataset.rowHours || '24 Jam';
                    stationForm.elements.app_name.value = row.dataset.rowApp || 'PLN Mobile';
                    stationForm.elements.current_status.value = row.dataset.rowCurrentStatus || 'normal';
                    stationModal?.removeAttribute('hidden');
                    return;
                }

                const delBtn = e.target.closest('[data-delete-station-row]');
                if (delBtn) {
                    const row = delBtn.closest('[data-station-row]');
                    const id = row?.dataset.rowId;
                    const name = row?.dataset.rowName || 'stasiun ini';
                    if (!id) return;
                    if (!window.confirm(`Hapus stasiun "${name}" beserta data konektor/ulasan terkait dari basis data?`)) return;

                    try {
                        const res = await apiRequest(`/stations/${id}`, 'DELETE');
                        reloadWithToast(res.message || 'Data stasiun berhasil dihapus.', '#stations');
                    } catch (err) {
                        showToast(err.message, true);
                    }
                }
            });

            stationForm?.addEventListener('submit', async (e) => {
                e.preventDefault();
                setStationModalError('');

                const payload = {
                    name: stationForm.elements.name.value.trim(),
                    operator_type: stationForm.elements.operator_type.value,
                    address: stationForm.elements.address.value.trim(),
                    latitude: Number(stationForm.elements.latitude.value),
                    longitude: Number(stationForm.elements.longitude.value),
                    price_per_kwh: Number(stationForm.elements.price_per_kwh.value),
                    operating_hours: stationForm.elements.hours.value.trim(),
                    require_app_name: stationForm.elements.app_name.value.trim() || 'PLN Mobile',
                    current_status: stationForm.elements.current_status.value,
                };

                if (!editingStationId) {
                    payload.connector_type = stationForm.elements.connector_type.value;
                    payload.power_kw = Number(stationForm.elements.power_kw.value || 50);
                    payload.available_connectors = Number(stationForm.elements.available_connectors.value || 2);
                    payload.total_connectors = Number(stationForm.elements.total_connectors.value || 2);
                }

                const originalText = stationSubmit?.textContent || '';
                if (stationSubmit) {
                    stationSubmit.disabled = true;
                    stationSubmit.textContent = 'Menyimpan...';
                }

                try {
                    const res = editingStationId
                        ? await apiRequest(`/stations/${editingStationId}`, 'PUT', payload)
                        : await apiRequest('/stations', 'POST', payload);
                    stationModal?.setAttribute('hidden', '');
                    reloadWithToast(res.message || 'Data stasiun berhasil disimpan.', '#stations');
                } catch (err) {
                    setStationModalError(err.message);
                } finally {
                    if (stationSubmit) {
                        stationSubmit.disabled = false;
                        stationSubmit.textContent = originalText;
                    }
                }
            });

            // ==============================================================
            // VEHICLE MODAL & CRUD
            // ==============================================================
            const vehicleModal = document.getElementById('casin-vehicle-modal');
            const vehicleForm = vehicleModal?.querySelector('[data-vehicle-modal-form]');
            const vehicleHeading = vehicleModal?.querySelector('[data-vehicle-modal-heading]');
            const vehicleSubmit = vehicleModal?.querySelector('[data-vehicle-modal-submit]');
            const vehicleError = vehicleModal?.querySelector('[data-vehicle-modal-error]');
            let editingVehicleId = null;

            function setVehicleModalError(msg) {
                if (!vehicleError) return;
                if (msg) {
                    vehicleError.textContent = msg;
                    vehicleError.removeAttribute('hidden');
                } else {
                    vehicleError.setAttribute('hidden', '');
                }
            }

            shell.querySelector('[data-open-vehicle-modal="create"]')?.addEventListener('click', () => {
                editingVehicleId = null;
                vehicleForm?.reset();
                setVehicleModalError('');
                if (vehicleHeading) vehicleHeading.textContent = 'Tambah Data Kendaraan';
                if (vehicleSubmit) vehicleSubmit.textContent = 'Tambah Data';
                vehicleModal?.removeAttribute('hidden');
            });

            vehicleModal?.querySelector('[data-close-vehicle-modal]')?.addEventListener('click', () => {
                vehicleModal.setAttribute('hidden', '');
            });

            vehicleModal?.addEventListener('click', (e) => {
                if (e.target === vehicleModal) vehicleModal.setAttribute('hidden', '');
            });

            shell.querySelector('[data-admin-vehicles-tbody]')?.addEventListener('click', async (e) => {
                const editBtn = e.target.closest('[data-edit-vehicle-row]');
                if (editBtn) {
                    const row = editBtn.closest('[data-vehicle-row]');
                    if (!row || !vehicleForm) return;
                    editingVehicleId = row.dataset.rowId;
                    setVehicleModalError('');
                    if (vehicleHeading) vehicleHeading.textContent = 'Edit Data Kendaraan';
                    if (vehicleSubmit) vehicleSubmit.textContent = 'Simpan Perubahan';

                    vehicleForm.elements.brand.value = row.dataset.rowBrand || '';
                    vehicleForm.elements.model.value = row.dataset.rowModel || '';
                    vehicleForm.elements.category.value = row.dataset.rowCategory || 'Mobil Listrik';
                    vehicleForm.elements.battery.value = row.dataset.rowBatteryKwh || '';
                    vehicleForm.elements.connector.value = row.dataset.rowConnector || 'CCS2';
                    vehicleModal?.removeAttribute('hidden');
                    return;
                }

                const delBtn = e.target.closest('[data-delete-vehicle-row]');
                if (delBtn) {
                    const row = delBtn.closest('[data-vehicle-row]');
                    const id = row?.dataset.rowId;
                    const label = `${row?.dataset.rowBrand || ''} ${row?.dataset.rowModel || ''}`.trim();
                    if (!id) return;
                    if (!window.confirm(`Hapus kendaraan "${label}" dari basis data?`)) return;

                    try {
                        const res = await apiRequest(`/vehicles/${id}`, 'DELETE');
                        reloadWithToast(res.message || 'Data kendaraan berhasil dihapus.', '#vehicles');
                    } catch (err) {
                        showToast(err.message, true);
                    }
                }
            });

            vehicleForm?.addEventListener('submit', async (e) => {
                e.preventDefault();
                setVehicleModalError('');

                const payload = {
                    brand: vehicleForm.elements.brand.value.trim(),
                    model: vehicleForm.elements.model.value.trim(),
                    category: vehicleForm.elements.category.value,
                    battery_capacity_kwh: Number(vehicleForm.elements.battery.value),
                    default_connector_type: vehicleForm.elements.connector.value,
                };

                const originalText = vehicleSubmit?.textContent || '';
                if (vehicleSubmit) {
                    vehicleSubmit.disabled = true;
                    vehicleSubmit.textContent = 'Menyimpan...';
                }

                try {
                    const res = editingVehicleId
                        ? await apiRequest(`/vehicles/${editingVehicleId}`, 'PUT', payload)
                        : await apiRequest('/vehicles', 'POST', payload);
                    vehicleModal?.setAttribute('hidden', '');
                    reloadWithToast(res.message || 'Data kendaraan berhasil disimpan.', '#vehicles');
                } catch (err) {
                    setVehicleModalError(err.message);
                } finally {
                    if (vehicleSubmit) {
                        vehicleSubmit.disabled = false;
                        vehicleSubmit.textContent = originalText;
                    }
                }
            });

            // ==============================================================
            // CONNECTOR MODAL & CRUD
            // ==============================================================
            const connectorModal = document.getElementById('casin-connector-modal');
            const connectorForm = connectorModal?.querySelector('[data-connector-modal-form]');
            const connectorHeading = connectorModal?.querySelector('[data-connector-modal-heading]');
            const connectorSubmit = connectorModal?.querySelector('[data-connector-modal-submit]');
            const connectorError = connectorModal?.querySelector('[data-connector-modal-error]');
            const connectorStationSearch = connectorModal?.querySelector('[data-connector-station-search]');
            let editingConnectorId = null;

            function ensureSelectOption(selectEl, value, label) {
                if (!selectEl || !value) return;
                const strVal = String(value);
                const exists = Array.from(selectEl.options).some((opt) => opt.value === strVal);
                if (!exists) {
                    const opt = document.createElement('option');
                    opt.value = strVal;
                    opt.textContent = label || strVal;
                    selectEl.appendChild(opt);
                }
                selectEl.value = strVal;
            }

            if (connectorStationSearch && connectorForm?.elements?.station_id) {
                let stOptTimer = null;
                connectorStationSearch.addEventListener('input', () => {
                    window.clearTimeout(stOptTimer);
                    stOptTimer = window.setTimeout(async () => {
                        const q = connectorStationSearch.value.trim();
                        if (q.length > 0 && q.length < 2) return;
                        try {
                            const url = `${stationOptionsEndpoint}?q=${encodeURIComponent(q)}`;
                            const response = await fetch(url, {
                                credentials: 'same-origin',
                                headers: { 'Accept': 'application/json' },
                            });
                            if (!response.ok) return;
                            const json = await response.json();
                            const items = Array.isArray(json?.data) ? json.data : [];
                            const selectEl = connectorForm.elements.station_id;
                            const previousVal = selectEl.value;
                            selectEl.innerHTML = items.map((item) => `<option value="${escapeHtml(item.id)}">${escapeHtml(item.name)}</option>`).join('');
                            if (previousVal && Array.from(selectEl.options).some((opt) => opt.value === previousVal)) {
                                selectEl.value = previousVal;
                            }
                        } catch (_) {}
                    }, 220);
                });
            }

            function setConnectorModalError(msg) {
                if (!connectorError) return;
                if (msg) {
                    connectorError.textContent = msg;
                    connectorError.removeAttribute('hidden');
                } else {
                    connectorError.setAttribute('hidden', '');
                }
            }

            shell.querySelector('[data-open-connector-modal="create"]')?.addEventListener('click', () => {
                editingConnectorId = null;
                connectorForm?.reset();
                if (connectorStationSearch) connectorStationSearch.value = '';
                setConnectorModalError('');
                if (connectorHeading) connectorHeading.textContent = 'Tambah Data Konektor';
                if (connectorSubmit) connectorSubmit.textContent = 'Tambah Data';
                connectorModal?.removeAttribute('hidden');
            });

            connectorModal?.querySelector('[data-close-connector-modal]')?.addEventListener('click', () => {
                connectorModal.setAttribute('hidden', '');
            });

            connectorModal?.addEventListener('click', (e) => {
                if (e.target === connectorModal) connectorModal.setAttribute('hidden', '');
            });

            shell.querySelector('[data-admin-connectors-tbody]')?.addEventListener('click', async (e) => {
                const editBtn = e.target.closest('[data-edit-connector-row]');
                if (editBtn) {
                    const row = editBtn.closest('[data-connector-row]');
                    if (!row || !connectorForm) return;
                    editingConnectorId = row.dataset.rowId;
                    setConnectorModalError('');
                    if (connectorHeading) connectorHeading.textContent = 'Edit Data Konektor';
                    if (connectorSubmit) connectorSubmit.textContent = 'Simpan Perubahan';

                    ensureSelectOption(connectorForm.elements.station_id, row.dataset.rowStationId || '', row.dataset.rowStationName || `Stasiun #${row.dataset.rowStationId}`);
                    ensureSelectOption(connectorForm.elements.connector_type, row.dataset.rowConnectorType || 'CCS2', row.dataset.rowConnectorType || 'CCS2');
                    connectorForm.elements.power_kw.value = row.dataset.rowPowerKw || '50';
                    connectorForm.elements.charging_speed_category.value = row.dataset.rowSpeed || 'ultra';
                    connectorForm.elements.current_type.value = row.dataset.rowCurrentType || 'DC';
                    connectorForm.elements.available_connectors.value = row.dataset.rowAvailable || '0';
                    connectorForm.elements.total_connectors.value = row.dataset.rowTotal || '1';
                    connectorModal?.removeAttribute('hidden');
                    return;
                }

                const delBtn = e.target.closest('[data-delete-connector-row]');
                if (delBtn) {
                    const row = delBtn.closest('[data-connector-row]');
                    const id = row?.dataset.rowId;
                    if (!id) return;
                    if (!window.confirm('Hapus data konektor ini dari basis data?')) return;

                    try {
                        const res = await apiRequest(`/connectors/${id}`, 'DELETE');
                        reloadWithToast(res.message || 'Data konektor berhasil dihapus.', '#connectors');
                    } catch (err) {
                        showToast(err.message, true);
                    }
                }
            });

            connectorForm?.addEventListener('submit', async (e) => {
                e.preventDefault();
                setConnectorModalError('');

                const payload = {
                    station_id: Number(connectorForm.elements.station_id.value),
                    connector_type: connectorForm.elements.connector_type.value,
                    power_kw: Number(connectorForm.elements.power_kw.value),
                    charging_speed_category: connectorForm.elements.charging_speed_category.value,
                    current_type: connectorForm.elements.current_type.value,
                    available_connectors: Number(connectorForm.elements.available_connectors.value),
                    total_connectors: Number(connectorForm.elements.total_connectors.value),
                };

                const originalText = connectorSubmit?.textContent || '';
                if (connectorSubmit) {
                    connectorSubmit.disabled = true;
                    connectorSubmit.textContent = 'Menyimpan...';
                }

                try {
                    const res = editingConnectorId
                        ? await apiRequest(`/connectors/${editingConnectorId}`, 'PUT', payload)
                        : await apiRequest('/connectors', 'POST', payload);
                    connectorModal?.setAttribute('hidden', '');
                    reloadWithToast(res.message || 'Data konektor berhasil disimpan.', '#connectors');
                } catch (err) {
                    setConnectorModalError(err.message);
                } finally {
                    if (connectorSubmit) {
                        connectorSubmit.disabled = false;
                        connectorSubmit.textContent = originalText;
                    }
                }
            });

            // ==============================================================
            // USERS CRUD (Role Toggle & Delete)
            // ==============================================================
            shell.querySelector('[data-admin-users-tbody]')?.addEventListener('click', async (e) => {
                const roleBtn = e.target.closest('[data-toggle-user-role]');
                if (roleBtn) {
                    if (roleBtn.disabled) return;
                    const id = roleBtn.dataset.userId;
                    const nextRole = roleBtn.dataset.nextRole;
                    const label = nextRole === 'admin' ? 'Admin' : 'Pengguna';
                    if (!window.confirm(`Ubah peran akun ini menjadi ${label}?`)) return;

                    try {
                        const res = await apiRequest(`/users/${id}/role`, 'PATCH', { role: nextRole });
                        reloadWithToast(res.message || 'Peran pengguna diperbarui.', '#users');
                    } catch (err) {
                        showToast(err.message, true);
                    }
                    return;
                }

                const delBtn = e.target.closest('[data-delete-user-row]');
                if (delBtn) {
                    if (delBtn.disabled) return;
                    const id = delBtn.dataset.userId;
                    const name = delBtn.dataset.userName || 'pengguna ini';
                    if (!window.confirm(`Hapus akun "${name}" dari basis data?`)) return;

                    try {
                        const res = await apiRequest(`/users/${id}`, 'DELETE');
                        reloadWithToast(res.message || 'Akun pengguna dihapus.', '#users');
                    } catch (err) {
                        showToast(err.message, true);
                    }
                }
            });

            // ==============================================================
            // REPORTS CRUD (Inline Status Update & Delete)
            // ==============================================================
            const reportsTbody = shell.querySelector('[data-admin-reports-tbody]');
            reportsTbody?.addEventListener('change', async (e) => {
                const select = e.target.closest('[data-report-status-select]');
                if (!select) return;
                const id = select.dataset.reportId;
                const status = select.value;

                try {
                    const res = await apiRequest(`/reports/${id}/status`, 'PATCH', { status });
                    reloadWithToast(res.message || 'Status laporan diperbarui.', '#reports');
                } catch (err) {
                    showToast(err.message, true);
                }
            });

            reportsTbody?.addEventListener('click', async (e) => {
                const delBtn = e.target.closest('[data-delete-report-row]');
                if (!delBtn) return;
                const id = delBtn.dataset.reportId;
                if (!window.confirm('Hapus laporan kondisi ini?')) return;

                try {
                    const res = await apiRequest(`/reports/${id}`, 'DELETE');
                    reloadWithToast(res.message || 'Laporan dihapus.', '#reports');
                } catch (err) {
                    showToast(err.message, true);
                }
            });

            // ==============================================================
            // REVIEWS DELETE
            // ==============================================================
            shell.querySelector('[data-admin-reviews-tbody]')?.addEventListener('click', async (e) => {
                const delBtn = e.target.closest('[data-delete-review-row]');
                if (!delBtn) return;
                const id = delBtn.dataset.reviewId;
                if (!window.confirm('Hapus ulasan pengguna ini?')) return;

                try {
                    const res = await apiRequest(`/reviews/${id}`, 'DELETE');
                    reloadWithToast(res.message || 'Ulasan pengguna dihapus.', '#reviews');
                } catch (err) {
                    showToast(err.message, true);
                }
            });

            // ==============================================================
            // ADMIN PROFILE UPDATE
            // ==============================================================
            const profileForm = shell.querySelector('[data-admin-profile-form]');
            const profileError = shell.querySelector('[data-profile-error]');
            const profileSubmitBtn = shell.querySelector('[data-profile-submit-btn]');

            profileForm?.addEventListener('submit', async (e) => {
                e.preventDefault();
                profileError?.setAttribute('hidden', '');

                const payload = {
                    name: profileForm.elements.name.value.trim(),
                    phone: profileForm.elements.phone.value.trim() || null,
                };

                const password = profileForm.elements.password.value;
                if (password !== '') {
                    payload.password = password;
                    payload.password_confirmation = profileForm.elements.password_confirmation.value;
                }

                const originalText = profileSubmitBtn?.textContent || '';
                if (profileSubmitBtn) {
                    profileSubmitBtn.disabled = true;
                    profileSubmitBtn.textContent = 'Menyimpan...';
                }

                try {
                    const res = await apiRequest('/profile', 'PATCH', payload);
                    reloadWithToast(res.message || 'Profil admin berhasil diperbarui.', '#profile');
                } catch (err) {
                    if (profileError) {
                        profileError.textContent = err.message;
                        profileError.removeAttribute('hidden');
                    }
                } finally {
                    if (profileSubmitBtn) {
                        profileSubmitBtn.disabled = false;
                        profileSubmitBtn.textContent = originalText;
                    }
                }
            });
        })();
    </script>
</x-filament-panels::page>
