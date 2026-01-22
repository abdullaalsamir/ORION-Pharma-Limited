@extends('admin.layouts.app')
@section('title', 'Product Complaints')

@section('content')
    <div class="card" style="height: calc(100vh - 100px);">
        <div class="card-header">
            <h3 style="margin:0">Submitted Product Complaints</h3>
            <small>Total Received: {{ $complaints->count() }}</small>
        </div>

        <div class="card-body scrollable-content menu-tree-wrapper">
            @forelse($complaints as $c)
                <div class="menu-card"
                    style="flex-direction: column; align-items: stretch; margin-bottom: 20px; padding: 20px;">
                    <div
                        style="display:flex; justify-content: space-between; border-bottom: 1px solid #eee; pb-10; margin-bottom: 15px; padding-bottom: 10px;">
                        <div>
                            <span style="font-size: 10px; color: #999; text-transform: uppercase; font-weight: bold;">Complaint
                                From:</span>
                            <h4 style="margin:0; color: #0a3d62;">{{ $c->complainant_name }} ({{ $c->contact_number }})</h4>
                        </div>
                        <div style="text-align: right;">
                            <span style="font-size: 10px; color: #999; text-transform: uppercase; font-weight: bold;">Submitted
                                On:</span>
                            <div style="font-weight: bold;">{{ $c->complaint_date->format('d/m/Y') }}</div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <p style="margin:0 0 5px 0; font-size: 12px;"><strong>Product:</strong> {{ $c->product_name }}</p>
                            <p style="margin:0 0 5px 0; font-size: 12px;"><strong>Batch:</strong> {{ $c->batch_number }}</p>
                            <p style="margin:0 0 5px 0; font-size: 12px;"><strong>Dosage Form:</strong> {{ $c->dosage_form }}
                            </p>
                        </div>
                        <div>
                            <p style="margin:0 0 5px 0; font-size: 12px;"><strong>MFG:</strong>
                                {{ $c->mfg_date->format('d/m/Y') }}</p>
                            <p style="margin:0 0 5px 0; font-size: 12px;"><strong>EXP:</strong>
                                {{ $c->exp_date->format('d/m/Y') }}</p>
                            <p style="margin:0 0 5px 0; font-size: 12px;"><strong>Type:</strong> {{ $c->complaint_type }}</p>
                        </div>
                    </div>

                    <div
                        style="background: #f8fafc; padding: 10px; border-radius: 8px; margin-top: 15px; border: 1px solid #eef1f6;">
                        <p style="margin:0; font-size: 12px; color: #555;"><strong>Description:</strong>
                            {{ $c->complaint_description }}</p>
                    </div>

                    <div style="margin-top: 15px; display: flex; justify-content: space-between; align-items: flex-end;">
                        <p style="margin:0; font-size: 11px; color: #888; max-width: 80%;"><strong>Address:</strong>
                            {{ $c->address }}</p>
                        <form action="{{ route('admin.complaint.delete', $c) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="icon-btn" style="color:red;" onclick="return confirm('Delete this record?')"><i
                                    class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
            @empty
                <p style="text-align:center; padding:50px; color:#ccc;">No complaints received yet.</p>
            @endforelse
        </div>
    </div>
@endsection