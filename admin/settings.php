<?php 
$active_page = 'settings';
require_once 'includes/admin_header.php';

// Ambil pengaturan saat ini
try {
    $stmt = $db->query("SELECT * FROM settings");
    $settings = [];
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Gagal mengambil data pengaturan";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $db->beginTransaction();
        
        // Update Informasi GOR
        $stmt = $db->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
        $settingsToUpdate = [
            'gor_name' => $_POST['gor_name'],
            'gor_address' => $_POST['gor_address'],
            'gor_phone' => $_POST['gor_phone'],
            'gor_email' => $_POST['gor_email'],
            'gor_description' => $_POST['gor_description'],
            'maps_embed' => $_POST['maps_embed'],
            
            // Jadwal Operasional
            'opening_hours_weekday' => $_POST['opening_hours_weekday'],
            'closing_hours_weekday' => $_POST['closing_hours_weekday'],
            'opening_hours_weekend' => $_POST['opening_hours_weekend'],
            'closing_hours_weekend' => $_POST['closing_hours_weekend'],
            
            // Harga Sewa
            'price_regular' => $_POST['price_regular'],
            'price_weekend' => $_POST['price_weekend'],
            'price_member' => $_POST['price_member'],
            
            // Pengaturan Sistem
            'booking_time_limit' => $_POST['booking_time_limit'],
            'max_booking_per_day' => $_POST['max_booking_per_day'],
            'cancellation_policy' => $_POST['cancellation_policy']
        ];
        
        foreach ($settingsToUpdate as $key => $value) {
            $stmt->execute([$value, $key]);
        }
        
        $db->commit();
        $_SESSION['success'] = "Pengaturan berhasil disimpan";
        
        // Refresh settings
        $stmt = $db->query("SELECT * FROM settings");
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        
    } catch (PDOException $e) {
        $db->rollBack();
        $_SESSION['error'] = "Gagal menyimpan pengaturan";
    }
}
?>

<div class="dashboard-container">
    <?php require_once 'includes/admin_sidebar.php'; ?>

    <main class="main-content">
        <header class="content-header">
            <h2>Pengaturan Sistem</h2>
        </header>

        <div class="content-body">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible">
                    <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible">
                    <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST" class="form settings-form">
                        <!-- Tab Navigation -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#info">
                                    Informasi GOR
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#schedule">
                                    Jadwal Operasional
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#pricing">
                                    Harga Sewa
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#system">
                                    Pengaturan Sistem
                                </a>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content mt-lg">
                            <!-- Informasi GOR -->
                            <div class="tab-pane fade show active" id="info">
                                <div class="form-group">
                                    <label for="gor_name">Nama GOR</label>
                                    <input type="text" id="gor_name" name="gor_name" class="form-control"
                                           value="<?php echo htmlspecialchars($settings['gor_name'] ?? ''); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="gor_address">Alamat</label>
                                    <textarea id="gor_address" name="gor_address" class="form-control" rows="3" required>
                                        <?php echo htmlspecialchars($settings['gor_address'] ?? ''); ?>
                                    </textarea>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="gor_phone">No. Telepon</label>
                                        <input type="tel" id="gor_phone" name="gor_phone" class="form-control"
                                               value="<?php echo htmlspecialchars($settings['gor_phone'] ?? ''); ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="gor_email">Email</label>
                                        <input type="email" id="gor_email" name="gor_email" class="form-control"
                                               value="<?php echo htmlspecialchars($settings['gor_email'] ?? ''); ?>" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="gor_description">Deskripsi</label>
                                    <textarea id="gor_description" name="gor_description" class="form-control" rows="4">
                                        <?php echo htmlspecialchars($settings['gor_description'] ?? ''); ?>
                                    </textarea>
                                </div>

                                <div class="form-group">
                                    <label for="maps_embed">Google Maps Embed Code</label>
                                    <textarea id="maps_embed" name="maps_embed" class="form-control" rows="3">
                                        <?php echo htmlspecialchars($settings['maps_embed'] ?? ''); ?>
                                    </textarea>
                                    <small class="form-text text-muted">
                                        Paste kode embed dari Google Maps di sini
                                    </small>
                                </div>
                            </div>

                            <!-- Jadwal Operasional -->
                            <div class="tab-pane fade" id="schedule">
                                <div class="form-section">
                                    <h4>Jam Operasional Weekday</h4>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="opening_hours_weekday">Jam Buka</label>
                                            <input type="time" id="opening_hours_weekday" name="opening_hours_weekday" 
                                                   class="form-control"
                                                   value="<?php echo htmlspecialchars($settings['opening_hours_weekday'] ?? ''); ?>" 
                                                   required>
                                        </div>

                                        <div class="form-group">
                                            <label for="closing_hours_weekday">Jam Tutup</label>
                                            <input type="time" id="closing_hours_weekday" name="closing_hours_weekday" 
                                                   class="form-control"
                                                   value="<?php echo htmlspecialchars($settings['closing_hours_weekday'] ?? ''); ?>" 
                                                   required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <h4>Jam Operasional Weekend</h4>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="opening_hours_weekend">Jam Buka</label>
                                            <input type="time" id="opening_hours_weekend" name="opening_hours_weekend" 
                                                   class="form-control"
                                                   value="<?php echo htmlspecialchars($settings['opening_hours_weekend'] ?? ''); ?>" 
                                                   required>
                                        </div>

                                        <div class="form-group">
                                            <label for="closing_hours_weekend">Jam Tutup</label>
                                            <input type="time" id="closing_hours_weekend" name="closing_hours_weekend" 
                                                   class="form-control"
                                                   value="<?php echo htmlspecialchars($settings['closing_hours_weekend'] ?? ''); ?>" 
                                                   required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Harga Sewa -->
                            <div class="tab-pane fade" id="pricing">
                                <div class="form-group">
                                    <label for="price_regular">Harga Regular (per jam)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="number" id="price_regular" name="price_regular" class="form-control"
                                               value="<?php echo htmlspecialchars($settings['price_regular'] ?? ''); ?>" 
                                               required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="price_weekend">Harga Weekend (per jam)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="number" id="price_weekend" name="price_weekend" class="form-control"
                                               value="<?php echo htmlspecialchars($settings['price_weekend'] ?? ''); ?>" 
                                               required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="price_member">Harga Member (per jam)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="number" id="price_member" name="price_member" class="form-control"
                                               value="<?php echo htmlspecialchars($settings['price_member'] ?? ''); ?>" 
                                               required>
                                    </div>
                                </div>
                            </div>

                            <!-- Pengaturan Sistem -->
                            <div class="tab-pane fade" id="system">
                                <div class="form-group">
                                    <label for="booking_time_limit">Batas Waktu Pembayaran (jam)</label>
                                    <input type="number" id="booking_time_limit" name="booking_time_limit" 
                                           class="form-control"
                                           value="<?php echo htmlspecialchars($settings['booking_time_limit'] ?? ''); ?>" 
                                           required>
                                    <small class="form-text text-muted">
                                        Berapa jam sebelum jadwal booking harus sudah dibayar
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="max_booking_per_day">Maksimal Booking per Hari</label>
                                    <input type="number" id="max_booking_per_day" name="max_booking_per_day" 
                                           class="form-control"
                                           value="<?php echo htmlspecialchars($settings['max_booking_per_day'] ?? ''); ?>" 
                                           required>
                                </div>

                                <div class="form-group">
                                    <label for="cancellation_policy">Kebijakan Pembatalan</label>
                                    <textarea id="cancellation_policy" name="cancellation_policy" 
                                              class="form-control" rows="4">
                                        <?php echo htmlspecialchars($settings['cancellation_policy'] ?? ''); ?>
                                    </textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once 'includes/admin_footer.php'; ?> 