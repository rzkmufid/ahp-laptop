<?php
echo "<html><head><style>
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
      </style></head><body>";

      $criteria_comparison = [
    ["C1", "C2", 3],
    ["C1", "C3", 2],
    ["C1", "C4", 2],
    ["C1", "C5", 5],
    ["C1", "C6", 5],
    ["C2", "C3", 3],
    ["C2", "C4", 2],
    ["C2", "C5", 2],
    ["C2", "C6", 3],
    ["C3", "C4", 2],
    ["C3", "C5", 2],
    ["C3", "C6", 3],
    ["C4", "C5", 5],
    ["C4", "C6", 3],
    ["C5", "C6", 2],
];

// 🔹 Tampilkan Perbandingan Kriteria
echo "<h2>Perbandingan Kriteria</h2><table>
        <tr><th>Kriteria 1</th><th>Nilai</th><th>Kriteria 2</th></tr>";
foreach ($criteria_comparison as $comparison) {
    echo "<tr><td>{$comparison[0]}</td><td>{$comparison[2]}</td><td>{$comparison[1]}</td></tr>";
}
echo "</table>";

// 🔹 Menyiapkan Matriks Perbandingan Berpasangan
$total_criteria = 6;
$pairwiseMatrix = array_fill(0, $total_criteria, array_fill(0, $total_criteria, 1));

// 🔹 Memasukkan data dari $criteria_comparison ke dalam matriks
foreach ($criteria_comparison as $comparison) {
    $row = intval(substr($comparison[0], 1)) - 1;
    $col = intval(substr($comparison[1], 1)) - 1;
    $value = floatval($comparison[2]);

    $pairwiseMatrix[$row][$col] = $value;
    $pairwiseMatrix[$col][$row] = 1 / $value;
}

// 🔹 Tampilkan Matriks Perbandingan Berpasangan
echo "<h2>1️⃣ Matriks Perbandingan Berpasangan</h2><table><tr><th>Kriteria</th>";
for ($i = 1; $i <= $total_criteria; $i++) echo "<th>C$i</th>";
echo "</tr>";
foreach ($pairwiseMatrix as $rowIndex => $row) {
    echo "<tr><th>C" . ($rowIndex + 1) . "</th>";
    foreach ($row as $value) {
        echo "<td>" . round($value, 5) . "</td>";
    }
    echo "</tr>";
}
echo "</table>";

// 2️⃣ Hitung jumlah tiap kolom
$columnSums = array_fill(0, 6, 0);
foreach ($pairwiseMatrix as $row) {
    foreach ($row as $colIndex => $value) {
        $columnSums[$colIndex] += $value;
    }
}

// Tampilkan jumlah kolom
echo "<h2>2️⃣ Jumlah Kolom</h2><table><tr><th>Kriteria</th>";
for ($i = 1; $i <= 6; $i++) echo "<th>C$i</th>";
echo "</tr><tr><th>Jumlah</th>";
foreach ($columnSums as $value) {
    echo "<td>" . round($value, 5) . "</td>";
}
echo "</tr></table>";

// 3️⃣ Normalisasi Matriks
$normalizedMatrix = [];
$priorities = array_fill(0, 6, 0);
foreach ($pairwiseMatrix as $rowIndex => $row) {
    foreach ($row as $colIndex => $value) {
        $normalizedMatrix[$rowIndex][$colIndex] = $value / $columnSums[$colIndex];
        $priorities[$rowIndex] += $normalizedMatrix[$rowIndex][$colIndex];
    }
}

// Hitung rata-rata bobot prioritas
$totalCriteria = count($priorities);
foreach ($priorities as $index => $value) {
    $priorities[$index] = $value / $totalCriteria;
}

// Tampilkan Normalisasi Matriks
echo "<h2>3️⃣ Matriks Normalisasi & Bobot Prioritas</h2><table><tr><th>Kriteria</th>";
for ($i = 1; $i <= 6; $i++) echo "<th>C$i</th>";
echo "<th>Jumlah</th><th>Bobot Prioritas</th></tr>";

foreach ($normalizedMatrix as $rowIndex => $row) {
    echo "<tr><th>C" . ($rowIndex + 1) . "</th>";
    foreach ($row as $value) {
        echo "<td>" . round($value, 5) . "</td>";
    }
    echo "<td>" . round(array_sum($row), 5) . "</td>";
    echo "<td><strong>" . round($priorities[$rowIndex], 5) . "</strong></td></tr>";
}
echo "</table>";

// 4️⃣ Menghitung Rasio Konsistensi
$weightedSums = array_fill(0, 6, 0);
foreach ($pairwiseMatrix as $rowIndex => $row) {
    foreach ($row as $colIndex => $value) {
        $weightedSums[$rowIndex] += $value * $priorities[$colIndex];
    }
}

// Hitung jumlah per baris dibagi prioritas
$lambdaValues = [];
foreach ($weightedSums as $index => $value) {
    $lambdaValues[$index] = $value / $priorities[$index];
}

// Hitung λ max, CI, dan CR
$lambdaMax = array_sum($lambdaValues) / $totalCriteria;
$CI = ($lambdaMax - $totalCriteria) / ($totalCriteria - 1);
$RI = 1.24; // Nilai acuan Random Index untuk n=6
$CR = $CI / $RI;

// Tampilkan Rasio Konsistensi
echo "<h2>4️⃣ Rasio Konsistensi</h2><table>
        <tr><th>Kriteria</th><th>Jumlah</th><th>Prioritas</th><th>Jumlah / Prioritas</th></tr>";
foreach ($weightedSums as $index => $value) {
    echo "<tr><td>C" . ($index + 1) . "</td>
              <td>" . round($value, 5) . "</td>
              <td>" . round($priorities[$index], 5) . "</td>
              <td>" . round($lambdaValues[$index], 5) . "</td>
          </tr>";
}
echo "</table>";

echo "<h3>Nilai Konsistensi</h3>";
echo "<table>
        <tr><td>Jumlah (λ max)</td><td>" . round($lambdaMax, 5) . "</td></tr>
        <tr><td>CI (Consistency Index)</td><td>" . round($CI, 5) . "</td></tr>
        <tr><td>CR (Consistency Ratio)</td><td>" . round($CR, 5) . "</td></tr>
      </table>";

if ($CR < 0.1) {
    echo "<p><strong>✅ Rasio Konsistensi diterima (CR < 0.1)</strong></p>";
} else {
    echo "<p><strong>❌ Rasio Konsistensi tidak konsisten (CR >= 0.1), perbaiki bobot!</strong></p>";
}

// 5️⃣ Alternatif Laptop
$alternatives = [
    'A1' => [0.25, 0.5, 0.5, 0.1, 0.5, 0.5],
    'A2' => [0.75, 0.25, 1, 0.5, 0.5, 0.1],
    'A3' => [0.25, 0.25, 1, 0.25, 0.5, 0.1],
    'A4' => [1, 0.25, 1, 0.5, 0.5, 0.5],
    'A5' => [1, 0.25, 1, 0.75, 0.5, 0.5],
    'A6' => [0.25, 0.25, 0.75, 0.1, 0.5, 0.1],
    'A7' => [1, 0.75, 0.75, 0.1, 0.75, 0.5],
    'A8' => [0.1, 0.25, 0.5, 0.1, 0.5, 0.5],
    'A9' => [0.5, 0.25, 1, 0.1, 0.5, 0.5],
    'A10' => [0.5, 0.5, 0.5, 1, 0.5, 0.1],
    'A11' => [0.75, 1, 0.5, 1, 0.5, 0.5],
    'A12' => [0.25, 0.25, 1, 0.25, 0.5, 0.1],
    'A13' => [0.25, 0.25, 1, 0.25, 0.5, 0.1],
    'A14' => [0.5, 0.5, 1, 0.25, 0.5, 0.1],
    'A15' => [0.5, 0.25, 1, 0.25, 0.5, 0.25],
];

// 6️⃣ Hitung skor akhir setiap alternatif
$results = [];
echo "<h2>5️⃣ Perhitungan Skor Akhir Tiap Alternatif</h2><table><tr><th>Alternatif</th>";
for ($i = 1; $i <= 6; $i++) echo "<th>C$i</th>";
echo "<th>Total</th></tr>";

foreach ($alternatives as $alt => $values) {
    $score = 0;
    echo "<tr><th>$alt</th>";
    foreach ($values as $index => $value) {
        $weightedValue = $value * $priorities[$index];
        echo "<td>" . round($weightedValue, 5) . "</td>";
        $score += $weightedValue;
    }
    $results[$alt] = $score;
    echo "<td><strong>" . round($score, 5) . "</strong></td></tr>";
}
echo "</table>";

// 7️⃣ Urutkan hasil dari skor tertinggi ke terendah
arsort($results);

// Tampilkan Hasil Akhir
echo "<h2>6️⃣ Hasil Akhir Perhitungan AHP</h2><table><tr><th>Alternatif</th><th>Skor Akhir</th></tr>";
foreach ($results as $alt => $score) {
    echo "<tr><td>$alt</td><td>" . round($score, 5) . "</td></tr>";
}
echo "</table></body></html>";
?>
