<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Digital Mahasiswa</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .dashboard {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        
        .card h2 {
            color: #2575fc;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .jadwal-hari-ini, .tugas-list {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .jadwal-item, .tugas-item {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            background-color: #f9f9f9;
            border-left: 4px solid #6a11cb;
        }
        
        .tugas-item.mendekati-deadline {
            border-left-color: #ff4d4d;
            background-color: #fff0f0;
        }
        
        .tugas-item.selesai {
            border-left-color: #4caf50;
            background-color: #f0fff4;
            text-decoration: line-through;
            opacity: 0.7;
        }
        
        .forms-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        button {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            color: #856404;
        }
        
        .summary {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
        }
        
        .summary-item {
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            flex: 1;
            margin: 0 10px;
        }
        
        .summary-item h3 {
            font-size: 2rem;
            color: #2575fc;
            margin-bottom: 5px;
        }
        
        @media (max-width: 768px) {
            .dashboard, .forms-container {
                grid-template-columns: 1fr;
            }
            
            .summary {
                flex-direction: column;
            }
            
            .summary-item {
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Agenda Digital Mahasiswa</h1>
            <p>Kelola jadwal kuliah dan tugas-tugas Anda dengan mudah</p>
        </header>

        <?php
        // Konfigurasi
        $jadwal_file = 'jadwal.json';
        $tugas_file = 'tugas.json';
        
        // Inisialisasi data jika file tidak ada
        if (!file_exists($jadwal_file)) {
            file_put_contents($jadwal_file, json_encode([]));
        }
        
        if (!file_exists($tugas_file)) {
            file_put_contents($tugas_file, json_encode([]));
        }
        
        // Baca data dari file
        $jadwal = json_decode(file_get_contents($jadwal_file), true) ?: [];
        $tugas = json_decode(file_get_contents($tugas_file), true) ?: [];
        
        // Proses form jadwal
        if (isset($_POST['tambah_jadwal'])) {
            $new_jadwal = [
                'id' => uniqid(),
                'matkul' => $_POST['matkul'],
                'hari' => $_POST['hari'],
                'jam' => $_POST['jam'],
                'ruangan' => $_POST['ruangan'],
                'dosen' => $_POST['dosen'],
                'sks' => (int)$_POST['sks']
            ];
            
            $jadwal[] = $new_jadwal;
            file_put_contents($jadwal_file, json_encode($jadwal, JSON_PRETTY_PRINT));
        }
        
        // Proses form tugas
        if (isset($_POST['tambah_tugas'])) {
            $new_tugas = [
                'id' => uniqid(),
                'matkul' => $_POST['tugas_matkul'],
                'judul' => $_POST['judul'],
                'deskripsi' => $_POST['deskripsi'],
                'deadline' => $_POST['deadline'],
                'selesai' => false
            ];
            
            $tugas[] = $new_tugas;
            file_put_contents($tugas_file, json_encode($tugas, JSON_PRETTY_PRINT));
        }
        
        // Tandai tugas sebagai selesai
        if (isset($_GET['selesai'])) {
            $task_id = $_GET['selesai'];
            foreach ($tugas as &$t) {
                if ($t['id'] === $task_id) {
                    $t['selesai'] = true;
                    break;
                }
            }
            file_put_contents($tugas_file, json_encode($tugas, JSON_PRETTY_PRINT));
        }
        
        // Hapus jadwal
        if (isset($_GET['hapus_jadwal'])) {
            $jadwal_id = $_GET['hapus_jadwal'];
            $jadwal = array_filter($jadwal, function($j) use ($jadwal_id) {
                return $j['id'] !== $jadwal_id;
            });
            $jadwal = array_values($jadwal); // Reset array keys
            file_put_contents($jadwal_file, json_encode($jadwal, JSON_PRETTY_PRINT));
        }
        
        // Hapus tugas
        if (isset($_GET['hapus_tugas'])) {
            $tugas_id = $_GET['hapus_tugas'];
            $tugas = array_filter($tugas, function($t) use ($tugas_id) {
                return $t['id'] !== $tugas_id;
            });
            $tugas = array_values($tugas); // Reset array keys
            file_put_contents($tugas_file, json_encode($tugas, JSON_PRETTY_PRINT));
        }
        
        // Fungsi bantu
        function getHariIndonesia($hari) {
            $hariMapping = [
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu',
                'Sunday' => 'Minggu'
            ];
            
            return $hariMapping[$hari] ?? $hari;
        }
        
        // Hitung total SKS
        $total_sks = 0;
        foreach ($jadwal as $j) {
            $total_sks += $j['sks'];
        }
        
        // Dapatkan jadwal hari ini
        $hari_ini = getHariIndonesia(date('l'));
        $jadwal_hari_ini = array_filter($jadwal, function($j) use ($hari_ini) {
            return strtolower($j['hari']) === strtolower($hari_ini);
        });
        
        // Dapatkan tugas yang belum selesai
        $tugas_belum_selesai = array_filter($tugas, function($t) {
            return !$t['selesai'];
        });
        
        // Dapatkan tugas yang mendekati deadline (kurang dari 3 hari)
        $tugas_mendekati_deadline = [];
        foreach ($tugas_belum_selesai as $t) {
            $deadline = new DateTime($t['deadline']);
            $sekarang = new DateTime();
            $interval = $sekarang->diff($deadline);
            
            if ($interval->days <= 3 && $interval->invert == 0) {
                $tugas_mendekati_deadline[] = $t;
            }
        }
        ?>
        
        <!-- Ringkasan -->
        <div class="summary">
            <div class="summary-item">
                <h3><?php echo count($jadwal); ?></h3>
                <p>Mata Kuliah</p>
            </div>
            <div class="summary-item">
                <h3><?php echo $total_sks; ?></h3>
                <p>Total SKS</p>
            </div>
            <div class="summary-item">
                <h3><?php echo count($tugas_belum_selesai); ?></h3>
                <p>Tugas Belum Selesai</p>
            </div>
        </div>
        
        <!-- Alert untuk tugas mendekati deadline -->
        <?php if (!empty($tugas_mendekati_deadline)): ?>
            <div class="alert">
                <strong>Peringatan!</strong> Anda memiliki <?php echo count($tugas_mendekati_deadline); ?> tugas yang mendekati deadline.
            </div>
        <?php endif; ?>
        
        <!-- Dashboard -->
        <div class="dashboard">
            <div class="card">
                <h2>Jadwal Hari Ini (<?php echo $hari_ini; ?>)</h2>
                <div class="jadwal-hari-ini">
                    <?php if (empty($jadwal_hari_ini)): ?>
                        <p>Tidak ada jadwal kuliah hari ini.</p>
                    <?php else: ?>
                        <?php foreach ($jadwal_hari_ini as $j): ?>
                            <div class="jadwal-item">
                                <strong><?php echo $j['matkul']; ?></strong> (<?php echo $j['sks']; ?> SKS)<br>
                                Jam: <?php echo $j['jam']; ?><br>
                                Ruang: <?php echo $j['ruangan']; ?><br>
                                Dosen: <?php echo $j['dosen']; ?>
                                <div style="margin-top: 5px;">
                                    <a href="?hapus_jadwal=<?php echo $j['id']; ?>" onclick="return confirm('Hapus jadwal ini?')" style="color: #ff4d4d; text-decoration: none;">Hapus</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card">
                <h2>Tugas Belum Selesai</h2>
                <div class="tugas-list">
                    <?php if (empty($tugas_belum_selesai)): ?>
                        <p>Selamat! Tidak ada tugas yang belum selesai.</p>
                    <?php else: ?>
                        <?php foreach ($tugas_belum_selesai as $t): 
                            $deadline = new DateTime($t['deadline']);
                            $sekarang = new DateTime();
                            $interval = $sekarang->diff($deadline);
                            $is_mendekati = $interval->days <= 3 && $interval->invert == 0;
                        ?>
                            <div class="tugas-item <?php echo $is_mendekati ? 'mendekati-deadline' : ''; ?>">
                                <strong><?php echo $t['matkul']; ?>: <?php echo $t['judul']; ?></strong><br>
                                Deadline: <?php echo date('d M Y', strtotime($t['deadline'])); ?><br>
                                <?php if ($is_mendekati): ?>
                                    <span style="color: #ff4d4d;">Tinggal <?php echo $interval->days; ?> hari lagi!</span><br>
                                <?php endif; ?>
                                <div style="margin-top: 5px;">
                                    <a href="?selesai=<?php echo $t['id']; ?>" style="color: #4caf50; text-decoration: none;">Tandai Selesai</a> | 
                                    <a href="?hapus_tugas=<?php echo $t['id']; ?>" onclick="return confirm('Hapus tugas ini?')" style="color: #ff4d4d; text-decoration: none;">Hapus</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Form Input -->
        <div class="forms-container">
            <div class="card">
                <h2>Tambah Jadwal Kuliah</h2>
                <form method="POST">
                    <div class="form-group">
                        <label for="matkul">Mata Kuliah</label>
                        <input type="text" id="matkul" name="matkul" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="hari">Hari</label>
                        <select id="hari" name="hari" required>
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="jam">Jam</label>
                        <input type="time" id="jam" name="jam" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="ruangan">Ruangan</label>
                        <input type="text" id="ruangan" name="ruangan" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="dosen">Dosen Pengampu</label>
                        <input type="text" id="dosen" name="dosen" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="sks">SKS</label>
                        <input type="number" id="sks" name="sks" min="1" max="6" required>
                    </div>
                    
                    <button type="submit" name="tambah_jadwal">Tambah Jadwal</button>
                </form>
            </div>
            
            <div class="card">
                <h2>Tambah Tugas</h2>
                <form method="POST">
                    <div class="form-group">
                        <label for="tugas_matkul">Mata Kuliah</label>
                        <input type="text" id="tugas_matkul" name="tugas_matkul" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="judul">Judul Tugas</label>
                        <input type="text" id="judul" name="judul" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="deadline">Deadline</label>
                        <input type="datetime-local" id="deadline" name="deadline" required>
                    </div>
                    
                    <button type="submit" name="tambah_tugas">Tambah Tugas</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>