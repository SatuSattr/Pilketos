<?php
session_start();
include 'conn.php';

$page_title = 'Laporan';
include 'islogin.php';

$file = 'config.json';
$config = json_decode(file_get_contents($file), true);
setcookie("last_refresh", time(), time() + 1200, "/");

// Query untuk mendapatkan data laporan
$query = "SELECT c.*, k.name as nama_kelas, COUNT(v.id) as jumlah_vote 
          FROM calon_ketua c 
          LEFT JOIN kelas k ON c.id_kelas = k.id 
          LEFT JOIN vote v ON c.id = v.id_calon 
          GROUP BY c.id 
          ORDER BY jumlah_vote DESC";
$result = mysqli_query($conn, $query);


$last_refresh = 0;
if (isset($_COOKIE["last_refresh"])) $last_refresh = time() - (int) $_COOKIE["last_refresh"];


$hak_suara_query = "SELECT COUNT(*) as total FROM hak_suara";
$hak_suara_result = mysqli_query($conn, $hak_suara_query);
$hak_suara = mysqli_fetch_assoc($hak_suara_result)['total'];



$total_vote_query = "SELECT COUNT(*) as total FROM vote";
$total_vote_result = mysqli_query($conn, $total_vote_query);
$total_vote = mysqli_fetch_assoc($total_vote_result)['total'];
$total_vote_add = 0;
if (isset($_COOKIE["total_vote"])) {
    $total_vote_add =  $total_vote - (int) $_COOKIE["total_vote"];
}
setcookie("total_vote", $total_vote, time() + 1200, "/");

$total_calon_query = "SELECT COUNT(*) as total FROM calon_ketua";
$total_calon_result = mysqli_query($conn, $total_calon_query);
$total_calon = mysqli_fetch_assoc($total_calon_result)['total'];
$total_calon_add = 0;
if (isset($_COOKIE["total_calon"])) {
    $total_calon_add =  $total_calon - (int) $_COOKIE["total_calon"];
}
setcookie("total_calon", $total_calon, time() + 1200, "/");

$partisipasi = $total_vote > 0 ? number_format(($total_vote / $config['haksuara']) * 100, 1) : 0;
$partisipasi_add = 0;
if (isset($_COOKIE["partisipasi"])) {
    $partisipasi_add =  number_format(($partisipasi - (int) $_COOKIE["partisipasi"]), 1);
}
setcookie("partisipasi", $partisipasi, time() + 1200, "/");



$total_every_query = "SELECT calon_ketua.nama, COUNT(*) AS jumlah_vote FROM vote JOIN calon_ketua ON vote.id_calon = calon_ketua.id GROUP BY calon_ketua.nama;
";
$total_every_result = mysqli_query($conn, $total_every_query);

$labels = [];
$data = [];

if ($total_every_result->num_rows > 0) {
    while ($row = $total_every_result->fetch_assoc()) {
        $labels[] = strtok($row['nama'], " ");
        $data[] = (int)$row['jumlah_vote'];
    }
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'header.php'; ?>
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="styles.css" />
</head>

<body class="flex">
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

                <div class="rounded-xl shadow-md bg-white">
                    <div class="px-4 py-2">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm mb-1 capitalize text-gray-600">Jumlah Calon</p>
                                <h4 class="text-3xl font-semibold text-gray-800"><?= $total_calon; ?></h4>
                            </div>
                            <div class="size-13 p-3 flex items-center justify-center bg-accent shadow-lg rounded-lg">
                                <i class="fas text-[1.2rem] fa-user-group text-primary"></i>
                            </div>
                        </div>
                    </div>
                    <hr class="border-t border-gray-200 my-0">
                    <div class="px-4 py-2">
                        <p class="text-sm text-gray-600 mb-0">
                            <span class="text-green-600 font-semibold">+<?= $total_calon_add ?> </span>than last <?= $last_refresh; ?> seconds
                        </p>
                    </div>
                </div>

                <div class="rounded-xl shadow-md bg-white">
                    <div class="px-4 py-2">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm mb-1 capitalize text-gray-600">Total Suara Masuk</p>
                                <h4 class="text-3xl font-semibold text-gray-800"><?= $total_vote; ?><span class="ml-2 text-gray-600 text-[1rem] font-normal">/ <?= $hak_suara; ?></span></h4>
                            </div>
                            <div class="size-13 p-3 flex items-center justify-center bg-accent shadow-lg rounded-lg">
                                <i class="text-[1.3rem] far fa-circle-check text-primary"></i>
                            </div>
                        </div>
                    </div>
                    <hr class="border-t border-gray-200 my-0">
                    <div class="px-4 py-2">
                        <p class="text-sm text-gray-600 mb-0">
                            <span class="text-green-600 font-semibold">+<?= $total_vote_add ?> </span>than last <?= $last_refresh; ?> seconds
                        </p>
                    </div>
                </div>



                <div class="rounded-xl shadow-md bg-white">
                    <div class="px-4 py-2">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm mb-1 capitalize text-gray-600">Tingkat Partisipasi</p>
                                <h4 class="text-3xl font-semibold text-gray-800"><?= $total_vote > 0 ? number_format(($total_vote / $hak_suara) * 100, 1) : 0; ?>%</h4>
                            </div>
                            <div class="size-13 p-3 flex items-center justify-center bg-accent shadow-lg rounded-lg">
                                <i class="fas text-[1.2rem] fa-chart-simple text-primary"></i>
                            </div>
                        </div>
                    </div>
                    <hr class="border-t border-gray-200 my-0">
                    <div class="px-4 py-2">
                        <p class="text-sm text-gray-600 mb-0">
                            <span class="text-green-600 font-semibold">+<?= $partisipasi_add ?>%</span>than last <?= $last_refresh; ?> seconds
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex gap-6 mb-8">
                <!-- Perolehan -->
                <div class="bg-secondary rounded-xl max-w-[55rem] shadow-lg border border-gray-100 mb-8">
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
                                                <?php if ($ranking == 1): ?>
                                                    <div class="w-12 h-12 bg-yellow-500 text-secondary relative rounded-full flex items-center justify-center font-bold text-lg mr-4">
                                                        <!-- #<?php echo $ranking; ?> -->
                                                        <i class="fa-solid fa-crown text-primary"></i>
                                                        <!-- <i class="fa-solid fa-crown absolute text-yellow-500 -top-[10px] -left-[2px] -rotate-[27deg]"></i> -->
                                                    </div>
                                                <?php else: ?>
                                                    <div class="w-12 h-12 bg-accent text-secondary rounded-full flex items-center justify-center font-bold text-lg mr-4">
                                                        #<?php echo $ranking; ?>
                                                    </div>
                                                <?php endif ?>
                                                <div>
                                                    <h3 class="text-xl font-bold text-accent"><?php echo $calon['nama']; ?></h3>
                                                    <div class="mini-info flex gap-3">
                                                        <p class="text-gray-600">Nomor Urut: <?php echo $calon['nomor']; ?></p>
                                                        <p class="text-gray-600">Kelas: <?php echo $calon['nama_kelas']; ?></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-right">
                                                <div class="text-3xl font-bold text-accent"><?php echo $calon['jumlah_vote']; ?></div>
                                                <div class="text-sm text-gray-600">suara</div>
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <div class="flex justify-between text-sm text-gray-600 mb-1">
                                                <span>Persentase</span>
                                                <span><?php echo number_format($persentase, 1); ?>%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-3">
                                                <div class="bg-gradient-to-r from-neutral-800 via-zinc-800 to-zinc-600 h-3 rounded-full transition-all duration-500"
                                                    style="width: <?php echo $persentase; ?>%"></div>
                                            </div>
                                        </div>


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

                <!-- Chart -->
                <div class="bg-secondary rounded-xl min-w-[40rem] h-fit shadow-lg border border-gray-100 mb-8">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-accent">Grafik Statistik Perolehan Suara</h2>
                    </div>

                    <div class="p-6 relative w-full h-full">
                        <canvas id="voteChart"></canvas>
                    </div>
                </div>

                <script>
                    const labels = <?php echo json_encode($labels); ?>;
                    const data = <?php echo json_encode($data); ?>;

                    const config = {
                        type: 'polarArea',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Suara',
                                data: data,
                                backgroundColor: [
                                    '#3D3B40b0',
                                    '#525CEBb0',
                                    '#BFCFE7b0',
                                    '#7AC6D2b0',
                                    '#DDA853b0'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                r: {
                                    pointLabels: {
                                        display: true,
                                        centerPointLabels: true,
                                        font: {
                                            size: 16
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'top'
                                },
                                title: {
                                    display: false,
                                    text: 'Vote Polar Area Chart per Calon'
                                }
                            }
                        }
                    };

                    new Chart(document.getElementById('voteChart'), config);
                </script>
            </div>

        </div>
    </div>



</body>

</html>