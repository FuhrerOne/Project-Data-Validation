<?php
function err($errors, $k) {
    if (isset($errors[$k])) {
        return "<small style='color:red'>{$errors[$k]}</small>";
    }
    return "";
}

$daftarGender=["Laki-laki", "Perempuan", "Attack Helicopter"];
$daftarSemester=["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18"];
$daftarLomba=["Web Development", "Game Development", "Data Science", "Bounty Hunter", "Breaching"];

$errors=[];
$sukses=false;
$nama=$email=$hp=$umur=$github=$semester=$gender="";
$lomba=[];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? "");
    $email    = trim($_POST['email'] ?? "");
    $hp       = trim($_POST['hp'] ?? "");
    $umur     = trim($_POST['umur'] ?? "");
    $github   = trim($_POST['github'] ?? "");
    $semester = $_POST['semester'] ?? "";
    $gender   = $_POST['gender'] ?? "";
    $lomba    = $_POST['lomba'] ?? [];
    if (!is_array($lomba)) {
        $lomba = [];
    }

    if ($nama === "") {
        $errors['nama'] = "Nama wajib diisi.";
    } elseif (strlen($nama) < 1 || strlen($nama) > 100) {
        $errors['nama'] = "Nama harus 1-100 karakter.";
    } elseif (!preg_match("/^[\p{L} .'-]+$/u", $nama)) {
        $errors['nama'] = "Nama hanya boleh huruf, spasi, titik, apostrof, dan strip.";
    }

    if ($email === "") {
        $errors['email'] = "Email wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Format email tidak valid.";
    }


    $opsiHp = ["options" => ["regexp" => "/^08[0-9]{8,11}$/"]];
    if ($hp === "") {
        $errors['hp'] = "Nomor HP wajib diisi.";
    } elseif (filter_var($hp, FILTER_VALIDATE_REGEXP, $opsiHp) === false) {
        $errors['hp'] = "Nomor HP harus diawali 08 dan 10-13 digit angka.";
    }

    $opsi = ["options" => ["min_range" => 17, "max_range" => 30]];
    if (filter_var($umur, FILTER_VALIDATE_INT, $opsi) === false) {
        $errors['umur'] = "Umur harus angka 17-30.";
    }

    if ($github === "") {
        $errors['github'] = "URL profil GitHub wajib diisi.";
    } elseif (!filter_var($github, FILTER_VALIDATE_URL)
        || !in_array(parse_url($github, PHP_URL_SCHEME), ["http", "https"], true)) {
        $errors['github'] = "URL tidak valid (contoh: https://github.com/namamu).";
    }

    // Gender: radio (satu pilihan)
    if ($gender === "") {
        $errors['gender'] = "Pilih gender bawaan anda dari lahir.";
    } elseif (!in_array($gender, $daftarGender, true)) {
        $errors['gender'] = "Pilihan gender tidak tersedia.";
    }

    if (!in_array($semester, $daftarSemester, true)) {
        $errors['semester'] = "Pilih semester yang tersedia.";
    }

    // Tema lomba: checkbox (boleh lebih dari satu)
    if (count($lomba) === 0) {
        $errors['lomba'] = "Pilih minimal satu tema lomba.";
    } else {
        foreach ($lomba as $l) {
            if (!in_array($l, $daftarLomba, true)) {
                $errors['lomba'] = "Ada pilihan tema lomba yang tidak tersedia.";
                break;
            }
        }
    }

    $sukses = empty($errors);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Registrasi Lomba Coding</title>
</head>
<body>
  <h2>Registrasi Lomba Coding</h2>

  <?php if ($sukses): ?>
    <p style="color:green">Berhasil, <?= htmlspecialchars($nama) ?>! Pendaftaran Anda sudah kami terima.</p>
    <ul>
      <li>Email: <?= htmlspecialchars($email) ?></li>
      <li>Nomor HP: <?= htmlspecialchars($hp) ?></li>
      <li>Umur: <?= htmlspecialchars($umur) ?></li>
      <li>Profil GitHub: <?= htmlspecialchars($github) ?></li>
      <li>Gender: <?= htmlspecialchars($gender) ?></li>
      <li>Semester: <?= htmlspecialchars($semester) ?></li>
      <li>Tema: <?= htmlspecialchars(implode(", ", $lomba)) ?></li>
    </ul>
  <?php endif; ?>

  <form method="post" action="" novalidate>
    Nama:<br>
    <input name="nama" value="<?= htmlspecialchars($nama) ?>">
    <?= err($errors, 'nama') ?><br><br>

    Email:<br>
    <input name="email" value="<?= htmlspecialchars($email) ?>">
    <?= err($errors, 'email') ?><br><br>

    Nomor HP:<br>
    <input name="hp" value="<?= htmlspecialchars($hp) ?>">
    <?= err($errors, 'hp') ?><br><br>

    Umur:<br>
    <input name="umur" value="<?= htmlspecialchars($umur) ?>">
    <?= err($errors, 'umur') ?><br><br>

    URL profil GitHub:<br>
    <input name="github" value="<?= htmlspecialchars($github) ?>">
    <?= err($errors, 'github') ?><br><br>

    Gender:<br>
    <?php foreach ($daftarGender as $g): ?>
      <label>
        <input type="radio" name="gender" value="<?= htmlspecialchars($g) ?>"
          <?= $gender === $g ? "checked" : "" ?>>
        <?= htmlspecialchars($g) ?>
      </label>
    <?php endforeach; ?>
    <?= err($errors, 'gender') ?><br><br>

    Semester:<br>
    <select name="semester">
      <option value="">-- Pilih --</option>
      <?php foreach ($daftarSemester as $s): ?>
        <option value="<?= $s ?>" <?= $semester === $s ? "selected" : "" ?>>Semester <?= $s ?></option>
      <?php endforeach; ?>
    </select>
    <?= err($errors, 'semester') ?><br><br>

    Tema lomba:<br>
    <?php foreach ($daftarLomba as $t): ?>
      <label>
        <input type="checkbox" name="lomba[]" value="<?= htmlspecialchars($t) ?>"
          <?= in_array($t, $lomba, true) ? "checked" : "" ?>>
        <?= htmlspecialchars($t) ?>
      </label>
    <?php endforeach; ?>
    <?= err($errors, 'lomba') ?><br><br>

    <button type="submit">Daftar</button>
  </form>
</body>
</html>