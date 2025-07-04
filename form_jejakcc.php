<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Follow Up Konsumen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #f72585;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }
        
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
            padding: 30px;
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 30px;
            color: var(--dark-color);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 20px;
        }
        
        .form-header h2 {
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--dark-color);
        }
        
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        
        .form-control, .form-select {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ced4da;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        
        .btn-submit {
            background-color: var(--primary-color);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
            width: 100%;
        }
        
        .btn-submit:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .search-results {
            position: absolute;
            z-index: 1000;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            background: white;
            border: 1px solid #ced4da;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: none;
        }
        
        .search-result-item {
            padding: 10px 15px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .search-result-item:hover {
            background-color: #f8f9fa;
        }
        
        .search-result-item .customer-code {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        .search-result-item .customer-phone {
            font-size: 0.8rem;
            color: #28a745;
            margin-top: 3px;
        }
        
        .search-input-container {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            right: 10px;
            top: 10px;
            color: #6c757d;
        }
        
        @media (max-width: 768px) {
            .form-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="form-header">
            <h2><i class="fas fa-phone-alt me-2"></i>Form Follow Up Konsumen</h2>
            <p>Lengkapi data follow up konsumen di bawah ini</p>
        </div>
        
        <form action="proses_followup.php" method="post">
            <!-- Customer Search Field -->
            <div class="mb-4">
                <label for="search-calonkonsumen" class="form-label required-field">Calon Konsumen</label>
                <div class="search-input-container">
                    <input type="text" class="form-control" id="search-calonkonsumen" name="calon_konsumen_display" 
                           onkeyup="searchCalonKonsumen()" required placeholder="Ketik nama atau kode konsumen...">
                    <i class="fas fa-search input-icon"></i>
                    <div id="calon-konsumen-list" class="search-results"></div>
                </div>
                <input type="hidden" name="calon_konsumen" id="calon_konsumen_id">
                <div class="form-text">Mulai ketik untuk mencari konsumen</div>
            </div>
            
            <div class="row">
                <!-- Follow Up Date -->
                <div class="col-md-6 mb-4">
                    <label for="tgl_follow_up" class="form-label required-field">Tanggal Follow Up</label>
                    <input type="date" class="form-control" id="tgl_follow_up" name="tgl_follow_up" required>
                </div>
                
                <!-- Follow Up Method -->
                <div class="col-md-6 mb-4">
                    <label for="melalui" class="form-label required-field">Melalui</label>
                    <select class="form-select" id="melalui" name="melalui" required>
                        <option value="" selected disabled>Pilih metode</option>
                        <option value="SMS">SMS</option>
                        <option value="Telp">Telepon</option>
                        <option value="WhatsApp">WhatsApp</option>
                        <option value="EMail">Email</option>
                        <option value="Tatap Muka">Tatap Muka</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
            </div>
            
            <!-- Phone Number -->
            <div class="mb-4">
                <label for="telp" class="form-label">Nomor Telepon</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                    <input type="text" class="form-control" id="telp" name="telp" placeholder="Nomor yang dihubungi">
                </div>
                <div class="form-text">Nomor akan terisi otomatis saat memilih konsumen</div>
            </div>
            
            <!-- Description -->
            <div class="mb-4">
                <label for="keterangan" class="form-label required-field">Keterangan</label>
                <textarea class="form-control" id="keterangan" name="keterangan" required 
                          placeholder="Deskripsi follow up..."></textarea>
            </div>
            
            <!-- Result -->
            <div class="mb-4">
                <label for="hasil" class="form-label">Hasil Follow Up</label>
                <textarea class="form-control" id="hasil" name="hasil" 
                          placeholder="Hasil dari follow up..."></textarea>
            </div>
            
            <!-- Progress Status -->
            <div class="mb-4">
                <label for="status_progres" class="form-label required-field">Status Progres Konsumen</label>
                <select class="form-select" id="status_progres" name="status_progres" required>
                    <option value="" selected disabled>Pilih status progres</option>
                    <option value="Call In">Call In</option>
                    <option value="Survey">Survey</option>
                    <option value="Reserve">Reserve</option>
                    <option value="DP">DP</option>
                    <option value="Pemberkasan">Pemberkasan</option>
                    <option value="Wawancara">Wawancara</option>
                    <option value="Analisa">Analisa</option>
                    <option value="Sp3k">Sp3k</option>
                    <option value="Reject">Reject</option>
                    <option value="Akad kredit">Akad kredit</option>
                    <option value="Pencairan Akad">Pencairan Akad</option>
                    <option value="Cek Fisik Bangunan">Cek Fisik Bangunan</option>
                    <option value="BAST">BAST</option>
                    <option value="Reques Bangun">Reques Bangun</option>
                    <option value="Pencairan Topping Off">Pencairan Topping Off</option>
                    <option value="Pencairan Legalitas">Pencairan Legalitas</option>
                    <option value="KOMPLAIN">KOMPLAIN</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-submit mt-3">
                <i class="fas fa-save me-2"></i>Simpan Follow Up
            </button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function searchCalonKonsumen() {
        var query = $("#search-calonkonsumen").val();
        var resultsContainer = $("#calon-konsumen-list");

        if (query.length > 2) {
            $.ajax({
                url: "search_calon_konsumen.php",
                method: "GET",
                data: { query: query },
                success: function(response) {
                    if (response.trim() !== '') {
                        resultsContainer.html(response).show();
                    } else {
                        resultsContainer.html('<div class="search-result-item">Tidak ditemukan</div>').show();
                    }
                }
            });
        } else {
            resultsContainer.hide();
        }
    }

    // Hide results when clicking elsewhere
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#search-calonkonsumen, #calon-konsumen-list').length) {
            $('#calon-konsumen-list').hide();
        }
    });

    // When a customer item is clicked
    $(document).on("click", ".calon-konsumen-item", function() {
        var selectedId = $(this).data("id");
        var selectedCode = $(this).data("kode");
        var selectedName = $(this).data("nama");
        var selectedPhone = $(this).data("telp"); // Get phone number from data attribute

        var displayText = selectedName + " (" + selectedCode + ")";

        // Set values to form fields
        $("#search-calonkonsumen").val(displayText);
        $("#calon_konsumen_id").val(selectedId);
        $("#telp").val(selectedPhone); // Auto-fill phone number
        
        $("#calon-konsumen-list").hide();
        
        // Auto-focus next field
        $("#tgl_follow_up").focus();
    });

    // Format phone number input
    $('#telp').on('input', function() {
        this.value = this.value.replace(/[^0-9+]/g, '');
    });
    </script>
</body>
</html>