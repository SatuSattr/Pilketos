<?php
session_start();
include 'conn.php';

$page_title = 'Dashboard';
include 'header.php';


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

// Query untuk total calon
$total_calon_query = "SELECT COUNT(*) as total FROM calon_ketua";
$total_calon_result = mysqli_query($conn, $total_calon_query);
$total_calon = mysqli_fetch_assoc($total_calon_result)['total'];
?>

<?php include 'sidebar.php'; ?>

<!-- Main Content -->
<div class="flex-1 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-6">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-accent">Dashboard</h1>
            <p class="text-gray-600 mt-2">Selamat datang di sistem pemilihan ketua OSIS</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">


            <div class="rounded-xl shadow-md bg-white">
                <div class="px-4 py-2">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm mb-0 capitalize text-gray-600">Jumlah Calon</p>
                            <h4 class="text-xl font-semibold text-gray-800"><?php echo $total_calon; ?></h4>
                        </div>
                        <div class="size-13 flex items-center justify-center bg-accent shadow-lg rounded-lg">
                            <i class="fas text-[1.2rem] fa-user-group text-primary"></i>
                        </div>
                    </div>
                </div>
                <hr class="border-t border-gray-200 my-0">
                <div class="px-4 py-2">
                    <p class="text-sm text-gray-600 mb-0">
                        <span class="text-green-600 font-semibold">+3% </span>than last month
                    </p>
                </div>
            </div>

            <div class="rounded-xl shadow-md bg-white">
                <div class="px-4 py-2">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm mb-0 capitalize text-gray-600">Total Votes</p>
                            <h4 class="text-xl font-semibold text-gray-800"><?php echo $total_vote; ?></h4>
                        </div>
                        <div class="size-13 flex items-center justify-center bg-accent shadow-lg rounded-lg">
                            <i class="text-[1.6rem] text-primary fa-regular fa-circle-check"></i>

                        </div>
                    </div>
                </div>
                <hr class="border-t border-gray-200 my-0">
                <div class="px-4 py-2">
                    <p class="text-sm text-gray-600 mb-0">
                        <span class="text-green-600 font-semibold">+3% </span>than last month
                    </p>
                </div>
            </div>


            <!-- <div class="bg-secondary rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Vote</p>
                        <p class="text-2xl font-bold text-accent"><?php echo $total_vote; ?></p>
                    </div>
                </div>
            </div> -->



            <div class="rounded-xl shadow-md bg-white">
                <div class="px-4 py-2">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm mb-0 capitalize text-gray-600">Tingkat Partisipasi</p>
                            <h4 class="text-xl font-semibold text-gray-800"><?php echo $total_vote > 0 ? number_format(($total_vote / 120) * 100, 1) : 0; ?>%</h4>
                        </div>
                        <div class="size-13 flex items-center justify-center bg-accent shadow-lg rounded-lg">
                            <i class="fas text-[1.2rem] fa-chart-simple text-primary"></i>
                        </div>
                    </div>
                </div>
                <hr class="border-t border-gray-200 my-0">
                <div class="px-4 py-2">
                    <p class="text-sm text-gray-600 mb-0">
                        <span class="text-green-600 font-semibold">+3% </span>than last month
                    </p>
                </div>
            </div>
        </div>


        <!-- Calon Cards -->
        <div class="mb-8">
            <h2 class="text-[1.4rem] font-semibold text-accent mb-6">Calon Ketos</h2>

            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-6">

                    <?php $no = 1;
                    while ($calon = mysqli_fetch_assoc($result)): ?>
                        <?php
                        $words = explode(" ", $calon['nama']);
                        $first = $words[0];
                        $second = isset($words[1]) ? $words[1] : "";
                        $third  = isset($words[2]) ? $words[2] : "";
                        ?>
                        <div
                            class="bg-white card w-[14rem] border-2 border-gray-200 rounded-xl shadow-lg hover:cursor-pointer hover:shadow-xl hover:border-birupesat transition-all duration-300 overflow-hidden max-w-sm group">
                            <div class="flex gap-3 p-6 border-b border-gray-100">
                                <h3 class="font-bold text-xl leading-6">
                                    <?php echo $first; ?><br />
                                    <span class="text-gray-500 text-[1rem] font-medium"><?php echo $second . " " . $third; ?></span>
                                </h3>
                            </div>
                            <div
                                class="h-48 bg-gradient-to-br from-gray-50 to-gray-200 flex items-center justify-center overflow-hidden relative">
                                <h1
                                    class="absolute duration-200 ease-in-out top-3 m-0 left-4 font-lato font-black opacity-20 text-7xl">
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
                            <div class="p-6 space-y-3">
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


                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada calon</h3>
                    <p class="text-gray-500">Silakan tambahkan calon ketua OSIS terlebih dahulu.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>

</html>