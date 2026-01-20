<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>E-Ticket AMPR</title>
    <style>
        /* 1. SETUP KERTAS & BACKGROUND */
        @page {
            margin: 0px;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 30px 20px;
            background-color: #E7E5D7; /* Cement Beige - Background Luar */
            color: #333333;
        }

        .container {
            width: 100%;
        }

        /* 2. CARD UTAMA */
        .ticket-card {
            background-color: #ffffff;
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            page-break-inside: avoid;
            /* Border tipis Court Green agar terdefinisi */
            border: 1px solid #869A69; 
        }

        /* 3. HEADER (Dominan Blue + Aksen Green) */
        .header {
            background-color: #65AAC2; /* Court Blue */
            color: #ffffff;
            padding: 25px 20px;
            text-align: center;
            /* Garis bawah Tennis Ball Green sebagai pemanis */
            border-bottom: 6px solid #ADBA5E; 
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-weight: 900;
        }

        .header p {
            margin: 4px 0 0;
            font-size: 10px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: normal;
        }

        /* CONTENT AREA */
        .content {
            padding: 25px 30px;
        }

        /* Kode Booking */
        .booking-code-label {
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            color: #869A69; /* Court Green */
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .booking-code {
            text-align: center;
            font-size: 30px;
            font-weight: bold;
            color: #65AAC2; /* Court Blue - Menyamakan dengan Header */
            margin: 0 0 20px 0;
            font-family: 'Courier New', Courier, monospace;
            letter-spacing: 2px;
        }

        /* QR Code Wrapper */
        .qr-container {
            text-align: center;
            margin-bottom: 25px;
        }

        .qr-box {
            display: inline-block;
            padding: 10px;
            background-color: #F9F9F9;
            border: 1px dashed #E7E5D7; /* Border Beige halus */
            border-radius: 8px;
        }

        .qr-img {
            width: 130px;
            height: 130px;
            display: block;
        }

        /* TABEL DETAIL */
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .details-table td {
            padding: 10px 0;
            vertical-align: top;
            /* Garis pemisah halus warna Beige */
            border-bottom: 1px solid #E7E5D7; 
        }

        .details-table tr:last-child td {
            border-bottom: none;
        }

        /* Label menggunakan Court Green */
        .label {
            font-size: 9px;
            color: #869A69; 
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 3px;
        }

        /* Value Utama */
        .value {
            font-size: 13px;
            font-weight: bold;
            color: #333;
            line-height: 1.4;
        }

        /* FOOTER (Background Beige agar menyatu dengan body) */
        .footer {
            background-color: #F4F4F0; /* Versi sangat muda dari Beige */
            text-align: center;
            padding: 15px;
            font-size: 9px;
            color: #869A69; /* Teks Hijau */
            border-top: 1px solid #E7E5D7;
        }
        
        .footer-note {
            font-style: italic;
            opacity: 0.8;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="ticket-card">

        <!-- Header: Blue with Green Accent line -->
        <div class="header">
            <h1>E-TICKET LAPANGAN</h1>
            <p>Apartemen Mediterania Palace</p>
        </div>

        <div class="content">
            <!-- Kode Booking -->
            <div class="booking-code-label">KODE BOOKING</div>
            <div class="booking-code">{{ strtoupper($booking->booking_code) }}</div>

            <!-- QR Code -->
            <div class="qr-container">
                <div class="qr-box">
                    <img src="{{ $qrCode }}" class="qr-img" />
                </div>
            </div>

            <!-- Detail Info -->
            <table class="details-table">
                <tr>
                    <td width="60%">
                        <span class="label">Tanggal Main</span>
                        <span class="value">
                            {{ \Carbon\Carbon::parse($booking->start_time)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                        </span>
                    </td>
                    <td width="40%" style="text-align:right;">
                        <span class="label">Unit Apartemen</span>
                        <!-- Unit menggunakan Court Blue agar menonjol -->
                        <span class="value" style="font-size:15px; color:#65AAC2;">
                            {{ $booking->unit->unit_number }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label">Jam</span>
                        <span class="value">
                            {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                        </span>
                    </td>
                    <td style="text-align:right;">
                        <span class="label">Status</span>
                        <!-- Status menggunakan Tennis Ball Green (Available/Success Color) -->
                        <span class="value" style="color:#ADBA5E; letter-spacing: 1px;">CONFIRMED</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <span class="label">Nama Pemain</span>
                        <span class="value" style="word-wrap:break-word;">
                            {{ is_array($booking->player_names) ? $booking->player_names[0] : $booking->player_names }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Footer: Light Theme -->
        <div class="footer">
            <div style="font-weight: bold; margin-bottom: 2px;">SCAN UNTUK CHECK-IN</div>
            <div class="footer-note">
                Harap tunjukkan tiket ini kepada petugas keamanan di lobby.<br>
                Datang 10 menit sebelum jadwal main.
            </div>
        </div>

    </div>
</div>

</body>
</html>