<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            border: 1px solid #e1e1e1;
            border-radius: 10px;
            overflow: hidden;
        }

        .header {
            background-color: #0054a6;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 20px;
        }

        .section-title {
            color: #0054a6;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 5px;
            margin-top: 25px;
            margin-bottom: 10px;
        }

        .field-group {
            margin-bottom: 10px;
            display: flex;
        }

        .label {
            font-weight: bold;
            width: 180px;
            color: #666;
            font-size: 13px;
        }

        .value {
            color: #333;
            font-size: 14px;
            flex: 1;
        }

        .footer {
            background-color: #f9f9f9;
            padding: 15px;
            text-align: center;
            font-size: 11px;
            color: #999;
        }

        .description-box {
            background-color: #f5f8fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 5px;
            font-style: italic;
            border-left: 4px solid #0054a6;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2 style="margin:0;">Product Complaint Report</h2>
            <p style="margin:5px 0 0 0; font-size:12px; opacity:0.8;">Raising Date:
                {{ $complaint->complaint_date->format('d/m/Y') }}
            </p>
        </div>

        <div class="content">
            <div class="section-title">SECTION – A: Product Identification</div>
            <div class="field-group"><span class="label">Product Name:</span> <span
                    class="value">{{ $complaint->product_name }}</span></div>
            <div class="field-group"><span class="label">Batch Number:</span> <span
                    class="value">{{ $complaint->batch_number }}</span></div>
            <div class="field-group"><span class="label">Manufacturing Date:</span> <span
                    class="value">{{ $complaint->mfg_date->format('d/m/Y') }}</span></div>
            <div class="field-group"><span class="label">Expiry Date:</span> <span
                    class="value">{{ $complaint->exp_date->format('d/m/Y') }}</span></div>
            <div class="field-group"><span class="label">Strength:</span> <span
                    class="value">{{ $complaint->strength }}</span></div>
            <div class="field-group"><span class="label">Dosage Form:</span> <span
                    class="value">{{ $complaint->dosage_form }}</span></div>

            <div class="section-title">SECTION – B: Nature of Complaint</div>
            <div class="field-group"><span class="label">Type:</span> <span
                    class="value">{{ $complaint->complaint_type }}</span></div>
            <div class="description-box">
                <strong>Description:</strong><br>
                {{ $complaint->complaint_description }}
            </div>

            <div class="section-title">SECTION – C: Complainant Information</div>
            <div class="field-group"><span class="label">Name:</span> <span
                    class="value">{{ $complaint->complainant_name }}</span></div>
            <div class="field-group"><span class="label">Contact:</span> <span
                    class="value">{{ $complaint->contact_number }}</span></div>
            <div class="field-group"><span class="label">Address:</span> <span
                    class="value">{{ $complaint->address }}</span></div>
        </div>

        <div class="footer">
            This is an automated report generated from the Orion Pharma Limited website.
        </div>
    </div>
</body>

</html>