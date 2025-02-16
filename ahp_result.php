<!-- ahp_result.php -->
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['ahp_results']) || !isset($_SESSION['ahp_details'])) {
    header("Location: compare.php");
    exit();
}

$results = $_SESSION['ahp_results'];
$details = $_SESSION['ahp_details'];

include 'includes/header.php';
?>

<div class="container mt-4">
    <h2 class="text-center mb-4">AHP Calculation Results</h2>

    <h3>Pairwise Matrix</h3>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th></th>
                <?php for ($i = 1; $i <= 6; $i++) echo "<th>C$i</th>"; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($details['pairwiseMatrix'] as $rowIndex => $row): ?>
                <tr>
                    <th>C<?php echo $rowIndex + 1; ?></th>
                    <?php foreach ($row as $value): ?>
                        <td><?php echo round($value, 5); ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Normalized Matrix & Priorities</h3>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th></th>
                <?php for ($i = 1; $i <= 6; $i++) echo "<th>C$i</th>"; ?>
                <th>Sum</th>
                <th>Priority</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($details['normalizedMatrix'] as $rowIndex => $row): ?>
                <tr>
                    <th>C<?php echo $rowIndex + 1; ?></th>
                    <?php foreach ($row as $value): ?>
                        <td><?php echo round($value, 5); ?></td>
                    <?php endforeach; ?>
                    <td><?php echo round(array_sum($row), 5); ?></td>
                    <td><?php echo round($details['priorities'][$rowIndex], 5); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Consistency Ratio</h3>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Kriteria</th>
                <th>Jumlah</th>
                <th>Prioritas</th>
                <th>Jumlah / Prioritas</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($details['weightedSums'] as $index => $value): ?>
                <tr>
                    <td>C<?php echo $index + 1; ?></td>
                    <td><?php echo round($value, 5); ?></td>
                    <td><?php echo round($details['priorities'][$index], 5); ?></td>
                    <td><?php echo round($details['lambdaValues'][$index], 5); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Consistency Index and Ratio</h3>
    <table class="table table-bordered table-striped">
        <tr>
            <td>λ max</td>
            <td><?php echo round($details['lambdaMax'], 5); ?></td>
        </tr>
        <tr>
            <td>CI (Consistency Index)</td>
            <td><?php echo round($details['CI'], 5); ?></td>
        </tr>
        <tr>
            <td>CR (Consistency Ratio)</td>
            <td><?php echo round($details['CR'], 5); ?></td>
        </tr>
    </table>

    <h3>Final Scores</h3>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Alternatif</th>
                <th>Skor Akhir</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $alt => $score): ?>
                <tr>
                    <td><?php echo htmlspecialchars($alt); ?></td>
                    <td><?php echo round($score['score'], 5); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
