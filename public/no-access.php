<?php

$x = $_GET['x']
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css" />
    <title>Pilketos | Error</title>
</head>

<body class="p-6">
    <script>
        if (window.innerWidth >= 1024) {
            window.location.href = "<?= $x ?>";
        }
    </script>
    <h1 class="font-montserrat font-bold text-4xl mb-6">Error!</h1>
    <p class="font-montserrat text-2xl text-neutral-700 mb-3">halaman admin hanya dapat diakses di layar laptop atau dekstop!</p>
    <p class="font-montserrat text-xl text-neutral-500">Atau gunakan mode-desktop jika ingin membuka lewat hp.</p>
</body>

</html>