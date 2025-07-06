<?php
session_start();
include '../routes/config.php';

// Cek login
if (!isset($_SESSION['role'])) {
    header("Location: ../login.php");
    exit;
}

// Ambil ID
if (!isset($_GET['id'])) {
    die("ID tidak ditemukan.");
}
$id = (int)$_GET['id'];

// Ambil data pemasukan dengan join
$query = "
SELECT 
    p.*,
    u.nama_unit,
    ap.akun,
    b.bank, b.no_rek
FROM pemasukan p
LEFT JOIN unit_properti u ON p.unit_id = u.id
LEFT JOIN akun_pemasukan ap ON p.akun_id = ap.id_akun_pemasukan
LEFT JOIN akun_bank b ON p.bank_id = b.id_bank
WHERE p.id = $id
";

$result = $conn->query($query);
if ($result->num_rows != 1) {
    die("Data tidak ditemukan.");
}

$data = $result->fetch_assoc();

// Fungsi untuk format tanggal Indonesia
function formatTanggalIndonesia($tanggal) {
    if (empty($tanggal)) return '-';
    
    $bulan = array(
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    );
    
    $tanggal = strtotime($tanggal);
    $tgl = date('d', $tanggal);
    $bln = $bulan[(int)date('m', $tanggal)];
    $thn = date('Y', $tanggal);
    
    return "$tgl $bln $thn";
}

// Informasi perusahaan (sesuaikan dengan data perusahaan Anda)
$infoPerusahaan = [
    'nama' => 'PT. Developer Property Maju',
    'alamat' => 'Jl. Perintis Kemerdekaan No. 123, Tasikmalaya',
    'telepon' => '(0265) 1234567',
    'nama_penerima' => 'Budi Santoso',
    'jabatan_penerima' => 'Manajer Keuangan',
    'nama_penyetuju' => 'Dewi Lestari',
    'jabatan_penyetuju' => 'Direktur Akuntansi'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Pemasukan - <?= htmlspecialchars($data['no_tanda_terima'] ?? '') ?> | <?= htmlspecialchars($infoPerusahaan['nama']) ?></title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #f5f5f5; 
            margin: 0; 
            padding: 0;
            color: #333;
        }
        .container-nota {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 25px;
            text-align: center;
            position: relative;
        }
        .logo-perusahaan {
            max-height: 60px;
            margin-bottom: 15px;
        }
        .nama-perusahaan {
            font-size: 24px;
            font-weight: 700;
            margin: 5px 0;
        }
        .alamat-perusahaan {
            font-size: 12px;
            opacity: 0.8;
        }
        .judul-nota {
            font-size: 18px;
            font-weight: 600;
            margin-top: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .nomor-nota {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255,255,255,0.2);
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 14px;
        }
        .konten {
            padding: 30px;
            position: relative;
        }
        .tabel-detail {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .tabel-detail th {
            background-color: #f8f9fa;
            text-align: left;
            padding: 12px 15px;
            font-weight: 600;
            width: 30%;
        }
        .tabel-detail td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        .tabel-detail tr:last-child td {
            border-bottom: none;
        }
        .baris-jumlah td {
            font-weight: bold;
            font-size: 18px;
            color: #27ae60;
        }
        .bagian-bukti {
            margin-top: 30px;
            border-top: 1px dashed #ddd;
            padding-top: 20px;
        }
        .judul-bukti {
            font-weight: 600;
            margin-bottom: 10px;
        }
        .gambar-bukti {
            max-width: 100%;
            max-height: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .bagian-ttd {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .kotak-ttd {
            text-align: center;
            width: 45%;
        }
        .garis-ttd {
            border-top: 1px solid #333;
            width: 80%;
            margin: 40px auto 10px;
            padding-top: 10px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #7f8c8d;
            padding: 15px;
            border-top: 1px solid #eee;
        }
        .watermark {
            position: absolute;
            opacity: 0.1;
            font-size: 120px;
            font-weight: bold;
            transform: rotate(-30deg);
            z-index: 0;
            top: 30%;
            left: 10%;
            color: #2c3e50;
        }
        @media print {
            body {
                background: none;
            }
            .container-nota {
                box-shadow: none;
                margin: 0;
                width: 100%;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container-nota">
        <div class="header">
            <!-- Ganti dengan logo perusahaan Anda -->
            <!-- <img src="../assets/logo.png" alt="Logo Perusahaan" class="logo-perusahaan"> -->
            <div class="nama-perusahaan"><?= htmlspecialchars($infoPerusahaan['nama']) ?></div>
            <div class="alamat-perusahaan"><?= htmlspecialchars($infoPerusahaan['alamat']) ?></div>
            <div class="judul-nota">BUKTI PENERIMAAN KAS</div>
            <div class="nomor-nota">No. <?= htmlspecialchars($data['no_tanda_terima'] ?? 'N/A') ?></div>
        </div>
        
        <div class="konten">
            <div class="watermark"><?= htmlspecialchars($infoPerusahaan['nama']) ?></div>
            
            <table class="tabel-detail">
                <tr>
                    <th>Tanggal</th>
                    <td><?= formatTanggalIndonesia($data['tanggal'] ?? date('Y-m-d')) ?></td>
                </tr>
                <tr>
                    <th>Sumber</th>
                    <td><?= htmlspecialchars($data['asal'] ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <th>Unit Properti</th>
                    <td><?= htmlspecialchars($data['nama_unit'] ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <th>Akun</th>
                    <td><?= htmlspecialchars($data['akun'] ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <th>Detail Bank</th>
                    <td>
                        <?= htmlspecialchars($data['bank'] ?? 'N/A') ?>
                        <?php if (!empty($data['no_rek'])): ?>
                            (No. Rek: <?= htmlspecialchars($data['no_rek']) ?>)
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Keterangan</th>
                    <td><?= htmlspecialchars($data['keterangan'] ?? 'N/A') ?></td>
                </tr>
                <tr class="baris-jumlah">
                    <th>Jumlah Diterima</th>
                    <td>Rp <?= isset($data['jumlah']) ? number_format($data['jumlah'], 0, ',', '.') : '0' ?></td>
                </tr>
            </table>

            <?php if (!empty($data['file_upload']) && file_exists($data['file_upload'])): ?>
                <div class="bagian-bukti">
                    <div class="judul-bukti">Bukti Pembayaran:</div>
                    <img src="<?= htmlspecialchars($data['file_upload']) ?>" alt="Bukti Pembayaran" class="gambar-bukti">
                </div>
            <?php endif; ?>

            <div class="bagian-ttd">
                <div class="kotak-ttd">
                    <div>Penerima,</div>
                    <div class="garis-ttd"></div>
                    <div><?= htmlspecialchars($infoPerusahaan['nama_penerima']) ?></div>
                    <div><?= htmlspecialchars($infoPerusahaan['jabatan_penerima']) ?></div>
                </div>
                <div class="kotak-ttd">
                    <div>Menyetujui,</div>
                    <div class="garis-ttd"></div>
                    <div><?= htmlspecialchars($infoPerusahaan['nama_penyetuju']) ?></div>
                    <div><?= htmlspecialchars($infoPerusahaan['jabatan_penyetuju']) ?></div>
                </div>
            </div>
        </div>
        
        <div class="footer">
            Ini adalah bukti penerimaan resmi dari <?= htmlspecialchars($infoPerusahaan['nama']) ?>. 
            Harap simpan dokumen ini sebagai arsip. 
            Untuk pertanyaan lebih lanjut, hubungi departemen akuntansi kami di <?= htmlspecialchars($infoPerusahaan['telepon']) ?>.
        </div>
    </div>
    
    <div class="no-print" style="text-align: center; margin: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #2c3e50; color: white; border: none; border-radius: 4px; cursor: pointer;">Cetak Nota</button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #e74c3c; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">Tutup Jendela</button>
    </div>
</body>
</html>