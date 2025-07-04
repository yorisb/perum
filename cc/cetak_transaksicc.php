<?php
// Koneksi database
$conn = new mysqli("srv143.niagahoster.com", "n1572337_developer", "bad_cookies8080", "n1572337_developer");

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil ID transaksi dari parameter URL
$id_transaksi = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Query untuk mendapatkan data transaksi
$query = $conn->prepare("
    SELECT t.*, k.nama_lengkap, k.no_hp, k.alamat_lengkap, k.npwp, k.kartu_id, k.no_kartu_id,
           u.nama_unit, u.type, u.luas_bangunan, u.harga as harga_unit, u.bonus
    FROM transaksi t
    LEFT JOIN calon_konsumen k ON t.id_konsumen = k.id
    LEFT JOIN unit_properti u ON t.nama_unit = u.nama_unit
    WHERE t.id = ?
");
$query->bind_param("i", $id_transaksi);
$query->execute();
$result = $query->get_result();
$transaksi = $result->fetch_assoc();

// Jika transaksi tidak ditemukan
if (!$transaksi) {
    die("Transaksi tidak ditemukan");
}

// Format tanggal
function formatDate($date) {
    return $date ? date('d/m/Y', strtotime($date)) : '-';
}

// Format mata uang
function formatCurrency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

// Hitung status pembayaran
function getPaymentStatus($transaksi) {
    $total_paid = $transaksi['uang_muka'];
    $total_due = $transaksi['total_setelah_penambahan'];
    
    if ($total_paid >= $total_due) {
        return 'LUNAS';
    } elseif ($total_paid > 0) {
        return 'DP';
    } else {
        return 'BELUM BAYAR';
    }
}

$payment_status = getPaymentStatus($transaksi);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cetak Transaksi <?= htmlspecialchars($transaksi['no_transaksi']) ?></title>
  <style>
    @page {
      size: A4;
      margin: 15mm;
    }
    
    body {
      font-family: 'Arial', sans-serif;
      line-height: 1.5;
      color: #000;
      background-color: #fff;
      padding: 0;
      margin: 0;
    }
    
    .container {
      width: 100%;
      max-width: 100%;
      padding: 0;
    }
    
    .header {
      text-align: center;
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 2px solid #333;
    }
    
    .header h1 {
      font-size: 18pt;
      margin: 0;
      color: #333;
    }
    
    .header p {
      margin: 5px 0 0;
      font-size: 11pt;
    }
    
    .info-box {
      display: flex;
      justify-content: space-between;
      margin-bottom: 20px;
    }
    
    .info-section {
      width: 48%;
    }
    
    .section-title {
      font-size: 12pt;
      font-weight: bold;
      margin: 15px 0 10px;
      padding-bottom: 3px;
      border-bottom: 1px solid #ddd;
    }
    
    .detail-row {
      display: flex;
      margin-bottom: 5px;
    }
    
    .detail-label {
      width: 150px;
      font-weight: bold;
    }
    
    .detail-value {
      flex: 1;
    }
    
    .currency {
      font-family: 'Courier New', monospace;
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
      margin: 10px 0 20px;
    }
    
    th, td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: left;
    }
    
    th {
      background-color: #f2f2f2;
      font-weight: bold;
    }
    
    .total-row {
      font-weight: bold;
    }
    
    .footer {
      margin-top: 30px;
      padding-top: 10px;
      border-top: 1px solid #333;
      display: flex;
      justify-content: space-between;
    }
    
    .signature {
      width: 300px;
      text-align: center;
    }
    
    .signature-line {
      border-top: 1px solid #000;
      margin-top: 50px;
      padding-top: 5px;
    }
    
    .status-badge {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 3px;
      font-weight: bold;
      font-size: 10pt;
    }
    
    .status-lunas {
      background-color: #d4edda;
      color: #155724;
    }
    
    .status-dp {
      background-color: #fff3cd;
      color: #856404;
    }
    
    .status-belum {
      background-color: #f8d7da;
      color: #721c24;
    }
    
    .no-print {
      display: none;
    }
    
    @media screen {
      body {
        background-color: #f5f5f5;
        padding: 20px;
      }
      
      .container {
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        padding: 20px;
        max-width: 800px;
        margin: 0 auto;
      }
      
      .no-print {
        display: block;
        text-align: center;
        margin-bottom: 20px;
      }
    }
  </style>
</head>
<body>
<div class="container">
  <!-- Tombol hanya muncul saat preview di browser -->
  <div class="no-print">
    <button onclick="window.print()" class="no-print" style="padding: 8px 15px; background: #4361ee; color: white; border: none; border-radius: 4px; cursor: pointer;">
      <i class="fas fa-print"></i> Cetak Dokumen
    </button>
    <a href="detail_transaksi.php?id=<?= $id_transaksi ?>" class="no-print" style="padding: 8px 15px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; margin-left: 10px;">
      <i class="fas fa-arrow-left"></i> Kembali ke Detail
    </a>
  </div>

  <!-- Kop Surat -->
  <div class="header">
    <h1>SURAT PERJANJIAN JUAL BELI (SPR)</h1>
    <p>No: <?= htmlspecialchars($transaksi['no_transaksi']) ?></p>
  </div>

  <!-- Info Dasar Transaksi -->
  <div class="info-box">
    <div class="info-section">
      <div class="detail-row">
        <div class="detail-label">Tanggal Transaksi</div>
        <div class="detail-value">: <?= formatDate($transaksi['tgl_transaksi']) ?></div>
      </div>
      <div class="detail-row">
        <div class="detail-label">Status</div>
        <div class="detail-value">: <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $payment_status)) ?>"><?= $payment_status ?></span></div>
      </div>
    </div>
    <div class="info-section">
      <div class="detail-row">
        <div class="detail-label">Kode Konsumen</div>
        <div class="detail-value">: <?= htmlspecialchars($transaksi['kode_konsumen']) ?></div>
      </div>
    </div>
  </div>

  <!-- Informasi Konsumen -->
  <div class="section-title">I. DATA PEMBELI</div>
  <div class="detail-row">
    <div class="detail-label">Nama Lengkap</div>
    <div class="detail-value">: <?= htmlspecialchars($transaksi['nama_lengkap']) ?></div>
  </div>
  <div class="detail-row">
    <div class="detail-label">No. HP</div>
    <div class="detail-value">: <?= htmlspecialchars($transaksi['no_hp']) ?></div>
  </div>
  <div class="detail-row">
    <div class="detail-label">Alamat</div>
    <div class="detail-value">: <?= htmlspecialchars($transaksi['alamat_lengkap']) ?></div>
  </div>
  <div class="detail-row">
    <div class="detail-label">NPWP</div>
    <div class="detail-value">: <?= $transaksi['npwp'] ? htmlspecialchars($transaksi['npwp']) : '-' ?></div>
  </div>
  <div class="detail-row">
    <div class="detail-label">Kartu Identitas</div>
    <div class="detail-value">: <?= htmlspecialchars($transaksi['kartu_id']) ?> (<?= htmlspecialchars($transaksi['no_kartu_id']) ?>)</div>
  </div>

  <!-- Informasi Properti -->
  <div class="section-title">II. DATA UNIT</div>
  <div class="detail-row">
    <div class="detail-label">Nama Unit/Kapling</div>
    <div class="detail-value">: <?= htmlspecialchars($transaksi['nama_unit']) ?></div>
  </div>
  <div class="detail-row">
    <div class="detail-label">Type</div>
    <div class="detail-value">: <?= htmlspecialchars($transaksi['type']) ?></div>
  </div>
  <div class="detail-row">
    <div class="detail-label">Luas Bangunan</div>
    <div class="detail-value">: <?= $transaksi['luas_bangunan'] ?> m²</div>
  </div>
  <div class="detail-row">
    <div class="detail-label">Bonus/Keterangan</div>
    <div class="detail-value">: <?= $transaksi['bonus'] ? htmlspecialchars($transaksi['bonus']) : '-' ?></div>
  </div>

  <!-- Rincian Harga -->
  <div class="section-title">III. RINCIAN HARGA</div>
  <table>
    <tr>
      <th width="60%">Keterangan</th>
      <th width="40%">Jumlah</th>
    </tr>
    <tr>
      <td>Harga Unit</td>
      <td class="currency"><?= formatCurrency($transaksi['harga_unit']) ?></td>
    </tr>
    <tr>
      <td>Harga Kesepakatan</td>
      <td class="currency"><?= formatCurrency($transaksi['harga_kesepakatan']) ?></td>
    </tr>
    <?php if ($transaksi['luas_tanah'] > 0): ?>
    <tr>
      <td>Penambahan Luas Tanah (<?= $transaksi['luas_tanah'] ?> m² × <?= formatCurrency($transaksi['harga_per_m2']) ?>)</td>
      <td class="currency"><?= formatCurrency($transaksi['total_harga_penambahan']) ?></td>
    </tr>
    <?php endif; ?>
    <?php if ($transaksi['penambahan_lain']): ?>
    <tr>
      <td>Penambahan Lain (<?= htmlspecialchars($transaksi['penambahan_lain']) ?>)</td>
      <td class="currency"><?= formatCurrency($transaksi['total_harga_penambahan_lain']) ?></td>
    </tr>
    <?php endif; ?>
    <tr class="total-row">
      <td>TOTAL HARGA</td>
      <td class="currency"><?= formatCurrency($transaksi['total_setelah_penambahan']) ?></td>
    </tr>
  </table>

  <!-- Rencana Pembayaran -->
  <div class="section-title">IV. RENCANA PEMBAYARAN</div>
  <table>
    <tr>
      <th width="60%">Keterangan</th>
      <th width="40%">Jumlah</th>
    </tr>
    <tr>
      <td>Tanda Jadi (<?= $transaksi['status_tanda_jadi'] == 'masuk' ? 'Masuk harga jual' : 'Tidak masuk harga jual' ?>)</td>
      <td class="currency"><?= formatCurrency($transaksi['tanda_jadi']) ?></td>
    </tr>
    <tr>
      <td>Uang Muka (<?= $transaksi['periode_uang_muka'] ?>x angsuran)</td>
      <td class="currency"><?= formatCurrency($transaksi['uang_muka']) ?></td>
    </tr>
    <tr class="total-row">
      <td>TOTAL AKHIR</td>
      <td class="currency"><?= formatCurrency($transaksi['total_akhir']) ?></td>
    </tr>
    <?php if ($transaksi['periode_cicilan']): ?>
    <tr>
      <td>Cicilan (<?= $transaksi['periode_cicilan'] ?> bulan)</td>
      <td class="currency"><?= formatCurrency($transaksi['cicilan']) ?>/bulan</td>
    </tr>
    <?php endif; ?>
  </table>

  <!-- Jadwal Pembayaran -->
  <div class="section-title">V. JADWAL PEMBAYARAN</div>
  <table>
    <tr>
      <th width="40%">Jenis Pembayaran</th>
      <th width="30%">Jumlah</th>
      <th width="30%">Jatuh Tempo</th>
    </tr>
    <tr>
      <td>Tanda Jadi</td>
      <td class="currency"><?= formatCurrency($transaksi['tanda_jadi']) ?></td>
      <td><?= formatDate($transaksi['rencana_tgl_bayar_tanda_jadi']) ?></td>
    </tr>
    <tr>
      <td>Uang Muka</td>
      <td class="currency"><?= formatCurrency($transaksi['uang_muka']) ?></td>
      <td><?= formatDate($transaksi['rencana_tgl_bayar_uang_muka']) ?></td>
    </tr>
    <?php if ($transaksi['periode_cicilan']): ?>
    <tr>
      <td>Cicilan</td>
      <td class="currency"><?= formatCurrency($transaksi['cicilan']) ?>/bulan</td>
      <td>Mulai <?= formatDate($transaksi['rencana_tgl_bayar_pembayaran']) ?></td>
    </tr>
    <?php endif; ?>
  </table>

  <!-- Angsuran Uang Muka -->
  <div class="section-title">VI. RINCIAN ANGSURAN UANG MUKA</div>
  <table>
    <tr>
      <th width="10%">No</th>
      <th width="30%">Jumlah</th>
      <th width="30%">Jatuh Tempo</th>
      <th width="30%">Status</th>
    </tr>
    <?php for ($i = 1; $i <= $transaksi['periode_uang_muka']; $i++): 
      $jumlah = $transaksi['angsuran_'.$i] ?? 0;
    ?>
    <tr>
      <td><?= $i ?></td>
      <td class="currency"><?= formatCurrency($jumlah) ?></td>
      <td><?= formatDate(date('Y-m-d', strtotime($transaksi['tgl_transaksi'] . " + $i month"))) ?></td>
      <td>BELUM LUNAS</td>
    </tr>
    <?php endfor; ?>
    <tr class="total-row">
      <td colspan="2">TOTAL UANG MUKA</td>
      <td class="currency" colspan="2"><?= formatCurrency($transaksi['uang_muka']) ?></td>
    </tr>
  </table>

  <!-- Footer dan Tanda Tangan -->
  <div class="footer">
    <div class="signature">
      <p>Hormat kami,</p>
      <div class="signature-line"></div>
      <p>(__________________________)</p>
      <p>Sales Executive</p>
    </div>
    <div class="signature">
      <p>Menyetujui,</p>
      <div class="signature-line"></div>
      <p>(__________________________)</p>
      <p>Pembeli</p>
    </div>
  </div>

  <p style="text-align: center; margin-top: 30px; font-size: 10pt;">
    Dokumen ini dicetak secara elektronik, tanda tangan asli diperlukan untuk versi fisik
  </p>
</div>

<script>
// Auto print saat halaman selesai loading (opsional)
window.onload = function() {
  // Jika ingin auto print, hapus komentar di bawah ini
  // window.print();
};
</script>
</body>
</html>