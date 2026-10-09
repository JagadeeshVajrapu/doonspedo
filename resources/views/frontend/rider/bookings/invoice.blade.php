<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $booking->displayReference() }} - Doonspedo</title>
    @include('partials.ui.favicon')
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 40px; background: #fff; color: #333; }
        .invoice-container { max-width: 800px; margin: auto; border: 1px solid #eee; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #f9f9f9; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #cddc29; }
        .logo span { color: #000; }
        .invoice-meta { text-align: right; }
        .invoice-meta h2 { margin: 0; font-size: 20px; color: #555; }
        .invoice-meta p { margin: 5px 0; font-size: 14px; color: #888; }
        .party-info { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .party-info h4 { margin-bottom: 10px; color: #cddc29; text-transform: uppercase; font-size: 13px; letter-spacing: 1px; }
        .party { width: 45%; }
        .party p { margin: 0; font-size: 15px; line-height: 1.6; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        .table th { background: #f9f9f9; padding: 15px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px; color: #555; }
        .table td { padding: 15px; border-bottom: 1px solid #fbfbfb; font-size: 15px; }
        .footer { text-align: center; margin-top: 50px; padding-top: 20px; border-top: 1px solid #f9f9f9; color: #aaa; font-size: 12px; }
        .total-section { display: flex; justify-content: flex-end; }
        .total-box { width: 300px; }
        .total-row { display: flex; justify-content: space-between; padding: 10px 0; font-size: 15px; }
        .total-row.grand-total { border-top: 2px solid #cddc29; margin-top: 10px; padding-top: 15px; font-weight: bold; font-size: 18px; color: #000; }
        @media print {
            body { padding: 0; }
            .invoice-container { border: none; box-shadow: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="background: #cddc29; border: none; padding: 12px 25px; border-radius: 5px; font-weight: bold; cursor: pointer; color: #000;">
            Print Invoice / Save as PDF
        </button>
    </div>

    <div class="invoice-container">
        <div class="header">
            <div class="logo">DOON<span>SPEDO</span></div>
            <div class="invoice-meta">
                <h2>INVOICE {{ $booking->displayReference() }}</h2>
                <p>Date: {{ $booking->created_at->format('d M, Y') }}</p>
                <p>Status: {{ strtoupper($booking->status) }}</p>
            </div>
        </div>

        <div class="party-info">
            <div class="party">
                <h4>Bill To:</h4>
                <p><strong>{{ $booking->user->name }}</strong></p>
                <p>{{ $booking->user->email }}</p>
                <p>{{ $booking->user->mobile }}</p>
            </div>
            <div class="party">
                <h4>Ride Provider:</h4>
                <p><strong>{{ $booking->driver->name }}</strong></p>
                <p>{{ $booking->driver->vehicle_number }}</p>
                <p>White Maruti Swift (Assigned)</p>
            </div>
        </div>

        <div class="party-info">
            <div class="party" style="width: 100%;">
                <h4>Route Information:</h4>
                <p><strong>From:</strong> {{ $booking->pickup_location }}</p>
                <p><strong>To:</strong> {{ $booking->dropoff_location }}</p>
                <p><strong>Service:</strong> {{ ucfirst($booking->service_type) }}</p>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>DESCRIPTION</th>
                    <th>BASE PRICE</th>
                    <th>TAX / GST</th>
                    <th style="text-align: right;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Ride Fare (Booking {{ $booking->displayReference() }})</td>
                    <td>₹{{ number_format($booking->fare, 2) }}</td>
                    <td>₹0.00</td>
                    <td style="text-align: right;">₹{{ number_format($booking->fare, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-box">
                <div class="total-row">
                    <span>Base Fare</span>
                    <span>₹{{ number_format($booking->fare, 2) }}</span>
                </div>
                <div class="total-row">
                    <span>Tax / GST</span>
                    <span>₹0.00</span>
                </div>
                <div class="total-row grand-total">
                    <span>Total Amount</span>
                    <span>₹{{ number_format($booking->fare, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for choosing Doonspedo!</p>
            <p>For support, please contact us at support@doonspedo.com</p>
            <p>&copy; {{ date('Y') }} Doonspedo | Developed by <a href="https://webfasttech.com/" target="_blank" rel="noopener noreferrer">Webfast Technology</a></p>
        </div>
    </div>
</body>
</html>
