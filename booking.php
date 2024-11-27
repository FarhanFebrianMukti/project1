<?php
require_once 'config/database.php';

$booking_type = isset($_GET['type']) ? $_GET['type'] : 'regular';
$selected_date = isset($_GET['date']) ? $_GET['date'] : '';
$selected_time = isset($_GET['time']) ? $_GET['time'] : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Lapangan - GOR Pandu Cendikia</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <a class="navbar-brand" href="index.php">GOR PANDU CENDIKIA</a>
        </div>
    </nav>

    <!-- Booking Section -->
    <section class="booking-section">
        <div class="container">
            <h2>Form Booking Lapangan</h2>
            
            <!-- Progress Steps -->
            <div class="booking-progress">
                <div class="progress-step active">
                    <span class="step-number">1</span>
                    <span class="step-text">Pilih Jadwal</span>
                </div>
                <div class="progress-step">
                    <span class="step-number">2</span>
                    <span class="step-text">Isi Data</span>
                </div>
                <div class="progress-step">
                    <span class="step-number">3</span>
                    <span class="step-text">Konfirmasi</span>
                </div>
            </div>

            <!-- Booking Form -->
            <form action="process_booking.php" method="POST" class="booking-form">
                <!-- Tipe Booking -->
                <div class="form-group">
                    <label>Tipe Booking</label>
                    <select name="booking_type" required>
                        <option value="regular" <?php echo $booking_type == 'regular' ? 'selected' : ''; ?>>Regular (2 Jam)</option>
                        <option value="member" <?php echo $booking_type == 'member' ? 'selected' : ''; ?>>Paket Langganan (1 Bulan)</option>
                    </select>
                </div>

                <!-- Tanggal dan Waktu -->
                <div class="form-group">
                    <label>Tanggal Main</label>
                    <input type="date" name="play_date" 
                           value="<?php echo $selected_date; ?>" 
                           min="<?php echo date('Y-m-d'); ?>" 
                           required>
                </div>

                <div class="form-group">
                    <label>Jam Main</label>
                    <select name="play_time" required>
                        <option value="">Pilih Jam</option>
                        <?php
                        $time_slots = [
                            '08:00-10:00',
                            '10:00-12:00',
                            '13:00-15:00',
                            '15:00-17:00',
                            '20:00-22:00'
                        ];
                        foreach ($time_slots as $slot) {
                            $selected = ($selected_time == $slot) ? 'selected' : '';
                            echo "<option value='$slot' $selected>$slot</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Data Pemesan -->
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" required>
                </div>

                <div class="form-group">
                    <label>Nomor WhatsApp</label>
                    <input type="tel" name="phone" required>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="btn-submit">Lanjutkan ke Pembayaran</button>
            </form>

            <!-- Informasi Tambahan -->
            <div class="booking-info">
                <h4>Catatan Penting:</h4>
                <ul>
                    <li>Pembayaran harus dilakukan maksimal H-1 sebelum jadwal main</li>
                    <li>Pembatalan dengan refund 100% maksimal H-2</li>
                    <li>Reschedule dapat dilakukan maksimal 1x</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 GOR Pandu Cendikia. All rights reserved.</p>
        </div>
    </footer>
</body>
</html> 