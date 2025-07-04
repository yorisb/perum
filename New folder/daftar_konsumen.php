<?php
$conn = new mysqli("srv143.niagahoster.com", "n1572337_developer", "bad_cookies8080", "n1572337_developer");

$result = $conn->query("SELECT kode, nama_lengkap, kartu_id, alamat_lengkap, no_hp, email FROM calon_konsumen");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --success-color: #2ecc71;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .header {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px 0;
            margin-bottom: 30px;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }
        
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            background-color: var(--secondary-color);
            color: white;
            border-bottom: none;
            font-weight: 500;
        }
        
        .table tbody tr {
            transition: background-color 0.2s;
        }
        
        .table tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
        }
        
        .btn-action {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-detail {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-edit {
            background-color: var(--warning-color);
            color: white;
        }
        
        .btn-hapus {
            background-color: var(--danger-color);
            color: white;
        }
        
        .btn-add {
            background-color: var(--success-color);
            color: white;
            padding: 8px 15px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .badge-id {
            background-color: #e3f2fd;
            color: var(--primary-color);
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 5px;
        }
        
        .search-container {
            max-width: 300px;
        }
        
        @media (max-width: 768px) {
            .table-responsive {
                border-radius: 0;
            }
            
            .header {
                padding: 15px 0;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0" style="color: var(--secondary-color);">
                    <i class="bi bi-people-fill me-2"></i>Customer Management
                </h2>
                <a href="input_calon_konsumen.php" class="btn btn-add">
                    <i class="bi bi-plus-lg"></i> Add Customer
                </a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title mb-0">Customer List</h5>
                    <div class="search-container">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" placeholder="Search...">
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>ID Type</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><span class="badge-id"><?= htmlspecialchars($row['kode']) ?></span></td>
                                    <td>
                                        <div class="fw-medium"><?= htmlspecialchars($row['nama_lengkap']) ?></div>
                                        <div class="text-muted small"><?= htmlspecialchars(substr($row['alamat_lengkap'], 0, 30)) ?>...</div>
                                    </td>
                                    <td><?= htmlspecialchars($row['kartu_id']) ?></td>
                                    <td><?= htmlspecialchars($row['no_hp']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="detail_konsumen.php?kode=<?= urlencode($row['kode']) ?>" class="btn-action btn-detail" title="Detail">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                            <a href="edit_konsumen.php?kode=<?= urlencode($row['kode']) ?>" class="btn-action btn-edit" title="Edit">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                            <a href="hapus_konsumen.php?kode=<?= urlencode($row['kode']) ?>" class="btn-action btn-hapus" title="Delete" onclick="return confirm('Are you sure you want to delete this customer?')">
                                                <i class="bi bi-trash-fill"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple search functionality
        document.querySelector('input[type="text"]').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    </script>
</body>
</html>