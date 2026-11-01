<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Struk {{ $sale->txtInvoiceNumber }} - {{ $sale->merchant->txtMerchantName }}</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Courier New', Courier, monospace; }
    body { background: #e2e8f0; padding: 20px; display: flex; justify-content: center; }
    .receipt {
      width: 300px;
      background: #fff;
      padding: 16px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      font-size: 12px;
      line-height: 1.4;
      color: #000;
    }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .bold { font-weight: bold; }
    .divider { border-bottom: 1px dashed #000; margin: 8px 0; }
    .row { display: flex; justify-content: space-between; }
    .no-print { margin-bottom: 12px; text-align: center; }
    .btn-print {
      background: #008542;
      color: #fff;
      border: none;
      padding: 8px 16px;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
      font-size: 13px;
    }
    @media print {
      body { background: none; padding: 0; }
      .receipt { box-shadow: none; width: 100%; }
      .no-print { display: none; }
    }
  </style>
</head>
<body>

  <div>
    <div class="no-print">
      <button class="btn-print" onclick="window.print()">🖨️ Cetak Struk (Print)</button>
    </div>

    <div class="receipt">
      <div class="text-center">
        <p class="bold" style="font-size: 13px;">HOYA-HOYA DAY FESTIVAL</p>
        <p style="font-size: 10px;">PT KALBE MORINAGA INDONESIA</p>
        <div class="divider"></div>
        <p class="bold" style="font-size: 14px;">{{ $sale->merchant->txtMerchantName }}</p>
        <p>{{ $sale->merchant->txtBoothNumber }}</p>
        <p style="font-size: 10px;">HP: {{ $sale->merchant->txtPhone }}</p>
      </div>

      <div class="divider"></div>

      <div class="row">
        <span>No:</span>
        <span class="bold">{{ $sale->txtInvoiceNumber }}</span>
      </div>
      <div class="row">
        <span>Waktu:</span>
        <span>{{ $sale->dtmInserted->format('d/m/Y H:i') }}</span>
      </div>
      <div class="row">
        <span>Kasir:</span>
        <span>{{ $sale->user->txtUsername ?? 'Kasir' }}</span>
      </div>

      @if($sale->employee)
        <div class="divider"></div>
        <div class="row">
          <span>Pelanggan:</span>
          <span class="bold">{{ $sale->employee->txtEmployeeName }}</span>
        </div>
        <div class="row">
          <span>NIK Karyawan:</span>
          <span>{{ $sale->employee->txtEmployeeNik }}</span>
        </div>
      @endif

      <div class="divider"></div>

      <!-- Items -->
      <table style="width: 100%; font-size: 12px;">
        @foreach($sale->details as $item)
          <tr>
            <td colspan="2" class="bold">{{ $item->txtProductName }}</td>
          </tr>
          <tr>
            <td style="padding-left: 10px;">{{ $item->intQuantity }} x {{ number_format($item->floatPrice, 0, ',', '.') }}</td>
            <td class="text-right bold">{{ number_format($item->floatSubtotal, 0, ',', '.') }}</td>
          </tr>
        @endforeach
      </table>

      <div class="divider"></div>

      <div class="row bold">
        <span>TOTAL BELANJA:</span>
        <span>Rp {{ number_format($sale->floatTotalAmount, 0, ',', '.') }}</span>
      </div>

      <div class="divider"></div>

      <div class="row">
        <span>Dibayar Voucher:</span>
        <span class="bold">Rp {{ number_format($sale->floatVoucherAmount, 0, ',', '.') }}</span>
      </div>
      <div class="row">
        <span>Dibayar Tunai:</span>
        <span>Rp {{ number_format($sale->floatCashAmount, 0, ',', '.') }}</span>
      </div>
      <div class="row">
        <span>Kembalian:</span>
        <span>Rp {{ number_format($sale->floatChangeAmount, 0, ',', '.') }}</span>
      </div>

      @if($sale->voucher)
        <div class="divider"></div>
        <div class="row bold" style="font-size: 11px;">
          <span>SISA SALDO VOUCHER:</span>
          <span>Rp {{ number_format($sale->voucher->floatCurrentBalance, 0, ',', '.') }}</span>
        </div>
      @endif

      <div class="divider"></div>

      <div class="text-center" style="margin-top: 10px; font-size: 10px;">
        <p>Terima kasih telah berbelanja di</p>
        <p class="bold">Hoya-Hoya Day Festival 2026</p>
        <p>Mari Dukung UMKM Binaan KMI!</p>
      </div>
    </div>
  </div>

</body>
</html>

