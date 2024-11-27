<?php
// Koneksi database
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GOR Pandu Cendikia - Lapangan Badminton Premium</title>
    <link rel="stylesheet" href="css/main.css">
    <!-- Font Awesome untuk icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Preload gambar background untuk performa lebih baik -->
    <link rel="preload" as="image" href="https://images.pexels.com/photos/1325738/pexels-photo-1325738.jpeg">
    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    
    <!-- Custom calendar style -->
    <style>
        .fc-event {
            cursor: pointer;
        }
        .fc-day:hover {
            background: rgba(0,0,0,0.05);
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container flex justify-between items-center">
            <a class="navbar-brand" href="#">GOR PANDU CENDIKIA</a>
            <div class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </div>
            <div class="nav-links" id="navLinks">
                <a href="#beranda">Beranda</a>
                <a href="#fasilitas">Fasilitas</a>
                <a href="#harga">Harga</a>
                <a href="#jadwal">Jadwal</a>
                <a href="#informasi">Informasi</a>
                <a href="booking.php" class="btn-booking">Booking</a>
                <a href="login.php" class="btn btn-login">Login</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="beranda" class="hero">
        <div class="container">
            <h1>Lapangan Badminton Premium</h1>
            <p>Fasilitas Standar Internasional untuk Pengalaman Bermain Terbaik</p>
            <div class="btn-container">
                <a href="booking.php" class="btn btn-primary">Booking Sekarang</a>
                <a href="#jadwal" class="btn btn-secondary">Cek Ketersediaan</a>
            </div>
        </div>
    </section>

    <!-- Fasilitas Section -->
    <section id="fasilitas" class="facilities">
        <div class="container">
            <h2>Fasilitas Unggulan</h2>
            <div class="card card-primary">
                <div class="grid grid-cols-2 gap-xl md:grid-cols-1">
                    <div class="card-image">
                        <img src="assets//images/court.jpg" alt="Lapangan Badminton">
                    </div>
                    <div class="card-body flex flex-col justify-center">
                        <h3>Spesifikasi Lapangan:</h3>
                        <ul class="card-list">
                            <li>Lantai Vinyl Kualitas Premium</li>
                            <li>Pencahayaan LED 300 Lux</li>
                            <li>Ruangan Full AC</li>
                            <li>Ukuran Standar Internasional</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="card-grid">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-parking"></i>
                        <h3>Area Parkir</h3>
                        <p>Parkir Luas dan Aman</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-restroom"></i>
                        <h3>Toilet & Ruang Ganti</h3>
                        <p>Bersih dan Nyaman</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-mosque"></i>
                        <h3>Musholla</h3>
                        <p>Tersedia Tempat Ibadah</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-store"></i>
                        <h3>Kantin</h3>
                        <p>Minuman & Snack</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Harga Section -->
    <section id="harga" class="pricing">
        <div class="container">
            <h2>Harga Sewa Lapangan</h2>
            <div class="card-grid">
                <div class="card">
                    <div class="card-header">
                        <h3>Regular</h3>
                        <div class="price">
                            <span class="amount">Rp 100.000</span>
                            <span class="duration">/ 2 jam</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="card-list">
                            <li>Booking fleksibel</li>
                            <li>Bisa reschedule (H-1)</li>
                            <li>Free pinjam raket</li>
                            <li>Free air mineral</li>
                        </ul>
                        <div class="card-actions">
                            <a href="booking.php?type=regular" class="btn-primary">Booking Sekarang</a>
                        </div>
                    </div>
                </div>

                <div class="card card-featured">
                    <div class="card-header">
                        <h3>Member</h3>
                        <div class="price">
                            <span class="amount">Rp 80.000</span>
                            <span class="duration">/ 2 jam</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="card-list">
                            <li>Semua fitur Regular</li>
                            <li>Prioritas booking</li>
                            <li>Diskon 20%</li>
                            <li>Free shuttle cock</li>
                            <li>Free minuman isotonic</li>
                        </ul>
                        <div class="card-actions">
                            <a href="booking.php?type=member" class="btn-primary">Booking Sekarang</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Jadwal Section -->
    <section id="jadwal" class="schedule">
        <div class="container">
            <div class="section-header">
                <h2>Jadwal Lapangan</h2>
                <p>Klik pada tanggal untuk melihat ketersediaan dan melakukan booking</p>
            </div>

            <div class="schedule-container">
                <div id="calendar"></div>

                <!-- Modal Detail Jadwal -->
                <div class="modal" id="scheduleModal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4>Jadwal: <span id="selectedDate"></span></h4>
                                <button type="button" class="close" onclick="closeModal()">&times;</button>
                            </div>
                            <div class="modal-body">
                                <div class="time-slots">
                                    <!-- Time slots akan di-render disini -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Informasi Section -->
    <section id="informasi" class="info">
        <div class="container">
            <h2>Informasi</h2>
            <div class="card-grid">
                <div class="card">
                    <div class="card-body">
                        <div class="card-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3>Jam Operasional</h3>
                        <ul class="card-list">
                            <li>Senin - Minggu</li>
                            <li>08:00 - 22:00 WIB</li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="card-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h3>Kontak</h3>
                        <ul class="card-list">
                            <li>Telp: 082386825808</li>
                            <li>Email: info@gorpandu.com</li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="card-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h3>Lokasi</h3>
                        <p>Ujong, Jalan Imam Bonjol, Seuneubok, Kabupaten Aceh Barat</p>
                    </div>
                </div>
            </div>

            <div class="card mt-xl">
                <div class="card-body">
                    <div class="map-container">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3979.2305525779657!2d96.10762177484!3d4.175027695798783!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x303ec3e39d1bd509%3A0xfbb592a4512045b0!2sGOR%20PANDU%20CENDIKIA!5e0!3m2!1sid!2sid!4v1732177471126!5m2!1sid!2sid" 
                            width="100%" 
                            height="450" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>

            <div class="card mt-xl">
                <div class="card-header">
                    <h3>Peraturan Penggunaan Lapangan</h3>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-2 gap-xl md:grid-cols-1">
                        <div>
                            <h4>Peraturan Umum</h4>
                            <ul class="card-list">
                                <li>Dilarang merokok di area GOR</li>
                                <li>Jaga kebersihan lapangan dan area GOR</li>
                                <li>Tidak diperkenankan membawa makanan ke area lapangan</li>
                            </ul>
                        </div>
                        <div>
                            <h4>Pembayaran & Pembatalan</h4>
                            <ul class="card-list">
                                <li>Pembayaran dilakukan maksimal H-1</li>
                                <li>Pembatalan dengan refund 100% maksimal H-2</li>
                                <li>Pembatalan H-1 dikenakan biaya 50%</li>
                                <li>Tidak ada refund untuk pembatalan di hari H</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="grid grid-cols-3 gap-xl md:grid-cols-1">
                <div class="footer-brand">
                    <h3>GOR PANDU CENDIKIA</h3>
                    <p>Lapangan Badminton Premium di Meulaboh</p>
                </div>
                <div class="footer-links">
                    <h4>Quick Links</h4>
                    <a href="#beranda">Beranda</a>
                    <a href="#fasilitas">Fasilitas</a>
                    <a href="#harga">Harga</a>
                    <a href="#jadwal">Jadwal</a>
                </div>
                <div class="footer-contact">
                    <h4>Kontak</h4>
                    <p><i class="fas fa-phone"></i> 082386825808</p>
                    <p><i class="fas fa-envelope"></i> info@gorpandu.com</p>
                    <p><i class="fas fa-map-marker-alt"></i> Ujong, Jalan Imam Bonjol, Seuneubok, Kabupaten Aceh Barat, Aceh 23611</p>
                </div>
            </div>
            <div class="footer-bottom text-center">
                <p>&copy; 2024 GOR Pandu Cendikia. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Tambahkan di bagian bawah body -->
    <script>
        const menuToggle = document.getElementById('menuToggle');
        const navLinks = document.getElementById('navLinks');

        menuToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });

        // Smooth scroll dengan offset untuk fixed navbar
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                
                // Cek apakah link menuju ke halaman lain (seperti booking.php)
                if (!href.startsWith('#')) {
                    return; // Biarkan link external berfungsi normal
                }
                
                e.preventDefault();
                if(href === '#') return;
                
                const targetElement = document.querySelector(href);
                if (!targetElement) return; // Jika element tidak ditemukan
                
                const navbarHeight = document.querySelector('.navbar').offsetHeight;
                const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset;
                
                window.scrollTo({
                    top: targetPosition - navbarHeight,
                    behavior: 'smooth'
                });

                // Menutup menu mobile jika terbuka
                navLinks.classList.remove('active');
            });
        });

        // Menutup menu saat scroll
        window.addEventListener('scroll', () => {
            navLinks.classList.remove('active');
        });

        // Highlight active section
        window.addEventListener('scroll', () => {
            const sections = document.querySelectorAll('section');
            const navItems = document.querySelectorAll('.nav-links a[href^="#"]');
            
            let current = '';
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if(pageYOffset >= sectionTop - 150) {
                    current = section.getAttribute('id');
                }
            });

            navItems.forEach(item => {
                item.classList.remove('active');
                if(item.getAttribute('href') === '#' + current) {
                    item.classList.add('active');
                }
            });
        });
    </script>

    <!-- FullCalendar Scripts -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'id',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            dateClick: function(info) {
                showSchedule(info.dateStr);
            },
            events: 'api/get_bookings.php'
        });
        calendar.render();
    });

    // Fungsi untuk menampilkan modal jadwal
    function showSchedule(date) {
        const modal = document.getElementById('scheduleModal');
        const selectedDate = document.getElementById('selectedDate');
        
        // Format tanggal
        const formattedDate = new Date(date).toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        selectedDate.textContent = formattedDate;
        modal.style.display = 'block';
        
        // Load jadwal untuk lapangan 1 (default)
        loadTimeSlots(date, 1);
    }

    // Fungsi untuk menutup modal
    function closeModal() {
        document.getElementById('scheduleModal').style.display = 'none';
    }

    // Fungsi untuk memuat time slots
    function loadTimeSlots(date, court) {
        const timeSlotsDiv = document.querySelector('.time-slots');
        timeSlotsDiv.innerHTML = '<div class="loading">Memuat jadwal...</div>';
        
        fetch(`api/get_time_slots.php?date=${date}&court=${court}`)
            .then(response => response.json())
            .then(data => {
                let html = '<div class="time-slots-grid">';
                data.forEach(slot => {
                    html += `
                        <div class="time-slot ${slot.status}">
                            <span class="time">${slot.time}</span>
                            ${slot.status === 'available' 
                                ? `<a href="booking.php?date=${date}&court=${court}&time=${slot.time}" 
                                     class="btn btn-sm btn-primary">Booking</a>`
                                : '<span class="badge badge-danger">Terisi</span>'
                            }
                        </div>
                    `;
                });
                html += '</div>';
                timeSlotsDiv.innerHTML = html;
            })
            .catch(error => {
                timeSlotsDiv.innerHTML = '<div class="error">Gagal memuat jadwal</div>';
            });
    }

    // Event listener untuk tab lapangan
    document.querySelectorAll('.court-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            document.querySelectorAll('.court-tab').forEach(t => t.classList.remove('active'));
            // Add active class to clicked tab
            this.classList.add('active');
            // Get selected date
            const date = document.getElementById('selectedDate').dataset.date;
            // Load time slots for selected court
            loadTimeSlots(date, this.dataset.court);
        });
    });

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('scheduleModal');
        if (event.target == modal) {
            closeModal();
        }
    }
    </script>
</body>
</html> 