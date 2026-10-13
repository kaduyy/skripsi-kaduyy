<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>
        Laporan Pendapatan Alan's Art Hair Salon
    </title>

    <style>

        @page {
            margin: 25px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #1A3B32;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 14px;
            font-weight: normal;
        }

        .header p {
            margin: 3px 0;
            color: #666;
        }

        .line {
            border-top: 2px solid #1A3B32;
            margin-top: 15px;
            margin-bottom: 20px;
        }

        .period {
            margin-bottom: 15px;
            font-size: 11px;
        }

        .summary {
            width: 100%;
            margin-bottom: 20px;
        }

        .summary td {
            width: 25%;
            border: 1px solid #ddd;
            padding: 10px;
            vertical-align: top;
        }

        .summary-title {
            font-size: 10px;
            color: #777;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 14px;
            font-weight: bold;
            color: #1A3B32;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.data th {
            background-color: #1A3B32;
            color: white;
            padding: 8px 6px;
            border: 1px solid #1A3B32;
            text-align: left;
        }

        table.data td {
            padding: 7px 6px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        table.data tr:nth-child(even) {
            background-color: #f8f8f8;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .status {
            font-weight: bold;
        }

        .total-row td {
            background-color: #f0f4f2;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #777;
        }

        .signature {
            margin-top: 50px;
            width: 250px;
            margin-left: auto;
            text-align: center;
        }

        .signature-space {
            height: 60px;
        }

    </style>

</head>

<body>

    {{-- ================================================= --}}
    {{-- HEADER --}}
    {{-- ================================================= --}}

    <div class="header">

        <h1>
            ALAN'S ART HAIR SALON
        </h1>

        <h2>
            LAPORAN PENDAPATAN
        </h2>

        <p>
            Jalan Taman Baloi Mas, Kompleks Baloi Indah
        </p>

        <p>
            Lubuk Baja, Batam
        </p>

    </div>

    <div class="line"></div>


    {{-- ================================================= --}}
    {{-- PERIODE --}}
    {{-- ================================================= --}}

    <div class="period">

        <strong>Periode Laporan:</strong>

        {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}

        -

        {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}

    </div>


    {{-- ================================================= --}}
    {{-- RINGKASAN --}}
    {{-- ================================================= --}}

    <table class="summary">

        <tr>

            <td>

                <div class="summary-title">
                    Pendapatan Layanan Selesai
                </div>

                <div class="summary-value">
                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                </div>

            </td>


            <td>

                <div class="summary-title">
                    Total Reservasi
                </div>

                <div class="summary-value">
                    {{ $totalReservasi }}
                </div>

            </td>


            <td>

                <div class="summary-title">
                    Uang Diterima
                </div>

                <div class="summary-value">
                    Rp {{ number_format($totalUangDiterima, 0, ',', '.') }}
                </div>

            </td>


            <td>

                <div class="summary-title">
                    Reservasi Selesai
                </div>

                <div class="summary-value">
                    {{ $totalReservasi }}
                </div>

            </td>

        </tr>

    </table>

    <p style="margin-top: 8px; color: #666; font-size: 10px;">
        Catatan: DP adalah uang muka, bukan pendapatan penuh. Pendapatan dihitung setelah layanan selesai.
    </p>


    {{-- ================================================= --}}
    {{-- DETAIL RESERVASI --}}
    {{-- ================================================= --}}

    <h3>
        Detail Pendapatan
    </h3>


    <table class="data">

        <thead>

            <tr>

                <th style="width: 5%;">
                    No
                </th>

                <th style="width: 18%;">
                    Pelanggan
                </th>

                <th style="width: 20%;">
                    Layanan
                </th>

                <th style="width: 14%;">
                    Tanggal
                </th>

                <th style="width: 14%;">
                    Metode
                </th>

                <th style="width: 13%;">
                    Status
                </th>

                <th style="width: 20%;">
                    Uang Diterima
                </th>

            </tr>

        </thead>


        <tbody>

            @forelse($payments as $index => $payment)

                <tr>

                    {{-- No --}}

                    <td class="text-center">

                        {{ $index + 1 }}

                    </td>


                    {{-- Pelanggan --}}

                    <td>

                        {{ $payment->booking?->user?->name
                            ?? $payment->booking?->nama_pelanggan
                            ?? '-' }}

                    </td>


                    {{-- Layanan --}}

                    <td>

                        {{ $payment->booking?->services?->pluck('nama_layanan')->implode(', ')
                            ?: '-' }}

                    </td>


                    {{-- Tanggal --}}

                    <td>

                        {{ ($payment->verified_at ?? $payment->updated_at)
                            ? \Carbon\Carbon::parse($payment->verified_at ?? $payment->updated_at)->format('d/m/Y')
                            : '-' }}

                    </td>

                    {{-- Metode --}}

                    <td>

                        {{ $payment->metode === 'tunai' ? 'Tunai / Lunas' : 'Transfer / DP' }}

                    </td>


                    {{-- Status --}}

                    <td class="status">

                        @if($payment->metode === 'tunai')

                            Tunai Terverifikasi

                        @elseif($payment->booking?->status === 'dibayar')

                            DP Terverifikasi

                        @elseif($payment->booking?->status === 'selesai')

                            Selesai

                        @else

                            {{ ucfirst(
                                str_replace(
                                    '_',
                                    ' ',
                                    $payment->booking?->status ?? 'terverifikasi'
                                )
                            ) }}

                        @endif

                    </td>


                    {{-- Harga --}}

                    <td class="text-right">

                        Rp
                        {{ number_format(
                            $payment->jumlah,
                            0,
                            ',',
                            '.'
                        ) }}

                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="7"
                        class="text-center"
                    >

                        Tidak ada data pendapatan
                        pada periode tersebut.

                    </td>

                </tr>

            @endforelse


            {{-- TOTAL --}}

            @if($payments->count() > 0)

                <tr class="total-row">

                    <td
                        colspan="6"
                        class="text-right"
                    >

                        TOTAL UANG DITERIMA

                    </td>

                    <td class="text-right">

                        Rp
                        {{ number_format(
                            $totalUangDiterima,
                            0,
                            ',',
                            '.'
                        ) }}

                    </td>

                </tr>

            @endif

        </tbody>

    </table>

    <div class="period">
        <strong>Catatan:</strong> Pastikan semua layanan telah diselesaikan di sistem. Pendapatan penuh dihitung setelah layanan selesai.
    </div>


    {{-- ================================================= --}}
    {{-- FOOTER --}}
    {{-- ================================================= --}}

    <div class="footer">

        Dicetak pada:

        {{ now()->format('d/m/Y H:i') }}

    </div>


    {{-- ================================================= --}}
    {{-- TANDA TANGAN --}}
    {{-- ================================================= --}}

    <div class="signature">

        <div>

            Batam,
            {{ now()->format('d/m/Y') }}

        </div>

        <div class="signature-space"></div>

        <strong>
            Admin Alan's Art Hair Salon
        </strong>

    </div>

</body>

</html>
