<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fee Receipt - {{ $receiptNumber }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #2196F3;
            padding: 0;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-bottom: 4px solid #f0f0f0;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .header p {
            font-size: 14px;
            margin: 3px 0;
        }

        .receipt-title {
            background-color: #f8f9fa;
            padding: 15px 30px;
            border-bottom: 2px solid #e0e0e0;
        }

        .receipt-title h2 {
            color: #2196F3;
            font-size: 24px;
            margin: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .receipt-number {
            font-size: 16px;
            color: #666;
            font-weight: normal;
        }

        .content {
            padding: 30px;
        }

        .info-section {
            margin-bottom: 25px;
        }

        .section-title {
            background-color: #f0f0f0;
            padding: 10px 15px;
            font-weight: bold;
            font-size: 14px;
            color: #2196F3;
            margin-bottom: 15px;
            border-left: 4px solid #2196F3;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            padding: 8px 15px 8px 0;
            font-weight: bold;
            width: 40%;
            color: #555;
        }

        .info-value {
            display: table-cell;
            padding: 8px 0;
            color: #333;
        }

        .fee-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .fee-table th {
            background-color: #2196F3;
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 13px;
            font-weight: bold;
        }

        .fee-table td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
        }

        .fee-table tr:last-child td {
            border-bottom: none;
        }

        .amount-row {
            background-color: #f8f9fa;
        }

        .total-row {
            background-color: #e3f2fd;
            font-weight: bold;
            font-size: 14px;
        }

        .total-row td {
            padding: 15px 12px;
            border-top: 2px solid #2196F3;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-paid {
            background-color: #4CAF50;
            color: white;
        }

        .status-partial {
            background-color: #2196F3;
            color: white;
        }

        .status-pending {
            background-color: #FFC107;
            color: #333;
        }

        .status-overdue {
            background-color: #f44336;
            color: white;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
        }

        .signature-section {
            display: table;
            width: 100%;
            margin-top: 60px;
        }

        .signature-block {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 10px;
        }

        .signature-line {
            border-top: 2px solid #333;
            margin: 40px auto 5px;
            width: 200px;
        }

        .signature-label {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }

        .notes {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-top: 20px;
            font-size: 11px;
            color: #856404;
        }

        .notes strong {
            display: block;
            margin-bottom: 5px;
            font-size: 12px;
        }

        .watermark {
            text-align: center;
            margin-top: 30px;
            padding: 15px;
            background-color: #f0f0f0;
            border-radius: 5px;
            font-size: 10px;
            color: #999;
        }

        .amount-in-words {
            background-color: #e8f5e9;
            padding: 12px;
            margin: 15px 0;
            border-left: 4px solid #4CAF50;
            font-style: italic;
            color: #2e7d32;
        }

        .amount-in-words strong {
            font-style: normal;
            color: #1b5e20;
        }

        @media print {
            body {
                padding: 0;
            }
            .receipt-container {
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="header">
            <h1>🎓 SCHOOL MANAGEMENT SYSTEM</h1>
            <p>Kathmandu, Nepal</p>
            <p>Phone: +977-1-XXXXXXX | Email: info@school.edu.np</p>
            <p>www.schoolmanagement.edu.np</p>
        </div>

        <!-- Receipt Title -->
        <div class="receipt-title">
            <h2>
                <span>FEE RECEIPT</span>
                <span class="receipt-number">#{{ $receiptNumber }}</span>
            </h2>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Student Information -->
            <div class="info-section">
                <div class="section-title">STUDENT INFORMATION</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Student Name:</div>
                        <div class="info-value">{{ $student->name }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Admission Number:</div>
                        <div class="info-value">{{ $student->admission_number }}</div>
                    </div>
                    @if($currentClass)
                    <div class="info-row">
                        <div class="info-label">Class:</div>
                        <div class="info-value">{{ $currentClass->name }} {{ $currentClass->section ? '- ' . $currentClass->section : '' }}</div>
                    </div>
                    @endif
                    <div class="info-row">
                        <div class="info-label">Email:</div>
                        <div class="info-value">{{ $student->email }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Phone:</div>
                        <div class="info-value">{{ $student->phone }}</div>
                    </div>
                    @if($student->guardian_name)
                    <div class="info-row">
                        <div class="info-label">Guardian Name:</div>
                        <div class="info-value">{{ $student->guardian_name }}</div>
                    </div>
                    @endif
                    @if($student->guardian_phone)
                    <div class="info-row">
                        <div class="info-label">Guardian Phone:</div>
                        <div class="info-value">{{ $student->guardian_phone }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Fee Details -->
            <div class="info-section">
                <div class="section-title">FEE DETAILS</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Receipt Number:</div>
                        <div class="info-value"><strong>{{ $receiptNumber }}</strong></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Receipt Date:</div>
                        <div class="info-value">{{ $generatedDate }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Fee Type:</div>
                        <div class="info-value">{{ $fee->fee_type }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Academic Year:</div>
                        <div class="info-value">{{ $fee->academic_year }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Due Date:</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($fee->due_date)->format('d M Y') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Payment Status:</div>
                        <div class="info-value">
                            <span class="status-badge status-{{ strtolower($fee->status) }}">
                                {{ $fee->status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Amount Breakdown Table -->
            <table class="fee-table">
                <thead>
                    <tr>
                        <th style="width: 50%;">Description</th>
                        <th style="width: 25%; text-align: right;">Amount (NPR)</th>
                        <th style="width: 25%; text-align: right;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $fee->fee_type }} - {{ $fee->academic_year }}</td>
                        <td style="text-align: right;">Rs. {{ number_format($fee->amount, 2) }}</td>
                        <td style="text-align: right;">Total</td>
                    </tr>
                    <tr class="amount-row">
                        <td>Amount Paid</td>
                        <td style="text-align: right; color: #4CAF50; font-weight: bold;">
                            Rs. {{ number_format($fee->paid_amount, 2) }}
                        </td>
                        <td style="text-align: right;">Paid</td>
                    </tr>
                    <tr class="amount-row">
                        <td>Pending Amount</td>
                        <td style="text-align: right; color: #f44336; font-weight: bold;">
                            Rs. {{ number_format($fee->pending_amount, 2) }}
                        </td>
                        <td style="text-align: right;">Due</td>
                    </tr>
                    <tr class="total-row">
                        <td>Total Payable</td>
                        <td style="text-align: right; font-size: 16px;">
                            Rs. {{ number_format($fee->amount, 2) }}
                        </td>
                        <td style="text-align: right;">-</td>
                    </tr>
                </tbody>
            </table>

            <!-- Amount in Words -->
            @php
                $amountInWords = \App\Helpers\NumberToWords::convert($fee->paid_amount);
            @endphp
            <div class="amount-in-words">
                <strong>Amount Paid in Words:</strong> 
                {{ ucfirst($amountInWords) }} Rupees Only
            </div>

            @if($fee->remarks)
            <div class="info-section">
                <div class="section-title">REMARKS</div>
                <p style="padding: 10px 0; color: #666;">{{ $fee->remarks }}</p>
            </div>
            @endif

            <!-- Notes -->
            <div class="notes">
                <strong>Important Notes:</strong>
                <ul style="margin: 5px 0 0 20px; padding: 0;">
                    <li>This is a computer-generated receipt and does not require a signature.</li>
                    <li>Please keep this receipt for your records.</li>
                    <li>For any queries, contact the school office during working hours.</li>
                    <li>Payment is non-refundable except in special circumstances approved by management.</li>
                </ul>
            </div>

            <!-- Signature Section -->
            <div class="signature-section">
                <div class="signature-block">
                    <div class="signature-line"></div>
                    <div class="signature-label">Received By</div>
                    <div class="signature-label" style="margin-top: 3px;">(Accounts Officer)</div>
                </div>
                <div class="signature-block">
                    <div class="signature-line"></div>
                    <div class="signature-label">Authorized Signature</div>
                    <div class="signature-label" style="margin-top: 3px;">(Principal/Administrator)</div>
                </div>
            </div>

            <!-- Footer/Watermark -->
            <div class="watermark">
                <p><strong>THANK YOU FOR YOUR PAYMENT</strong></p>
                <p style="margin-top: 5px;">Generated on {{ $generatedDate }}</p>
                <p style="margin-top: 3px; font-size: 9px;">
                    This is a system-generated document. For verification, contact: accounts@school.edu.np
                </p>
            </div>
        </div>
    </div>
</body>
</html>