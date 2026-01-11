<style>
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .modal-content {
        background: #fff;
        width: 800px;
        max-width: 800px;
        padding: 22px;
        border-radius: 10px;
        position: relative;
        transform: translateY(30px);
        opacity: 0;
        transition: transform 0.3s ease, opacity 0.3s ease;
    }

    .modal-overlay.active .modal-content {
        transform: translateY(0);
        opacity: 1;
    }

    .modal-close {
        position: absolute;
        top: 14px;
        right: 14px;
        background: none;
        border: none;
        font-size: 20px;
        color: #888;
        cursor: pointer;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .modal-close:hover {
        background: #f0f0f0;
        color: #333;
    }

    .segmented-control {
        display: flex;
        background: #f9f9f9;
        padding: 3px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
    }

    .segmented-control input[type="radio"] {
        display: none;
    }

    .segmented-control label {
        flex: 1;
        padding: 7px 15px;
        cursor: pointer;
        text-align: center;
        font-size: 13px;
        color: #666;
        border: 1px solid transparent;
        border-radius: 6px;
        transition: all 0.2s ease;
        margin: 0;
        white-space: nowrap;
    }

    .segmented-control input[type="radio"]:checked+label {
        background: #fff;
        border: 1px solid #ddd;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .segmented-control input[value="0"]:checked+label {
        color: #07426e;
    }

    .segmented-control input[value="1"]:checked+label {
        color: crimson;
    }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 28px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ff6b6b;
        transition: .3s;
        border-radius: 14px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
    }

    input:checked+.slider {
        background-color: #4cd964;
    }

    input:checked+.slider:before {
        transform: translateX(32px);
    }

    .nested {
        margin-left: 35px;
        max-height: 0;
        overflow: hidden;
        opacity: 0;
        transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease;
    }

    .nested.expanded {
        max-height: 2000px;
        opacity: 1;
    }

    .menu-tree-wrapper {
        flex: 1;
        overflow-y: scroll;
        overflow-x: hidden;
        scrollbar-gutter: stable;
        padding-right: 10px;
        -ms-overflow-style: none;
        scrollbar-width: thin;
        scrollbar-color: rgba(0, 0, 0, 0.05) transparent;
        transition: all 0.2s ease-in-out;
    }

    .menu-tree-wrapper::-webkit-scrollbar {
        width: 6px;
    }

    .menu-tree-wrapper::-webkit-scrollbar-track {
        background: transparent;
    }

    .menu-tree-wrapper::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 10px;
        transition: background 0.2s ease;
    }

    .menu-tree-wrapper:hover::-webkit-scrollbar-thumb {
        background: rgba(10, 61, 98, 0.2);
    }

    .menu-tree-wrapper::-webkit-scrollbar-thumb:hover {
        background: rgba(10, 61, 98, 0.5) !important;
    }

    .menu-tree-wrapper:hover {
        scrollbar-color: rgba(10, 61, 98, 0.2) transparent;
    }

    .menu-tree {
        list-style: none;
        padding-left: 0;
        margin: 0;
        display: block;
    }

    .menu-tree li {
        margin: 10px 0;
    }

    .menu-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: #fff;
        border: 1px solid #eef1f6;
        padding: 12px 14px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(14, 30, 37, 0.03);
    }

    .menu-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .menu-title {
        font-weight: 600;
        color: #12263f;
    }

    .menu-badge {
        display: flex;
        align-items: center;
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 999px;
        background: #e9f5ef;
        color: #1e7a43;
        border: 1px solid #dff0e6;
    }

    .menu-badge.inactive {
        background: #fff3f3;
        color: #c0392b;
        border: 1px solid #ffe6e6;
    }

    .functional-badge {
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 999px;
        background: #f9f9f9;
        color: #1e7a43;
        border: 1px solid #dff0e6;
        text-transform: capitalize;
    }

    .functional-badge.type-functional {
        color: #07426e;
    }

    .functional-badge.type-multi {
        color: crimson;
    }

    .menu-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .icon-btn {
        background: transparent;
        border: none;
        padding: 8px;
        border-radius: 6px;
        cursor: pointer;
        color: #5b6b7a;
    }

    .icon-btn:hover {
        background: #f3f6f9;
        color: #0a3d62;
    }

    .collapse-toggle {
        background: transparent;
        border: none;
        padding: 6px;
        border-radius: 6px;
        cursor: pointer;
        color: #66788a;
    }

    .collapse-toggle:hover {
        background: #f3f6f9;
        color: #0a3d62;
    }

    .small-note {
        color: #8a99a8;
        font-size: 13px;
    }

    .menu-card.drag-over-above,
    .menu-card.drag-over-below {
        position: relative;
    }

    .menu-card.drag-over-above::before,
    .menu-card.drag-over-below::after {
        content: '';
        position: absolute;
        left: 12px;
        right: 12px;
        height: 3px;
        background: #fbc531;
        border-radius: 2px;
    }

    .menu-card.drag-over-above::before {
        top: -6px;
    }

    .menu-card.drag-over-below::after {
        bottom: -6px;
    }

    .drag-handle {
        background: transparent;
        border: none;
        padding: 6px;
        cursor: move;
        color: #a0aec0;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
    }

    .drag-handle:hover {
        background: #f3f6f9;
        color: #66788a;
    }

    .menu-card.dragging {
        opacity: 0.6;
    }

    .content-status-badge {
        font-size: 12px;
        padding: 3px 10px;
        border-radius: 6px;
    }

    .menu-right {
        margin-left: auto;
        margin-right: 0;
        display: flex;
        align-items: center;
    }

    .status-empty {
        background: #fdf2f2;
        color: #9b1c1c;
        border: 1px solid #f8d7da;
    }

    .status-multi {
        background: #f9f9f9;
        color: #07426e;
        border: 1px solid #ddd;
        padding: 4px 8px;
    }

    .content-preview {
        font-size: 12px;
        color: #555;
        background: #f9fafb;
        padding: 5px 12px;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
        width: 500px;
        overflow: hidden;
        white-space: nowrap;
    }

    .ace_scroller.ace_scroll-left:after {
        box-shadow: none;
    }
</style>