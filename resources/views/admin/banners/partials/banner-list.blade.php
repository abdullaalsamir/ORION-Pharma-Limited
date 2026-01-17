<div class="image-area-header">
    <div style="display:flex; justify-content: space-between; align-items: center;">
        <div>
            <h3 style="margin:0">{{ $menu->name }}</h3>
            <small style="color:#64748b">48:9 Ratio Images</small>
        </div>
        <button onclick="openUploadModal()"
            style="background:#1e7a43; color:#fff; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight: 500;">
            <i class="fas fa-plus"></i> Add Image
        </button>
    </div>
</div>

<div class="image-area-body menu-tree-wrapper">
    <style>
        .image-grid-48-9 {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }

        .image-card-wide {
            width: 100%;
            aspect-ratio: 48 / 9;
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .image-card-wide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: 0.3s;
        }

        .image-card-wide.is-inactive img {
            opacity: 0.4;
            filter: grayscale(20%);
        }

        .image-card-wide .card-actions {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 5px;
            opacity: 0;
            transition: 0.3s;
            z-index: 5;
        }

        .image-card-wide:hover .card-actions {
            opacity: 1;
        }

        .action-btn {
            background: rgba(255, 255, 255, 0.9);
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #475569;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .action-btn:hover {
            background: #fff;
            color: #0a3d62;
        }
    </style>

    <div class="image-grid-48-9">
        @forelse($banners as $banner)
            <div class="image-card-wide {{ !$banner->is_active ? 'is-inactive' : '' }}">
                <img src="{{ url($menu->full_slug . '/' . $banner->file_name) }}?v={{ time() }}" alt="banner">

                <div class="card-actions">
                    <button class="action-btn"
                        onclick="openEditModal({{ $banner->id }}, '{{ $banner->file_name }}', '{{ $menu->full_slug }}', {{ $banner->is_active }})">
                        <i class="fas fa-pen"></i>
                    </button>
                    <button class="action-btn" style="color:#e11d48" onclick="deleteImage({{ $banner->id }})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>

                @if(!$banner->is_active)
                    <div
                        style="position:absolute; bottom:10px; left:10px; background:rgba(192, 57, 43, 0.9); color:#fff; font-size:10px; font-weight:bold; padding:3px 10px; border-radius:4px; z-index: 5; text-transform: uppercase;">
                        Inactive
                    </div>
                @endif
            </div>
        @empty
            <div
                style="padding: 60px; text-align: center; color: #94a3b8; border: 2px dashed #f1f5f9; border-radius: 12px; margin-top:20px;">
                <i class="fas fa-cloud-upload-alt" style="font-size: 40px; margin-bottom: 10px;"></i>
                <p>No banners found. Upload your first 48:9 banner.</p>
            </div>
        @endforelse
    </div>
</div>