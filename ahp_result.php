<?php
session_start();

if (!isset($_SESSION['ahp_results']) || !isset($_SESSION['ahp_details'])) {
    header("Location: compare.php");
    exit();
}

$results = $_SESSION['ahp_results'];
$details = $_SESSION['ahp_details'];

include 'includes/header.php';

// Sorting results for ranking
$ranked = $results;
uasort($ranked, function($a, $b) {
    return $b['score'] <=> $a['score'];
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AHP Product Ranking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        h1, h2, h3 {
            font-family: 'Playfair Display', serif;
        }
        .card {
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border: none;
            transition: all 0.3s ease;
        }
        .card:hover {
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }
        .btn-custom {
            background-color: #333;
            color: #fff;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            background-color: #555;
            color: #fff;
        }
        .table {
            border-color: #ddd;
        }
        .table thead th {
            border-bottom: 2px solid #333;
        }
        .bg-custom {
            background-color: #333;
            color: #fff;
        }
        .text-custom {
            color: #333;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center mb-5 display-4">AHP Product Ranking Results</h1>

    <!-- Ranking Section -->
    <div class="card mb-5">
        <div class="card-header bg-custom">
            <h2 class="mb-0 h3"><i class="fas fa-trophy"></i> Product Ranking</h2>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Product</th>
                        <th>Score</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $rank = 1;
                    foreach ($ranked as $id => $data): ?>
                        <tr>
                            <td class="align-middle"><?php echo $rank++; ?></td>
                            <td class="align-middle"><?php echo htmlspecialchars($data['name']); ?></td>
                            <td class="align-middle"><?php echo number_format($data['score'], 4); ?></td>
                            <td>
                                <a href="detail.php?id=<?php echo urlencode($id); ?>" class="btn btn-custom btn-sm me-2">
                                    <i class="fas fa-info-circle"></i> Details
                                </a>
                                <form method="POST" action="wishlist.php" style="display:inline;">
                                    <input type="hidden" name="product_id" value="<?php echo urlencode($id); ?>">
                                    <button type="submit" name="add_to_wishlist" class="btn btn-custom btn-sm">
                                        <i class="fas fa-heart"></i> Wishlist
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- AHP Details Section -->
    <div class="card">
        <div class="card-header bg-custom">
            <h2 class="mb-0 h3">
                <button class="btn btn-link text-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAHP" aria-expanded="false" aria-controls="collapseAHP">
                    <i class="fas fa-calculator"></i> AHP Calculation Details
                </button>
            </h2>
        </div>
        <div id="collapseAHP" class="collapse">
            <div class="card-body">
                <!-- Pairwise Comparison Matrix -->
                <h3 class="mt-4 h4"><i class="fas fa-table"></i> Pairwise Comparison Matrix</h3>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="bg-custom">
                            <tr>
                                <th></th>
                                <?php for ($i = 1; $i <= 6; $i++) echo "<th>C$i</th>"; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $columnSums = array_fill(0, 6, 0);
                            foreach ($details['pairwiseMatrix'] as $rowIndex => $row): ?>
                                <tr>
                                    <th>C<?php echo $rowIndex + 1; ?></th>
                                    <?php foreach ($row as $colIndex => $value): ?>
                                        <td><?php echo round($value, 5); ?></td>
                                        <?php $columnSums[$colIndex] += $value; ?>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Column Sums -->
                <h3 class="mt-4 h4"><i class="fas fa-sum"></i> Column Sums</h3>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="bg-custom">
                            <tr>
                                <th>Criteria</th>
                                <?php for ($i = 1; $i <= 6; $i++) echo "<th>C$i</th>"; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>Sum</th>
                                <?php foreach ($columnSums as $value): ?>
                                    <td><?php echo round($value, 5); ?></td>
                                <?php endforeach; ?>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Normalized Matrix & Priority Weights -->
                <h3 class="mt-4 h4"><i class="fas fa-balance-scale"></i> Normalized Matrix & Priority Weights</h3>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="bg-custom">
                            <tr>
                                <th></th>
                                <?php for ($i = 1; $i <= 6; $i++) echo "<th>C$i</th>"; ?>
                                <th>Sum</th>
                                <th>Priority Weight</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($details['normalizedMatrix'] as $rowIndex => $row): ?>
                                <tr>
                                    <th>C<?php echo $rowIndex + 1; ?></th>
                                    <?php foreach ($row as $value) echo "<td>" . round($value, 5) . "</td>"; ?>
                                    <td><?php echo round(array_sum($row), 5); ?></td>
                                    <td><strong><?php echo round($details['priorities'][$rowIndex], 5); ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Consistency Ratio -->
                <h3 class="mt-4 h4"><i class="fas fa-check-circle"></i> Consistency Ratio</h3>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="bg-custom">
                            <tr>
                                <th>Criteria</th>
                                <th>Sum</th>
                                <th>Priority</th>
                                <th>Sum / Priority</th>
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
                </div>

                <!-- Consistency Values -->
                <h3 class="mt-4 h4"><i class="fas fa-chart-line"></i> Consistency Values</h3>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <tr>
                            <td class="bg-light">λ max</td>
                            <td><?php echo round($details['lambdaMax'], 5); ?></td>
                        </tr>
                        <tr>
                            <td class="bg-light">CI (Consistency Index)</td>
                            <td><?php echo round($details['CI'], 5); ?></td>
                        </tr>
                        <tr>
                            <td class="bg-light">CR (Consistency Ratio)</td>
                            <td><?php echo round($details['CR'], 5); ?></td>
                        </tr>
                    </table>
                </div>
                <p class="mt-3">
                    <?php if ($details['CR'] < 0.1): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle"></i> Consistency is acceptable (CR < 0.1)
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> Consistency is not valid (CR ≥ 0.1), please revise the weights!
                        </div>
                    <?php endif; ?>
                </p>

                <!-- Final Scores Calculation -->
                <h3 class="mt-4 h4"><i class="fas fa-calculator"></i> Final Scores Calculation</h3>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="bg-custom">
                            <tr>
                                <th>Alternative</th>
                                <th>Price (C1)</th>
                                <th>Display (C2)</th>
                                <th>CPU (C3)</th>
                                <th>GPU (C4)</th>
                                <th>Storage (C5)</th>
                                <th>RAM (C6)</th>
                                <th>Total Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $id => $data): ?>
                                <tr>
                                    <th><?php echo htmlspecialchars($data['name']); ?></th>
                                    <td><?php echo number_format($data['details']['harga'], 4); ?></td>
                                    <td><?php echo number_format($data['details']['display'], 4); ?></td>
                                    <td><?php echo number_format($data['details']['cpu'], 4); ?></td>
                                    <td><?php echo number_format($data['details']['gpu'], 4); ?></td>
                                    <td><?php echo number_format($data['details']['storage'], 4); ?></td>
                                    <td><?php echo number_format($data['details']['ram'], 4); ?></td>
                                    <td><strong><?php echo number_format($data['score'], 4); ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include 'includes/footer.php'; ?>