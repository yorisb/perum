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
    SELECT t.*, k.nama_lengkap, k.no_hp, k.alamat_lengkap, u.nama_unit, u.type, u.luas_bangunan, u.harga as harga_unit, u.bonus
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
    // Logika sederhana - sesuaikan dengan kebutuhan Anda
    $total_paid = $transaksi['uang_muka']; // Ini harus dihitung dari record pembayaran sebenarnya
    $total_due = $transaksi['total_setelah_penambahan'];
    
    if ($total_paid >= $total_due) {
        return [
            'text' => 'LUNAS',
            'class' => 'success',
            'icon' => 'check-circle'
        ];
    } elseif ($total_paid > 0) {
        return [
            'text' => 'DP',
            'class' => 'warning',
            'icon' => 'hourglass-half'
        ];
    } else {
        return [
            'text' => 'BELUM BAYAR',
            'class' => 'danger',
            'icon' => 'exclamation-circle'
        ];
    }
}

$payment_status = getPaymentStatus($transaksi);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Transaksi <?= htmlspecialchars($transaksi['no_transaksi']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #4361ee;
      --primary-light: #e6e9ff;
      --secondary: #3f37c9;
      --success: #4cc9f0;
      --danger: #f72585;
      --warning: #f8961e;
      --light: #f8f9fa;
      --dark: #212529;
      --gray: #6c757d;
    }
    
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f5f7fa;
      color: var(--dark);
    }
    
    .card {
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      border: none;
      margin-bottom: 20px;
    }
    
    .card-header {
      background-color: white;
      border-bottom: 1px solid rgba(0, 0, 0, 0.08);
      padding: 20px;
      border-radius: 10px 10px 0 0 !important;
    }
    
    .card-title {
      color: var(--primary);
      font-weight: 600;
      margin: 0;
    }
    
    .badge {
      font-weight: 500;
      padding: 8px 12px;
      border-radius: 20px;
      font-size: 0.9rem;
    }
    
    .badge-success {
      background-color: rgba(76, 201, 240, 0.1);
      color: #4cc9f0;
    }
    
    .badge-warning {
      background-color: rgba(248, 150, 30, 0.1);
      color: #f8961e;
    }
    
    .badge-danger {
      background-color: rgba(247, 37, 133, 0.1);
      color: #f72585;
    }
    
    .detail-label {
      font-weight: 500;
      color: var(--gray);
      margin-bottom: 5px;
    }
    
    .detail-value {
      font-weight: 500;
      margin-bottom: 15px;
    }
    
    .currency {
      font-family: 'Courier New', monospace;
      font-weight: 500;
    }
    
    .status-indicator {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      display: inline-block;
      margin-right: 6px;
    }
    
    .status-success {
      background-color: var(--success);
    }
    
    .status-warning {
      background-color: var(--warning);
    }
    
    .status-danger {
      background-color: var(--danger);
    }
    
    .section-title {
      color: var(--primary);
      font-weight: 600;
      margin-bottom: 15px;
      padding-bottom: 8px;
      border-bottom: 1px solid var(--primary-light);
    }
    
    .btn-print {
      background-color: white;
      border: 1px solid var(--primary);
      color: var(--primary);
    }
    
    .btn-print:hover {
      background-color: var(--primary-light);
    }
    
    .payment-plan {
      border-left: 3px solid var(--primary);
      padding-left: 15px;
      margin-bottom: 20px;
    }
    
    .payment-item {
      margin-bottom: 10px;
    }
    
    @media print {
      .no-print {
        display: none;
      }
      
      body {
        background-color: white;
        font-size: 12pt;
      }
      
      .card {
        box-shadow: none;
        border: 1px solid #ddd;
      }
    }
  </style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <h2 class="mb-0">Detail Transaksi</h2>
    <div>
      <a href="daftar_transaksi.php" class="btn btn-outline-secondary me-2">
        <i class="fas fa-arrow-left"></i> Kembali
      </a>
      <button onclick="window.print()" class="btn btn-print">
        <i class="fas fa-print"></i> Cetak
      </button>
    </div>
  </div>

  <!-- Header Transaksi -->
  <div class="card mb-4">
    <div class="card-header">
      <div class="d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Transaksi #<?= htmlspecialchars($transaksi['no_transaksi']) ?></h5>
        <span class="badge badge-<?= $payment_status['class'] ?>">
          <i class="fas fa-<?= $payment_status['icon'] ?>"></i> <?= $payment_status['text'] ?>
        </span>
      </div>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <div class="detail-label">Tanggal Transaksi</div>
          <div class="detail-value"><?= formatDate($transaksi['tgl_transaksi']) ?></div>
          
          <div class="detail-label">Konsumen</div>
          <div class="detail-value">
            <?= htmlspecialchars($transaksi['nama_lengkap']) ?><br>
            <small class="text-muted"><?= htmlspecialchars($transaksi['no_hp']) ?></small>
          </div>
        </div>
        <div class="col-md-6">
          <div class="detail-label">Unit/Kapling</div>
          <div class="detail-value">
            <?= htmlspecialchars($transaksi['nama_unit']) ?> (<?= htmlspecialchars($transaksi['type']) ?>)<br>
            <small class="text-muted">Luas Bangunan: <?= $transaksi['luas_bangunan'] ?> m²</small>
          </div>
          
          <div class="detail-label">Kode Konsumen</div>
          <div class="detail-value"><?= htmlspecialchars($transaksi['kode_konsumen']) ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- Informasi Harga -->
    <div class="col-lg-8">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title mb-0">Rincian Harga</h5>
        </div>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <div class="section-title">Harga Unit</div>
              <div class="payment-plan">
                <div class="payment-item">
                  <div class="detail-label">Harga Unit</div>
                  <div class="detail-value currency"><?= formatCurrency($transaksi['harga_unit']) ?></div>
                </div>
                
                <div class="payment-item">
                  <div class="detail-label">Bonus/Keterangan</div>
                  <div class="detail-value"><?= $transaksi['bonus'] ? htmlspecialchars($transaksi['bonus']) : '-' ?></div>
                </div>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="section-title">Penambahan</div>
              <div class="payment-plan">
                <div class="payment-item">
                  <div class="detail-label">Harga Kesepakatan</div>
                  <div class="detail-value currency"><?= formatCurrency($transaksi['harga_kesepakatan']) ?></div>
                </div>
                
                <div class="payment-item">
                  <div class="detail-label">Luas Tanah</div>
                  <div class="detail-value"><?= $transaksi['luas_tanah'] ?> m² × <?= formatCurrency($transaksi['harga_per_m2']) ?> = <?= formatCurrency($transaksi['total_harga_penambahan']) ?></div>
                </div>
                
                <?php if ($transaksi['penambahan_lain']): ?>
                <div class="payment-item">
                  <div class="detail-label">Penambahan Lain</div>
                  <div class="detail-value"><?= htmlspecialchars($transaksi['penambahan_lain']) ?> (<?= formatCurrency($transaksi['total_harga_penambahan_lain']) ?>)</div>
                </div>
                <?php endif; ?>
                
                <div class="payment-item">
                  <div class="detail-label">Total Penambahan</div>
                  <div class="detail-value currency"><?= formatCurrency($transaksi['total_penambahan']) ?></div>
                </div>
                
                <div class="payment-item">
                  <div class="detail-label">Total Setelah Penambahan</div>
                  <div class="detail-value currency"><strong><?= formatCurrency($transaksi['total_setelah_penambahan']) ?></strong></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Rencana Pembayaran -->
    <div class="col-lg-4">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title mb-0">Rencana Pembayaran</h5>
        </div>
        <div class="card-body">
          <div class="payment-plan">
            <div class="payment-item">
              <div class="detail-label">Tanda Jadi</div>
              <div class="detail-value currency"><?= formatCurrency($transaksi['tanda_jadi']) ?> 
                <small>(<?= $transaksi['status_tanda_jadi'] == 'masuk' ? 'Masuk harga jual' : 'Tidak masuk harga jual' ?>)</small>
              </div>
            </div>
            
            <div class="payment-item">
              <div class="detail-label">Uang Muka</div>
              <div class="detail-value currency"><?= formatCurrency($transaksi['uang_muka']) ?></div>
            </div>
            
            <div class="payment-item">
              <div class="detail-label">Total Akhir</div>
              <div class="detail-value currency"><strong><?= formatCurrency($transaksi['total_akhir']) ?></strong></div>
            </div>
            
            <div class="payment-item">
              <div class="detail-label">Cara Pembayaran</div>
              <div class="detail-value"><?= $transaksi['cara_pembayaran'] ? htmlspecialchars($transaksi['cara_pembayaran']) : '-' ?></div>
            </div>
            
            <?php if ($transaksi['periode_cicilan']): ?>
            <div class="payment-item">
              <div class="detail-label">Cicilan</div>
              <div class="detail-value">
                <?= formatCurrency($transaksi['cicilan']) ?> / bulan (<?= $transaksi['periode_cicilan'] ?> bulan)
              </div>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      
      <!-- Jadwal Pembayaran -->
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">Jadwal Pembayaran</h5>
        </div>
        <div class="card-body">
          <div class="payment-plan">
            <div class="payment-item">
              <div class="detail-label">Tanda Jadi</div>
              <div class="detail-value"><?= formatDate($transaksi['rencana_tgl_bayar_tanda_jadi']) ?></div>
            </div>
            
            <div class="payment-item">
              <div class="detail-label">Uang Muka</div>
              <div class="detail-value"><?= formatDate($transaksi['rencana_tgl_bayar_uang_muka']) ?></div>
            </div>
            
            <div class="payment-item">
              <div class="detail-label">Pembayaran</div>
              <div class="detail-value"><?= formatDate($transaksi['rencana_tgl_bayar_pembayaran']) ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Angsuran Uang Muka -->
  <div class="card">
    <div class="card-header">
      <h5 class="card-title mb-0">Rincian Angsuran Uang Muka</h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead class="table-light">
            <tr>
              <th>No</th>
              <th>Jumlah</th>
              <th>Tanggal Jatuh Tempo</th>
              <th>Status</th>
              <th>Tanggal Pembayaran</th>
            </tr>
          </thead>
          <tbody>
            <?php
            // Contoh data angsuran - sesuaikan dengan database Anda
            $angsuran = [];
            for ($i = 1; $i <= $transaksi['periode_uang_muka']; $i++) {
              $angsuran[] = [
                'no' => $i,
                'jumlah' => $transaksi['angsuran_'.$i] ?? 0,
                'jatuh_tempo' => date('Y-m-d', strtotime($transaksi['tgl_transaksi'] . " + $i month")),
                'status' => 'Belum Lunas',
                'tgl_bayar' => null
              ];
            }
            
            foreach ($angsuran as $item): 
            ?>
            <tr>
              <td><?= $item['no'] ?></td>
              <td class="currency"><?= formatCurrency($item['jumlah']) ?></td>
              <td><?= formatDate($item['jatuh_tempo']) ?></td>
              <td>
                <span class="badge badge-danger">
                  <i class="fas fa-exclamation-circle"></i> <?= $item['status'] ?>
                </span>
              </td>
              <td><?= $item['tgl_bayar'] ? formatDate($item['tgl_bayar']) : '-' ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="table-light">
              <td colspan="1"><strong>Total</strong></td>
              <td class="currency"><strong><?= formatCurrency($transaksi['uang_muka']) ?></strong></td>
              <td colspan="3"></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Fungsi untuk mencetak
function printDocument() {
  window.print();
}
</script>
</body>
</html>