<?php
session_start();
include 'conn.php';

$page_title = 'Laporan';
include 'header.php';

// Query untuk mendapatkan data laporan
$query = "SELECT c.*, k.name as nama_kelas, COUNT(v.id) as jumlah_vote 
          FROM calon_ketua c 
          LEFT JOIN kelas k ON c.id_kelas = k.id 
          LEFT JOIN vote v ON c.id = v.id_calon 
          GROUP BY c.id 
          ORDER BY jumlah_vote DESC";
$result = mysqli_query($conn, $query);

// Query untuk total vote
$total_vote_query = "SELECT COUNT(*) as total FROM vote";
$total_vote_result = mysqli_query($conn, $total_vote_query);
$total_vote = mysqli_fetch_assoc($total_vote_result)['total'];

// Query untuk vote per hari (7 hari terakhir)
$vote_per_hari_query = "SELECT DATE(created_at) as tanggal, COUNT(*) as jumlah 
                        FROM vote 
                        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                        GROUP BY DATE(created_at) 
                        ORDER BY tanggal DESC";
$vote_per_hari_result = mysqli_query($conn, $vote_per_hari_query);
?>

<?php include 'sidebar.php'; ?>

<!-- Main Content -->
<div class="flex-1 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-6">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-accent">Laporan Pilketos</h1>
            <p class="text-gray-600 mt-2">Laporan perolehan suara pemilihan ketua OSIS</p>
        </div>
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-secondary rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="text-lg font-semibold text-accent mb-4">Total Suara Masuk</h3>
                <div class="text-4xl font-bold text-accent"><?php echo $total_vote; ?></div>
                <p class="text-gray-600 mt-2">Total suara yang telah masuk</p>
            </div>
            
            <div class="bg-secondary rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="text-lg font-semibold text-accent mb-4">Tingkat Partisipasi</h3>
                <div class="text-4xl font-bold text-accent"><?php echo $total_vote > 0 ? number_format(($total_vote / 100) * 100, 1) : 0; ?>%</div>
                <p class="text-gray-600 mt-2">Dari total siswa yang memiliki hak pilih</p>
            </div>
        </div>
        
        <!-- Hasil Perolehan Suara -->
        <div class="bg-secondary rounded-xl shadow-sm border border-gray-100 mb-8">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-accent">Perolehan Suara Calon Ketua OSIS</h2>
            </div>
            
            <div class="p-6">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <div class="space-y-6">
                        <?php 
                        $ranking = 1;
                        while ($calon = mysqli_fetch_assoc($result)): 
                            $persentase = $total_vote > 0 ? ($calon['jumlah_vote'] / $total_vote) * 100 : 0;
                        ?>
                            <div class="border border-gray-200 rounded-lg p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-accent text-secondary rounded-full flex items-center justify-center font-bold text-lg mr-4">
                                            <?php echo $ranking; ?>
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-bold text-accent"><?php echo $calon['nama']; ?></h3>
                                            <p class="text-gray-600">Kelas: <?php echo $calon['nama_kelas']; ?></p>
                                        </div>
                                    </div>
                                    
                                    <div class="text-right">
                                        <div class="text-3xl font-bold text-accent"><?php echo $calon['jumlah_vote']; ?></div>
                                        <div class="text-sm text-gray-600">suara</div>
                                    </div>
                                </div>
                                
                                <!-- Progress Bar -->
                                <div class="mb-2">
                                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                                        <span>Persentase</span>
                                        <span><?php echo number_format($persentase, 1); ?>%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-3">
                                        <div class="bg-accent h-3 rounded-full transition-all duration-500" 
                                             style="width: <?php echo $persentase; ?>%"></div>
                                    </div>
                                </div>
                                
                                <!-- Visi Misi Preview -->
                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <h4 class="font-semibold text-accent mb-2">Visi:</h4>
                                        <p class="text-sm text-gray-600"><?php echo substr($calon['visi'], 0, 150) . '...'; ?></p>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-accent mb-2">Misi:</h4>
                                        <p class="text-sm text-gray-600"><?php echo substr($calon['misi'], 0, 150) . '...'; ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php 
                        $ranking++;
                        endwhile; 
                        ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada data</h3>
                        <p class="text-gray-500">Belum ada calon atau vote yang masuk.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Vote per Hari -->
        <div class="bg-secondary rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-accent">Aktivitas Voting (7 Hari Terakhir)</h2>
            </div>
            
            <div class="p-6">
                <?php if (mysqli_num_rows($vote_per_hari_result) > 0): ?>
                    <div class="space-y-4">
                        <?php while ($vote_hari = mysqli_fetch_assoc($vote_per_hari_result)): ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="font-semibold text-accent">
                                        <?php echo date('d F Y', strtotime($vote_hari['tanggal'])); ?>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <?php echo date('l', strtotime($vote_hari['tanggal'])); ?>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-accent"><?php echo $vote_hari['jumlah']; ?></div>
                                    <div class="text-sm text-gray-600">suara</div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8">
                        <p class="text-gray-500">Belum ada aktivitas voting dalam 7 hari terakhir.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Print Button -->
        <div class="mt-8 text-center">
            <button onclick="window.print()" class="bg-accent text-secondary py-3 px-6 rounded-xl font-medium hover:bg-gray-800 transition-colors">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Cetak Laporan
            </button>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    body {
        background: white !important;
    }
    
    .bg-secondary {
        background: white !important;
        box-shadow: none !important;
    }
}
</style>

</body>
</html>
