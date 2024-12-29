<?php
include "../../service/database.php";
session_start();

// Logout handler
if (isset($_POST["logout"])) {
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit();
}

// Periksa jika session untuk npm_mhs sudah ada
if (!isset($_SESSION['npm_mhs'])) {
    echo "NPM Mahasiswa tidak ditemukan di session.";
    exit;
}

// Fetch mata kuliah berdasarkan semester
try {
    $query = $db->prepare("SELECT kode_mk, nama_mk, semester, sks, status FROM data_mk");
    $query->execute();
    $result = $query->get_result();

    $mataKuliah = [];
    while ($row = $result->fetch_assoc()) {
        $mataKuliah[$row['semester']][] = $row;
    }

    $dataKhs = [];
    $dataMk = [];
    $dataPrasyarat = [];
} catch (Exception $e) {
    die("Error fetching mata kuliah: " . $e->getMessage());
}

if (isset($_GET['semester_khs']) && $_GET['semester_khs'] && isset($_GET['npm_mhs']) && $_GET['npm_mhs']) {
    $queryKhs = $db->query("SELECT 
        data_khs.id_khs AS id_khs,
        data_khs.npm_mahasiswa AS npm,
        data_khs.kode_mk AS kode_mk,
        data_mk.nama_mk AS nama_mk,
        data_mk.sks AS sks_mk,
        data_khs.semester AS semester_khs,
        data_mk.semester AS semester_mk,
        data_mk.status AS status_mk,
        data_khs.nilai AS nilai_khs
        FROM data_khs
        LEFT JOIN data_mk
        ON data_khs.kode_mk = data_mk.kode_mk
        WHERE data_khs.semester=" . $_GET['semester_khs'] . "
        AND data_khs.npm_mahasiswa=" . $_GET['npm_mhs'] . "
    ");

    if ($queryKhs->num_rows > 0) {
        while ($row = $queryKhs->fetch_assoc()) {
            array_push($dataKhs, $row);
        }
    }
}

if (isset($_GET['semester_mk']) && $_GET['semester_mk']) {
    $queryMk = $db->query("SELECT * FROM data_mk
        WHERE semester=" . $_GET['semester_mk'] . "
    ");

    if ($queryMk->num_rows > 0) {
        while ($row = $queryMk->fetch_assoc()) {
            array_push($dataMk, $row);
        }
    }
}

if (isset($_GET['cek_prasyarat']) && $_GET['cek_prasyarat']) {
    $queryPrasyarat = $db->query("SELECT * FROM data_mk
        LEFT JOIN mk_prasyarat ON mk_prasyarat.kode_mk = data_mk.kode_mk
        LEFT JOIN data_khs ON data_khs.kode_mk = mk_prasyarat.kode_mk_prasyarat
        WHERE data_khs.id_khs='" . $_GET['cek_prasyarat'] . "'
    ");

    if ($queryPrasyarat->num_rows > 0) {
        while ($row = $queryPrasyarat->fetch_assoc()) {
            array_push($dataPrasyarat, $row);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen KRS</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <script>
        // Filter mata kuliah berdasarkan semester
        function filterMataKuliah() {
            const semesterMK = document.getElementById("semester").value;
            const mataKuliahSelect = document.getElementById("kode_mk");

            // Clear current options
            mataKuliahSelect.innerHTML = "<option value=''>-- Pilih Mata Kuliah --</option>";

            // Mata kuliah berdasarkan semester
            const mataKuliahData = <?php echo json_encode($mataKuliah); ?>;
            if (semesterMK) {
                const filteredMataKuliah = mataKuliahData[semesterMK] || [];
                filteredMataKuliah.forEach(mk => {
                    const option = document.createElement("option");
                    option.value = mk.kode_mk;
                    option.text = mk.nama_mk;
                    mataKuliahSelect.appendChild(option);
                });
            }
        }

        // Tambahkan mata kuliah ke tabel
        function tambahMataKuliah() {
            const kodeMKSelect = document.getElementById('kode_mk');
            const kodeMK = kodeMKSelect.value;
            const namaMK = kodeMKSelect.options[kodeMKSelect.selectedIndex].text;
            const semesterMK = document.getElementById('semester_mk').value;
            const semesterKHS = document.getElementById('semester_khs').value;
            const npm = document.getElementById('npm_mhs').value;
            const nilaiSelect = document.getElementById('nilai');
            const nilai = nilaiSelect.value;

            // Validasi input
            if (!kodeMK || !semesterMK || !nilai) {
                alert('Semua field harus diisi!');
                return;
            }

            // Dapatkan status mata kuliah dari data yang ada
            const mataKuliahData = <?php echo json_encode($mataKuliah); ?>;

            // Tambah baris baru ke tabel
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td class="py-2 px-4 border-b">${kodeMK}</td>
                <td class="py-2 px-4 border-b">${namaMK}</td>
                <td class="py-2 px-4 border-b">3</td>
                <td class="py-2 px-4 border-b">${semesterMK}</td>
                <td class="py-2 px-4 border-b">${status}</td>
                <td class="py-2 px-4 border-b">${nilai}</td>
                <td class="py-2 px-4 border-b">
                    <button class="text-red-500 hover:text-red-700" onclick="hapusBaris(this)">Hapus</button>
                </td>
            `;
            document.querySelector('table tbody').appendChild(newRow);

            // Submit form
            document.getElementById('npm').value = npm;
            document.getElementById('semester').value = semesterKHS;
            document.getElementById('kode-mk').value = kodeMK;
            document.getElementById('nilai-mk').value = nilai;
            document.getElementById('submit-form').submit();
        }
    </script>
    <style>
        .menu-box {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .menu-box:hover {
            transform: translateY(-5px);
            /* Lift the box */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            /* Shadow effect */
        }

        .menu-box:active {
            transform: translateY(2px);
            /* Press the box */
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        /* Sidebar button effects */
        nav a {
            transition: background-color 0.3s, transform 0.2s;
        }

        nav a:hover {
            background-color: #1D4ED8;
            /* Hover color */
            transform: translateX(10px);
            /* Slide effect */
        }

        nav a:active {
            background-color: #2563EB;
            /* Active button color */
            transform: scale(0.98);
            /* Button press effect */
        }

        nav a:focus {
            outline: none;
            /* Remove default outline */
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.5);
            /* Blue shadow */
        }

        nav a.bg-blue-700 {
            background-color: #3B82F6;
            /* Active button background */
        }

        nav button:hover {
            background-color: #1D4ED8;
            /* Hover color for logout button */
            transform: translateX(10px);
            /* Slide effect */
        }
    </style>
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <!-- Sidebar -->
    <div class="flex">
        <div class="bg-blue-600 w-64 min-h-screen text-white p-4">
            <!-- Logo -->
            <div class="flex items-center mb-8">
                <img src="logo.png" alt="Logo" class="w-13 h-12 rounded-full mr-3">
                <div>
                    <h2 class="font-bold">KRS Unkhair</h2>
                    <p class="text-sm text-blue-200">Sistem Manajemen KRS</p>
                </div>
            </div>
            <nav>
                <a href="dashboardMahasiswa.php" class="block py-2 px-4 mb-2 hover:bg-blue-700 rounded flex items-center">
                    <i class="fas fa-home mr-2"></i> Beranda
                </a>
                <a href="pengajuanKHS.php" class="block py-2 px-4 mb-2 hover:bg-blue-700 rounded flex items-center">
                    <i class="fas fa-edit mr-2"></i> Pengajuan KHS
                </a>
                <a href="profile.php" class="block py-2 px-4 mb-2 hover:bg-blue-700 rounded flex items-center">
                    <i class="fas fa-user mr-2"></i> Profile
                </a>
                <!-- Logout button -->
                <form method="POST" action="">
                    <button type="submit" name="logout" class="block py-2 px-4 mb-2 hover:bg-blue-700 rounded flex items-center w-full text-left text-white">
                        <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                    </button>
                </form>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-6">
            <section id="beranda" class="bg-white p-6 rounded-lg shadow-md mb-6">
                <h1 class="text-2xl font-bold mb-4">Pengajuan KHS</h1>
                <!-- Form Tambah KHS -->
                <div class="space-y-4">
                    <form id="filter-form" action="" method="GET" class="space-y-4">
                        <!-- Semester KHS -->
                        <div>
                            <label for="semester_khs" class="block font-medium">Semester KHS</label>
                            <select id="semester_khs" name="semester_khs" class="w-full p-2 border border-gray-300 rounded" required>
                                <option value="">-- Pilih Semester --</option>
                                <?php for ($i = 1; $i <= 8; $i++): ?>
                                    <option value="<?= $i ?>" <?= isset($_GET['semester_khs']) && $_GET['semester_khs'] == $i ? 'selected' : '' ?>>Semester <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- NPM Mahasiswa -->
                        <div>
                            <label for="npm_mhs" class="block font-medium">NPM Mahasiswa</label>
                            <input type="text" id="npm_mhs" name="npm_mhs" class="w-full p-2 border border-gray-300 rounded" value="<?php echo $_SESSION['npm_mhs']; ?>" readonly required>
                        </div>

                        <!-- Semester Mata Kuliah -->
                        <div>
                            <label for="semester_mk" class="block font-medium">Semester Mata Kuliah</label>
                            <select id="semester_mk" name="semester_mk" class="w-full p-2 border border-gray-300 rounded" onchange="filterMataKuliah()" required>
                                <option value="">-- Pilih Semester --</option>
                                <?php for ($i = 1; $i <= 8; $i++): ?>
                                    <option value="<?= $i ?>" <?= isset($_GET['semester_mk']) && $_GET['semester_mk'] == $i ? 'selected' : '' ?>>Semester <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- Mata Kuliah -->
                        <div>
                            <label for="kode_mk" class="block font-medium">Mata Kuliah</label>
                            <select id="kode_mk" name="kode_mk" class="w-full p-2 border border-gray-300 rounded" required>
                                <option value="">-- Pilih Mata Kuliah --</option>
                                <?php if ($dataMk) : ?>
                                    <?php foreach ($dataMk as $mk) : ?>
                                        <option value="<?= $mk['kode_mk'] ?>" <?= isset($_GET['kode_mk']) && $_GET['kode_mk'] == $mk['kode_mk'] ? 'selected' : '' ?>><?= $mk['nama_mk'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <!-- Nilai -->
                        <div>
                            <label for="nilai" class="block font-medium">Nilai</label>
                            <select id="nilai" name="nilai" class="w-full p-2 border border-gray-300 rounded" required>
                                <option value="">-- Pilih Nilai --</option>
                                <option value="A" <?= isset($_GET['nilai']) && $_GET['nilai'] == 'A' ? 'selected' : '' ?>>A</option>
                                <option value="AB" <?= isset($_GET['nilai']) && $_GET['nilai'] == 'AB' ? 'selected' : '' ?>>AB</option>
                                <option value="B" <?= isset($_GET['nilai']) && $_GET['nilai'] == 'B' ? 'selected' : '' ?>>B</option>
                                <option value="BC" <?= isset($_GET['nilai']) && $_GET['nilai'] == 'BC' ? 'selected' : '' ?>>BC</option>
                                <option value="C" <?= isset($_GET['nilai']) && $_GET['nilai'] == 'C' ? 'selected' : '' ?>>C</option>
                                <option value="D" <?= isset($_GET['nilai']) && $_GET['nilai'] == 'D' ? 'selected' : '' ?>>D</option>
                                <option value="E" <?= isset($_GET['nilai']) && $_GET['nilai'] == 'E' ? 'selected' : '' ?>>E</option>
                            </select>
                        </div>

                        <!-- Tombol Tambah -->
                        <button type="button" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-700" onclick="tambahMataKuliah()">Tambah</button>
                    </form>

                    <!-- Hidden Inputs to Send Course Data -->
                    <div id="course_data"></div>

                    <div class="bg-white shadow rounded p-4 mb-6">
                        <h2 class="font-semibold text-lg mb-4">Daftar Mata Kuliah yang Telah Diambil</h2>
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="py-2 px-4 border-b">Kode</th>
                                    <th class="py-2 px-4 border-b">Mata Kuliah</th>
                                    <th class="py-2 px-4 border-b">SKS</th>
                                    <th class="py-2 px-4 border-b">Semester</th>
                                    <th class="py-2 px-4 border-b">Status</th>
                                    <th class="py-2 px-4 border-b">Nilai</th>
                                    <th class="py-2 px-4 border-b">Aksi</th>
                                    <th class="py-2 px-4 border-b">Cek MK</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($dataKhs) : ?>
                                    <?php foreach ($dataKhs as $khs) : ?>
                                        <tr>
                                            <td class="py-2 px-4 border-b"><?= $khs['kode_mk'] ?></td>
                                            <td class="py-2 px-4 border-b"><?= $khs['nama_mk'] ?></td>
                                            <td class="py-2 px-4 border-b"><?= $khs['sks_mk'] ?></td>
                                            <td class="py-2 px-4 border-b"><?= $khs['semester_mk'] ?></td>
                                            <td class="py-2 px-4 border-b"><?= $khs['status_mk'] ?></td>
                                            <td class="py-2 px-4 border-b"><?= $khs['nilai_khs'] ?></td>
                                            <td class="py-2 px-4 border-b">
                                                <form action="hapusKHS.php" method="POST">
                                                    <button name="hapus" value="<?= $khs['id_khs'] ?>" class="text-red-500 hover:text-red-700">Hapus</button>
                                                </form>
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                <a href="?semester_khs=<?= $khs['semester_khs'] ?>&npm_mhs=<?= $khs['npm'] ?>&cek_prasyarat=<?= $khs['id_khs'] ?>" class="text-blue-500 hover:text-blue-700">Cek</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <!-- Tabel akan diperbarui oleh JavaScript -->
                            </tbody>
                        </table>
                        <!-- Tombol Submit KHS -->
                        <form id="submit-form" method="POST" action="simpanKHS.php" class="space-y-4">
                            <input type="hidden" name="npm" id="npm" />
                            <input type="hidden" name="semester" id="semester" />
                            <input type="hidden" name="kode-mk" id="kode-mk" />
                            <input type="hidden" name="nilai-mk" id="nilai-mk" />
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <?php if (isset($_GET['cek_prasyarat']) && $_GET['cek_prasyarat']): ?>
        <!-- Modal -->
        <div id="myModal" class="fixed top-0 left-0 right-0 bottom-0 inset-0 flex items-center justify-center bg-black bg-opacity-50 p-40">
            <div class="bg-white rounded-lg shadow-lg w-full">
                <div class="p-4 border-b">
                    <h2 class="text-lg font-semibold">Modal Title</h2>
                </div>
                <div class="p-4">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="py-2 px-4 border-b">Kode</th>
                                <th class="py-2 px-4 border-b">Mata Kuliah</th>
                                <th class="py-2 px-4 border-b">Semester</th>
                                <th class="py-2 px-4 border-b">SKS</th>
                                <th class="py-2 px-4 border-b">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($dataPrasyarat) : ?>
                                <?php foreach ($dataPrasyarat as $mk) : ?>
                                    <tr class="<?= $mk['nilai_minimal'] < $mk['nilai'] ? 'bg-red-300' : 'bg-green-300'; ?>">
                                        <td class="py-2 px-4 border-b"><?= $mk['kode_mk'] ?></td>
                                        <td class="py-2 px-4 border-b"><?= $mk['nama_mk'] ?></td>
                                        <td class="py-2 px-4 border-b"><?= $mk['semester'] ?></td>
                                        <td class="py-2 px-4 border-b"><?= $mk['sks'] ?></td>
                                        <td class="py-2 px-4 border-b"><?= $mk['status'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <!-- Tabel akan diperbarui oleh JavaScript -->
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t flex justify-end">
                    <button id="closeModal" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                        Close
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>
</body>

<script>
    const inputForm = document.getElementById('filter-form');
    inputForm.querySelectorAll("select").forEach((select) => {
        select.addEventListener('change', function() {
            inputForm.submit();
        });
    });
</script>
<script>
    // Get elements
    const closeModalBtn = document.getElementById('closeModal');
    const modal = document.getElementById('myModal');

    // Close modal function
    closeModalBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    // Close modal when clicking outside of it
    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    });
</script>

</html>