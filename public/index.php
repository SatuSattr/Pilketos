<?php
include 'conn.php';

// Handle vote submission
$vote_success = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_calon'], $_POST['nisn'])) {
    $id_calon = $_POST['id_calon'];
    $nisn_voter = $_POST['nisn'];

    // Validasi format NISN: hanya angka, 8-10 digit
    if (!preg_match('/^\d{8,10}$/', $nisn_voter)) {
        $error_message = "Format NISN tidak valid. Harus terdiri dari 8-10 digit angka.";
    } else {
        // Cek apakah NISN terdaftar di tabel hak_suara
        $check_nisn_query = "SELECT nisn FROM hak_suara WHERE nisn = '$nisn_voter'";
        $check_nisn_result = mysqli_query($conn, $check_nisn_query);

        if (mysqli_num_rows($check_nisn_result) === 0) {
            $error_message = "NISN tidak terdaftar sebagai pemilih sah.";
        } else {
            // Cek apakah calon valid
            $check_calon_query = "SELECT id FROM calon_ketua WHERE id = '$id_calon'";
            $check_calon_result = mysqli_query($conn, $check_calon_query);

            if (mysqli_num_rows($check_calon_result) === 0) {
                $error_message = "Calon yang dipilih tidak valid.";
            } else {
                // Cek apakah NISN sudah pernah voting
                $check_vote_query = "SELECT id FROM vote WHERE nisn = '$nisn_voter'";
                $check_vote_result = mysqli_query($conn, $check_vote_query);

                if (mysqli_num_rows($check_vote_result) > 0) {
                    $error_message = "NISN ini sudah pernah melakukan voting. Anda tidak dapat memilih dua kali.";
                } else {
                    // Semua valid, simpan vote
                    $vote_query = "INSERT INTO vote (id_calon, nisn) VALUES ('$id_calon', '$nisn_voter')";
                    if (mysqli_query($conn, $vote_query)) {
                        $vote_success = true;
                    } else {
                        $error_message = "Terjadi kesalahan saat menyimpan vote. Silakan coba lagi.";
                    }
                }
            }
        }
    }
}



$file = 'config.json';
$config = json_decode(file_get_contents($file), true);


// Get all candidates with vote count for ranking
$query = "SELECT c.*, k.name as nama_kelas, COUNT(v.id) as jumlah_vote
          FROM calon_ketua c 
          LEFT JOIN kelas k ON c.id_kelas = k.id 
          LEFT JOIN vote v ON c.id = v.id_calon
          GROUP BY c.id
          ORDER BY c.id ASC";
$result = mysqli_query($conn, $query);

$total_vote_query = "SELECT COUNT(*) as total FROM vote";
$total_vote_result = mysqli_query($conn, $total_vote_query);
$total_vote = mysqli_fetch_assoc($total_vote_result)['total'];


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilketos | Pilih Calon Ketua OSIS</title>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/35d8865ade.js" crossorigin="anonymous"></script>

    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="styles.css" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">


    <style>
        /* Custom SweetAlert2 Styling */
        .swal2-popup {
            font-family: 'Montserrat', sans-serif !important;
            border-radius: 1.5rem !important;
            padding: 2rem !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
        }

        .swal2-title {
            color: #1A1A1B !important;
            font-weight: 700 !important;
            font-size: 1.875rem !important;
            margin-bottom: 1rem !important;
        }

        .swal2-html-container {
            color: #6B7280 !important;
            font-size: 1rem !important;
            line-height: 1.5 !important;
            margin-bottom: 1.5rem !important;
        }

        .swal2-confirm {
            background-color: #1A1A1B !important;
            color: #FFFFFF !important;
            border: none !important;
            border-radius: 0.75rem !important;
            padding: 0.75rem 2rem !important;
            font-weight: 600 !important;
            font-size: 1rem !important;
            transition: all 0.3s ease !important;
            box-shadow: none !important;
        }

        .swal2-confirm:hover {
            background-color: #374151 !important;
            transform: translateY(-1px) !important;
        }

        .swal2-cancel {
            background-color: #6B7280 !important;
            color: #FFFFFF !important;
            border: none !important;
            border-radius: 0.75rem !important;
            padding: 0.75rem 2rem !important;
            font-weight: 600 !important;
            font-size: 1rem !important;
            margin-right: 1rem !important;
            box-shadow: none !important;
        }

        .swal2-cancel:hover {
            background-color: #4B5563 !important;
        }

        .swal2-icon.swal2-success {
            border-color: #10B981 !important;
            color: #10B981 !important;
        }

        .swal2-icon.swal2-error {
            border-color: #EF4444 !important;
            color: #EF4444 !important;
        }

        .swal2-icon.swal2-warning {
            border-color: #F59E0B !important;
            color: #F59E0B !important;
        }

        .swal2-icon.swal2-question {
            border-color: #3B82F6 !important;
            color: #3B82F6 !important;
        }

        /* Card Styles */
        .card.selected {
            border-color: #2f2575 !important;
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4) !important;
        }

        .card.selected .selection-indicator {
            opacity: 100;
        }
    </style>
</head>

<body class="bg-primary font-montserrat min-h-screen">
    <!-- Header -->
    <div class="bg-secondary shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex gap-3 items-center">
                    <img src="img/logo.png" alt="" class="size-9">
                    <div>
                        <h1 class="text-xl font-bold text-accent">PILKETOS</h1>
                        <p class="text-xs text-gray-600">v1.0</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-accent">Sistem Voting</p>
                    <p class="text-xs text-gray-600">Pilih calon favorit Anda</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Title Section -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-accent mb-4">Pemilihan Ketua OSIS</h1>
            <?php if ($config['haksuara'] - $total_vote > 0): ?>
                <p class="text-xl text-gray-600 mb-2">Pilih satu calon ketua OSIS favorit Anda</p>
            <?php else: ?>
                <p class="text-xl text-red-600 mb-2">Pemilihan suara ditutup! hak suara sudah mencapai batas</p>
            <?php endif; ?>
        </div>

        <!-- Voting Form -->
        <form id="votingForm" method="POST" class="space-y-8">
            <!-- Candidates Grid -->
            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="flex gap-8 items-center justify-center">
                    <?php
                    $no = 1;
                    while ($calon = mysqli_fetch_assoc($result)):
                        // Split name into parts
                        $words = explode(" ", $calon['nama']);
                        $first = $words[0];
                        $second = isset($words[1]) ? $words[1] : "";
                        $third = isset($words[2]) ? $words[2] : "";
                    ?>
                        <div id="caketos-container-<?php echo $no; ?>" class=" transition-all duration-150 ease-in">
                            <div class="flex w-[22rem] group items-center relative">
                                <div class="bg-white z-10 card w-full border-2 border-gray-200 rounded-xl shadow-lg hover:shadow-xl <?php if ($config['haksuara'] - $total_vote > 0) echo "hover:border-birupesat"; ?> transition-all duration-300 overflow-hidden max-w-sm group relative">
                                    <!-- Selection Indicator -->
                                    <i class="selection-indicator opacity-0 text-birupesat absolute top-2.5 right-2.5 text-2xl  fa-solid fa-circle-check z-20 transition-opacity duration-150 ease-in-out"></i>


                                    <!-- Radio Input (Hidden) -->
                                    <input type="radio" name="id_calon" <?php if ($config['haksuara'] - $total_vote <= 0) echo "disabled"; ?> value="<?php echo $calon['id']; ?>" id="calon_<?php echo $calon['id']; ?>" class="hidden candidate-radio">

                                    <!-- Card Content -->
                                    <label for="calon_<?php echo $calon['id']; ?>" class="<?php if ($config['haksuara'] - $total_vote <= 0) echo "saturate-0 cursor-not-allowed"; ?> block">
                                        <div class="flex gap-3 p-6 border-b border-gray-100">
                                            <h3 class="font-bold text-2xl leading-6">
                                                <?php echo $first; ?><br />
                                                <span class="text-gray-500 text-xl font-medium"><?php echo $second . " " . $third; ?></span>
                                            </h3>
                                        </div>
                                        <div class="h-[22rem] bg-gradient-to-br from-gray-50 to-gray-200 flex items-center justify-center overflow-hidden relative">
                                            <h1 class="absolute duration-200 ease-in-out top-3 m-0 left-4 font-bold opacity-20 text-9xl">
                                                <?php echo "0" . $calon['nomor']; ?>
                                            </h1>
                                            <?php if (!empty($calon['url_foto'])): ?>
                                                <img class="size-[140%] object-cover absolute -top-3 -right-9" src="<?php echo $calon['url_foto']; ?>" alt="<?php echo $calon['nama']; ?>" />
                                            <?php else: ?>
                                                <svg class="absolute bottom-0 right-0 w-32 h-32 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            <?php endif; ?>
                                        </div>
                                        <div class="p-6 space-y-3">
                                            <div class="flex justify-between text-xl">
                                                <span class="text-gray-500 font-medium">KELAS</span>
                                                <span class="text-accent font-semibold"><?php echo $calon['nama_kelas']; ?></span>
                                            </div>

                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    <?php
                        $no++;
                    endwhile;
                    ?>
                </div>

                <!-- Vote Button -->
                <div class="text-center mt-12">
                    <button type="submit" id="voteButton" disabled class="bg-gray-400 text-white py-4 px-12 rounded-2xl font-bold text-lg transition-all duration-300 cursor-not-allowed">
                        Pilih Calon Favorit
                    </button>
                    <p class="text-sm text-gray-500 mt-3">Silakan pilih salah satu calon terlebih dahulu</p>
                </div>

            <?php else: ?>
                <div class="text-center py-16">
                    <svg class="w-24 h-24 text-gray-400 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Belum Ada Calon</h3>
                    <p class="text-gray-600">Saat ini belum ada calon ketua OSIS yang terdaftar.</p>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <!-- Footer -->
    <footer class="bg-secondary border-t border-gray-200 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="text-center">
                <p class="text-sm text-gray-600">© 2025 Pilketos v1.0 - Sistem Pemilihan Ketua OSIS</p>
                <p class="text-xs text-gray-500 mt-1">Suara Anda sangat berharga untuk masa depan sekolah</p>
            </div>
        </div>
    </footer>

    <script>
        // Handle candidate selection
        const candidateRadios = document.querySelectorAll('.candidate-radio');
        const candidateCards = document.querySelectorAll('.card');
        const voteButton = document.getElementById('voteButton');

        function selectCandidate(id) {
            const radio = document.getElementById(`calon_${id}`);
            radio.checked = true;

            // Reset all cards
            candidateCards.forEach(card => {
                card.classList.remove('selected');
            });

            // Highlight selected card
            radio.closest('.card').classList.add('selected');

            // Enable vote button
            voteButton.disabled = false;
            voteButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
            voteButton.classList.add('bg-birupesat', 'hover:bg-blue-700', 'cursor-pointer');
            voteButton.textContent = 'VOTE SEKARANG!';
            voteButton.nextElementSibling.textContent = 'Klik untuk memberikan suara Anda';

            // Close all panels
            document.querySelectorAll('[id^="caketos-"]').forEach(p => {
                p.style.right = '';
            });
        }

        candidateRadios.forEach((radio) => {
            radio.addEventListener('change', function() {
                // Reset all cards
                candidateCards.forEach(card => {
                    card.classList.remove('selected');
                });

                // Highlight selected card
                if (this.checked) {
                    this.closest('.card').classList.add('selected');

                    // Enable vote button
                    voteButton.disabled = false;
                    voteButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
                    voteButton.classList.add('bg-birupesat', 'hover:bg-blue-700', 'cursor-pointer');
                    voteButton.textContent = 'VOTE SEKARANG!';
                    voteButton.nextElementSibling.textContent = 'Klik untuk memberikan suara Anda';
                }
            });
        });

        // Handle form submission
        document.getElementById('votingForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const selectedCandidate = document.querySelector('input[name="id_calon"]:checked');
            if (!selectedCandidate) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilihan Belum Dipilih',
                    text: 'Silakan pilih salah satu calon terlebih dahulu!',
                    confirmButtonText: 'OK, Mengerti'
                });
                return;
            }

            // Get candidate name for confirmation
            const candidateCard = selectedCandidate.closest('.card');
            const candidateName = candidateCard.querySelector('h3').textContent.trim().replace(/\s+/g, ' ');

            // Show confirmation dialog
            Swal.fire({
                icon: 'question',
                title: 'Konfirmasi Pilihan',
                html: `Apakah Anda yakin ingin memilih <strong>${candidateName}</strong> sebagai calon ketua OSIS?<br><br><small class="text-gray-500">Masukkan NISN Anda untuk konfirmasi. Pilihan tidak dapat diubah setelah dikonfirmasi.</small>`,
                input: 'text',
                inputLabel: 'Masukkan NISN',
                inputPlaceholder: 'Contoh: 1234567890',
                inputAttributes: {
                    maxlength: 10,
                    autocapitalize: 'off',
                    autocorrect: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Ya, Pilih Calon Ini',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                preConfirm: (nisn) => {
                    if (!nisn) {
                        Swal.showValidationMessage('NISN tidak boleh kosong!');
                    } else if (!/^\d+$/.test(nisn)) {
                        Swal.showValidationMessage('NISN harus berupa angka!');
                    } else if (nisn.length < 8 || nisn.length > 10) {
                        Swal.showValidationMessage('NISN tidak valid!');
                    }
                    return nisn;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const nisn = result.value;

                    // Tambahkan input hidden ke form untuk mengirim NISN ke backend
                    const form = document.getElementById('votingForm');
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'nisn';
                    hiddenInput.value = nisn;
                    form.appendChild(hiddenInput);

                    // Tampilkan loading
                    Swal.fire({
                        title: 'Memproses Vote...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    form.submit();
                }
            });

        });


        <?php if ($vote_success): ?>
            window.addEventListener('load', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Vote Berhasil!',
                    html: 'Terima kasih telah berpartisipasi dalam pemilihan ketua OSIS.',
                    confirmButtonText: 'Close',
                    timer: 1500,
                    timerProgressBar: true
                }).then(() => {
                    resetForm();
                });
            });
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            window.addEventListener('load', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: '<?php echo $error_message; ?>',
                    confirmButtonText: 'Coba Lagi'
                });
            });
        <?php endif; ?>

        // Reset form function
        function resetForm() {
            // Reset form
            document.getElementById('votingForm').reset();

            // Reset all cards
            candidateCards.forEach(card => {
                card.classList.remove('selected');
            });

            // Reset vote button
            voteButton.disabled = true;
            voteButton.classList.remove('bg-birupesat', 'hover:bg-blue-700', 'cursor-pointer');
            voteButton.classList.add('bg-gray-400', 'cursor-not-allowed');
            voteButton.textContent = 'Pilih Calon Favorit';
            voteButton.nextElementSibling.textContent = 'Silakan pilih salah satu calon terlebih dahulu';

            // Close all panels
            document.querySelectorAll('[id^="caketos-"]').forEach(p => {
                p.style.right = '';
            });
        }
    </script>
</body>

</html>