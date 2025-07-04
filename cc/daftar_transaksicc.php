<?php
$conn = new mysqli("srv143.niagahoster.com", "n1572337_developer", "bad_cookies8080", "n1572337_developer");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Transaksi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
      padding: 20px;
    }
    
    .card {
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      border: none;
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
    
    .table {
      margin-bottom: 0;
    }
    
    .table thead th {
      border-bottom-width: 1px;
      font-weight: 600;
      color: var(--dark);
      background-color: #f8f9fa;
      white-space: nowrap;
    }
    
    .table-hover tbody tr:hover {
      background-color: rgba(67, 97, 238, 0.05);
    }
    
    .badge {
      font-weight: 500;
      padding: 6px 10px;
      border-radius: 20px;
      font-size: 0.8rem;
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
    
    .btn-sm {
      padding: 5px 12px;
      font-size: 0.8rem;
      border-radius: 6px;
    }
    
    .btn-primary {
      background-color: var(--primary);
      border-color: var(--primary);
    }
    
    .btn-primary:hover {
      background-color: var(--secondary);
      border-color: var(--secondary);
    }
    
    .btn-icon {
      width: 32px;
      height: 32px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0;
      border-radius: 50%;
    }
    
    .currency {
      font-family: 'Courier New', monospace;
      font-weight: 500;
      white-space: nowrap;
    }
    
    .dataTables_length select {
      border-radius: 6px;
      padding: 5px 10px;
      border: 1px solid #dee2e6;
    }
    
    .dataTables_filter input {
      border-radius: 6px;
      padding: 5px 10px;
      border: 1px solid #dee2e6;
      margin-left: 10px;
    }
    
    .pagination .page-item.active .page-link {
      background-color: var(--primary);
      border-color: var(--primary);
    }
    
    .pagination .page-link {
      color: var(--primary);
    }
    
    .status-indicator {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      display: inline-block;
      margin-right: 6px;
    }
    
    .status-lunas {
      background-color: var(--success);
    }
    
    .status-dp {
      background-color: var(--warning);
    }
    
    .status-belum {
      background-color: var(--danger);
    }
    
    .action-buttons {
      white-space: nowrap;
    }
  </style>
</head>
<body>

<div class="container-fluid">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="card-title">Daftar Transaksi</h5>
      <div>
        <a href="transaksi.php" class="btn btn-primary">
          <i class="fas fa-plus"></i> Tambah Transaksi
        </a>
        <button class="btn btn-outline-primary ms-2" onclick="window.print()">
          <i class="fas fa-print"></i> Cetak
        </button>
      </div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table id="transaksiTable" class="table table-hover" style="width:100%">
          <thead>
            <tr>
              <th>No. SPR</th>
              <th>Nama Konsumen</th>
              <th>Kapling/Unit</th>
              <th>Type</th>
              <th>Harga Total</th>
              <th>Uang Muka</th>
              <th>Cicilan</th>
              <th>Cara Bayar</th>
              <th>Tgl Transaksi</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $query = $conn->query("
              SELECT t.*, k.nama_lengkap
              FROM transaksi t
              LEFT JOIN calon_konsumen k ON t.id_konsumen = k.id
              ORDER BY t.tgl_transaksi DESC
            ");
            
            while ($row = $query->fetch_assoc()) {
              // Calculate payment status based on your business logic
              $payment_status = calculatePaymentStatus($row);
              $status_class = $payment_status['class'];
              $status_text = $payment_status['text'];
              $status_icon = $payment_status['icon'];
              
              // Format dates
              $tgl_transaksi = date('d/m/Y', strtotime($row['tgl_transaksi']));
              $tgl_tanda_jadi = $row['rencana_tgl_bayar_tanda_jadi'] ? date('d/m/Y', strtotime($row['rencana_tgl_bayar_tanda_jadi'])) : '-';
              $tgl_uang_muka = $row['rencana_tgl_bayar_uang_muka'] ? date('d/m/Y', strtotime($row['rencana_tgl_bayar_uang_muka'])) : '-';
              
              // Format currency values
              $harga_total = 'Rp ' . number_format($row['total_setelah_penambahan'], 0, ',', '.');
              $uang_muka = 'Rp ' . number_format($row['uang_muka'], 0, ',', '.');
              $cicilan = $row['cicilan'] ? 'Rp ' . number_format($row['cicilan'], 0, ',', '.') : '-';
              
              echo "
              <tr>
                <td>{$row['no_transaksi']}</td>
                <td>{$row['nama_lengkap']}</td>
                <td>{$row['nama_unit']}</td>
                <td>{$row['type']}</td>
                <td class='currency'>{$harga_total}</td>
                <td class='currency'>{$uang_muka}</td>
                <td class='currency'>{$cicilan}</td>
                <td>{$row['cara_pembayaran']}</td>
                <td>{$tgl_transaksi}</td>
                <td>
                  <span class='status-indicator {$status_class}'></span>
                  <span class='badge {$status_class}'>
                    <i class='{$status_icon}'></i> {$status_text}
                  </span>
                </td>
                <td class='action-buttons'>
                  <div class='d-flex gap-2'>
                    <a href='detail_transaksi.php?id={$row['id']}' class='btn btn-sm btn-primary btn-icon' title='Detail'>
                      <i class='fas fa-eye'></i>
                    </a>
                    <a href='cetak_transaksi.php?id={$row['id']}' target='_blank' class='btn btn-sm btn-outline-secondary btn-icon' title='Cetak'>
                      <i class='fas fa-print'></i>
                    </a>
                  </div>
                </td>
              </tr>
              ";
            }
            
            // Function to determine payment status (customize according to your business logic)
            function calculatePaymentStatus($transaction) {
              // Example logic - adjust according to your requirements
              $total_paid = $transaction['uang_muka']; // This should be calculated from payment records
              $total_due = $transaction['total_setelah_penambahan'];
              
              if ($total_paid >= $total_due) {
                return [
                  'class' => 'badge-success',
                  'text' => 'LUNAS',
                  'icon' => 'fas fa-check-circle'
                ];
              } elseif ($total_paid > 0) {
                return [
                  'class' => 'badge-warning',
                  'text' => 'DP',
                  'icon' => 'fas fa-hourglass-half'
                ];
              } else {
                return [
                  'class' => 'badge-danger',
                  'text' => 'BELUM',
                  'icon' => 'fas fa-exclamation-circle'
                ];
              }
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
  $('#transaksiTable').DataTable({
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
    },
    order: [[8, 'desc']], // Default sort by transaction date
    responsive: true,
    dom: '<"top"<"row"<"col-md-6"l><"col-md-6"f>>><"row"<"col-md-12"tr>><"bottom"<"row"<"col-md-6"i><"col-md-6"p>>>',
    pageLength: 25,
    buttons: [
      {
        extend: 'excel',
        text: '<i class="fas fa-file-excel"></i> Excel',
        className: 'btn btn-success btn-sm'
      },
      {
        extend: 'pdf',
        text: '<i class="fas fa-file-pdf"></i> PDF',
        className: 'btn btn-danger btn-sm'
      },
      {
        extend: 'print',
        text: '<i class="fas fa-print"></i> Print',
        className: 'btn btn-secondary btn-sm'
      }
    ],
    initComplete: function() {
      this.api().buttons().container().appendTo('#transaksiTable_wrapper .col-md-6:eq(0)');
    }
  });
});
</script>

</body>
</html>