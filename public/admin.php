<?php
session_start();
include 'conn.php';
include 'islogin.php';

$page_title = 'Dashboard';
setcookie("last_refresh", time(), time() + 1200, "/");

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

$partisipasi = $total_vote > 0 ? number_format(($total_vote / $hak_suara) * 100, 1) : 0;
$partisipasi_add = 0;
if (isset($_COOKIE["partisipasi"])) {
    $partisipasi_add =  number_format(($partisipasi - (int) $_COOKIE["partisipasi"]), 1);
}
setcookie("partisipasi", $partisipasi, time() + 1200, "/");


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'header.php'; ?>
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="styles.css" />
</head>

<body class="flex">

    <div class="hidden lg:block">
        <?php include 'sidebar.php'; ?>
    </div>
    <!-- Main Content -->
    <div class="flex-1">
        <div class="p-6">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-accent">Dashboard</h1>
                <p class="text-gray-600 mt-2">Selamat datang di sistem pemilihan ketua OSIS</p>
            </div>

            <!-- Stats Cards -->
            <div class="flex lg:grid lg:grid-cols-3 flex-wrap gap-3 lg:gap-6 mb-8">


                <div class="w-full rounded-xl shadow-md bg-white">
                    <div class="px-4 py-2">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm mb-1 capitalize text-gray-600">Jumlah Calon</p>
                                <h4 class="text-3xl font-semibold text-gray-800"><?php echo $total_calon; ?></h4>
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

                <div class="w-full rounded-xl shadow-md bg-white">
                    <div class="px-4 py-2">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm mb-1 capitalize text-gray-600">Total Votes</p>
                                <h4 class="text-3xl font-semibold text-gray-800"><?php echo $total_vote; ?></h4>
                            </div>
                            <div class="size-13 p-3 flex items-center justify-center bg-accent shadow-lg rounded-lg">
                                <i class="text-[1.6rem] text-primary fa-regular fa-circle-check"></i>

                            </div>
                        </div>
                    </div>
                    <hr class="border-t border-gray-200 my-0">
                    <div class="px-4 py-2">
                        <p class="text-sm text-gray-600 mb-0">
                            <span class="text-green-600 font-semibold">+<?= $total_vote_add; ?> </span>than last <?= $last_refresh; ?> seconds
                        </p>
                    </div>
                </div>



                <div class="w-full rounded-xl shadow-md bg-white">
                    <div class="px-4 py-2">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm mb-1 capitalize text-gray-600">Tingkat Partisipasi</p>
                                <h4 class="text-3xl font-semibold text-gray-800"><?php echo $partisipasi; ?>%</h4>
                            </div>
                            <div class="size-13 p-3 flex items-center justify-center bg-accent shadow-lg rounded-lg">
                                <i class="fas text-[1.2rem] fa-chart-simple text-primary"></i>
                            </div>
                        </div>
                    </div>
                    <hr class="border-t border-gray-200 my-0">
                    <div class="px-4 py-2">
                        <p class="text-xs lg:text-sm text-gray-600 mb-0">
                            <span class="text-green-600 font-semibold">+<?= $partisipasi_add; ?>% </span>than last <?= $last_refresh; ?> seconds
                        </p>
                    </div>
                </div>
            </div>


            <!-- Calon Cards -->
            <div class="mb-8">
                <h2 class="text-[1.4rem] font-semibold text-accent mb-6">Calon Ketos</h2>

                <?php if (mysqli_num_rows($result) > 0): ?>
                    <div class="flex gap-3 flex-wrap lg:gap-6">
                        <script>
                            function toggleCaketos(id) {
                                // Cek apakah ukuran layar termasuk 'lg' ke atas (min-width: 1024px)
                                if (!window.matchMedia("(min-width: 1024px)").matches) return;

                                const panel = document.getElementById(`caketos-${id}`);
                                const panel_container = document.getElementById(`caketos-container-${id}`);
                                const isVisible = panel.classList.contains("-right-[105%]");

                                if (!isVisible) {
                                    panel_container.classList.remove("min-w-[16rem]");
                                    panel_container.classList.add("w-[31rem]");
                                    panel.classList.add("-right-[105%]");
                                    panel.classList.remove("right-0");
                                } else {
                                    panel_container.classList.remove("w-[31rem]");
                                    panel_container.classList.add("min-w-[16rem]");
                                    panel.classList.remove("-right-[105%]");
                                    panel.classList.add("right-0");
                                }
                            }
                        </script>
                        <?php $no = 1;
                        while ($calon = mysqli_fetch_assoc($result)): ?>
                            <?php
                            $words = explode(" ", $calon['nama']);
                            $first = $words[0];
                            $second = isset($words[1]) ? $words[1] : "";
                            $third  = isset($words[2]) ? $words[2] : "";
                            ?>
                            <div id="caketos-container-<?php echo $calon['nomor'] ?>" class="w-[48%] lg:w-auto transition-all duration-150 ease-in">
                                <div class="flex lg:w-[15rem] items-center relative">
                                    <div
                                        onclick="toggleCaketos(<?php echo $calon['nomor'] ?>);"
                                        class="bg-white z-10 card w-full border-2 border-gray-200 rounded-xl shadow-lg hover:cursor-pointer hover:shadow-xl hover:border-birupesat transition-all duration-300 overflow-hidden max-w-sm group">
                                        <div class="flex gap-3 p-3 lg:p-6 border-b border-gray-100">
                                            <h3 class="font-bold text-xl leading-6">
                                                <?php echo $first; ?><br />
                                                <span class="text-gray-500 text-[1rem] font-medium"><?php echo $second . " " . $third; ?></span>
                                            </h3>
                                        </div>
                                        <div
                                            class="h-48 bg-gradient-to-br from-gray-50 to-gray-200 flex items-center justify-center overflow-hidden relative">
                                            <h1
                                                class="absolute duration-200 ease-in-out top-3 m-0 left-4 font-black opacity-20 text-6xl lg:text-7xl">
                                                <?php echo "0" . $calon['nomor'];
                                                $no++ ?>
                                            </h1>
                                            <?php if (!empty($calon['url_foto'])): ?>
                                                <img
                                                    class="size-[146%] object-cover absolute -top-3 -right-9"
                                                    src="<?php echo $calon['url_foto'] ?>"
                                                    alt="<?php echo $calon['nama'] ?>" />
                                            <?php else: ?>
                                                <i class="absolute bottom-0 right-0 far opacity-30 text-9xl fa-user"></i>
                                            <?php endif; ?>

                                        </div>
                                        <div class="p-3 lg:p-6 space-y-3">
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-500 font-medium">VOTERS</span>
                                                <span class="text-birupesat font-semibold"><?php echo $calon['jumlah_vote']; ?></span>
                                            </div>
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-500 font-medium">KELAS</span>
                                                <span class="text-accent font-semibold"><?php echo $calon['nama_kelas']; ?></span>
                                            </div>
                                            <?php if ($total_vote > 0): ?>
                                                <div class="flex gap-2 items-center">
                                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                                        <div class="bg-birupesat h-2 rounded-full" style="width: <?php echo ($calon['jumlah_vote'] / $total_vote) * 100; ?>%"></div>
                                                    </div>
                                                    <span class="text-sm text-accent font-semibold"><?php echo substr(($calon['jumlah_vote'] / $total_vote) * 100, 0, 4) . "%"; ?></span>

                                                </div>

                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div
                                        id="caketos-<?php echo $calon['nomor'] ?>"
                                        class="right-0 absolute flex flex-col z-0 pl-5 p-3 h-[90%]  transition-[right] duration-100 ease-in-out max-w-[16rem] bg-white border-l-0 border-2 border-gray-200 rounded-r-lg shadow-lg">
                                        <div class="visi">
                                            <h1 class="font-semibold text-[1.2rem]">Visi</h1>
                                            <p class="max-h-[6rem] leading-5 font-normal text-[13px] text-wrap pr-1">
                                                <?php echo substr($calon['visi'], 0, 118) . '...' ?>
                                            </p>
                                        </div>
                                        <div class="border-b border-neutral-300 misi pb-5">
                                            <h1 class="font-semibold text-[1.2rem] mt-3">Misi</h1>
                                            <p class="max-h-[6rem] leading-5 font-normal text-[13px] text-wrap pr-1">
                                                <?php echo substr($calon['misi'], 0, 118) . '...' ?>
                                            </p>
                                        </div>
                                        <div class="mt-5 action flex gap-2 w-full items-center justify-center">
                                            <a href="calon.php?edit=<?php echo $calon['id']; ?>" class="p-2 px-4 w-full text-sm rounded-xl flex items-center hover:bg-accent transition-all duration-100 hover:text-neutral-100 justify-center bg-neutral-300 text-neutral-700 text-semibold">Edit</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12">
                        <i class="fas fa-user-group text-neutral-400 text-5xl">?</i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada calon</h3>
                        <p class="text-gray-500">Silakan tambahkan calon ketua OSIS terlebih dahulu.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

</html>