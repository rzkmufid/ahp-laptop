<?php
session_start();
require_once 'config/database.php';
require_once 'classes/AHP.php';


// Initialize comparison session
if (!isset($_SESSION['compare_laptops'])) {
    $_SESSION['compare_laptops'] = [];
}

// Handle adding/removing laptops and other POST actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_to_compare'])) {
        $product_id = $_POST['product_id'];
        if (!in_array($product_id, $_SESSION['compare_laptops'])) {
            $_SESSION['compare_laptops'][] = $product_id;
        }
    }

    if (isset($_POST['remove_from_compare'])) {
        $product_id = $_POST['product_id'];
        $key = array_search($product_id, $_SESSION['compare_laptops']);
        if ($key !== false) {
            unset($_SESSION['compare_laptops'][$key]);
            $_SESSION['compare_laptops'] = array_values($_SESSION['compare_laptops']);
        }
    }

    if (isset($_POST['save_comparison'])) {
        $userCriteria = [];
        foreach ($_POST['comparison'] as $c1 => $row) {
            foreach ($row as $c2 => $value) {
                if (!empty($value) && $value != 'Select Value') {
                    $userCriteria[] = [$c1, $c2, floatval($value)];
                }
            }
        }
        if (!empty($userCriteria)) {
            $_SESSION['user_criteria'] = $userCriteria;
            $success_message = "Criteria comparison has been updated.";
        } else {
            unset($_SESSION['user_criteria']);
        }
    }

    if (isset($_POST['compare_now']) && count($_SESSION['compare_laptops']) >= 2) {
        try {
            $stmt = $pdo->prepare("SELECT p.id, p.name, aps.processor_score, aps.ram_score, aps.storage_score, aps.gpu_score, aps.display_score, aps.harga_score
                                   FROM products p
                                   JOIN ahp_scores aps ON p.id = aps.product_id
                                   WHERE p.id IN (" . str_repeat('?,', count($_SESSION['compare_laptops']) - 1) . "?)");
            $stmt->execute($_SESSION['compare_laptops']);
            $laptops = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $userCriteria = $_SESSION['user_criteria'] ?? null;
            $ahp = new AHP($userCriteria);
            $results = $ahp->compareLaptops($laptops);

            $_SESSION['ahp_results'] = $results;
            $_SESSION['ahp_details'] = $ahp->getCalculationDetails();

            header("Location: ahp_result.php");
            exit();
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Get selected laptops
$selected_laptops = [];
if (!empty($_SESSION['compare_laptops'])) {
    $stmt = $pdo->prepare("SELECT p.*, ls.processor, ls.processor_detail, ls.ram, ls.storage, ls.gpu, ls.display, ls.battery
                           FROM products p
                           LEFT JOIN laptop_specifications ls ON p.id = ls.product_id
                           WHERE p.id IN (" . str_repeat('?,', count($_SESSION['compare_laptops']) - 1) . "?)");
    $stmt->execute($_SESSION['compare_laptops']);
    $selected_laptops = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compare Laptops - GAPTECH STORE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        h1, h2, h3, h4, h5 {
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
        .btn-outline-custom {
            color: #333;
            border-color: #333;
        }
        .btn-outline-custom:hover {
            background-color: #333;
            color: #fff;
        }
        .table {
            border-color: #ddd;
        }
        .table thead th {
            border-bottom: 2px solid #333;
        }
        .bg-custom-light {
            background-color: #f1f3f5;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center mb-4">Compare Laptops</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h3 class="mb-0">
                <button class="btn btn-link text-white" type="button" data-bs-toggle="collapse" data-bs-target="#comparisonForm" aria-expanded="false" aria-controls="comparisonForm">
                    <i class="fas fa-cog"></i> Set Criteria Comparison (Optional)
                </button>
            </h3>
        </div>
        <div class="collapse" id="comparisonForm">
            <div class="card-body">
                <form method="POST" action="" id="comparisonFormSubmit">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Criteria 1</th>
                                <th>Comparison Value</th>
                                <th>Criteria 2</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $criteriaList = ["C1", "C2", "C3", "C4", "C5", "C6"];
                            $defaultComparison = [
                                ["C1", "C2", 3], ["C1", "C3", 2], ["C1", "C4", 2], ["C1", "C5", 5], ["C1", "C6", 5],
                                ["C2", "C3", 3], ["C2", "C4", 2], ["C2", "C5", 2], ["C2", "C6", 3],
                                ["C3", "C4", 2], ["C3", "C5", 2], ["C3", "C6", 3],
                                ["C4", "C5", 5], ["C4", "C6", 3], ["C5", "C6", 2]
                            ];
                            $comparisonValues = [1, 2, 3, 5];

                            for ($i = 0; $i < count($criteriaList); $i++) {
                                for ($j = $i + 1; $j < count($criteriaList); $j++) {
                                    $existingValue = "";
                                    if (isset($_SESSION['user_criteria'])) {
                                        foreach ($_SESSION['user_criteria'] as $comparison) {
                                            if ($comparison[0] === $criteriaList[$i] && $comparison[1] === $criteriaList[$j]) {
                                                $existingValue = $comparison[2];
                                                break;
                                            }
                                        }
                                    } else {
                                        foreach ($defaultComparison as $default) {
                                            if ($default[0] === $criteriaList[$i] && $default[1] === $criteriaList[$j]) {
                                                $existingValue = $default[2];
                                                break;
                                            }
                                        }
                                    }
                                    echo "<tr>
                                        <td>{$criteriaList[$i]}</td>
                                        <td>
                                            <select name='comparison[{$criteriaList[$i]}][{$criteriaList[$j]}]' class='form-select'>
                                                <option value='' " . ($existingValue == '' ? 'selected' : '') . ">Select Value</option>";
                                                foreach ($comparisonValues as $value) {
                                                    echo "<option value='{$value}' " . ($existingValue == $value ? 'selected' : '') . ">{$value}</option>";
                                                }
                                    echo "      </select>
                                        </td>
                                        <td>{$criteriaList[$j]}</td>
                                    </tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-between">
                        <button type="submit" name="save_comparison" class="btn btn-custom">Save Comparison</button>
                        <button type="button" id="resetComparison" class="btn btn-outline-custom">Reset to Default</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php if (!empty($selected_laptops)): ?>
        <div class="row mb-4">
            <?php foreach ($selected_laptops as $laptop): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="img/laptops/<?php echo htmlspecialchars($laptop['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($laptop['name']); ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($laptop['name']); ?></h5>
                            <p class="card-text">Price: Rp<?php echo number_format($laptop['price'], 0, ',', '.'); ?></p>
                            <ul class="list-unstyled mt-3 mb-4">
                                <li><strong>Processor:</strong> <?php echo htmlspecialchars($laptop['processor']); ?></li>
                                <li><strong>RAM:</strong> <?php echo htmlspecialchars($laptop['ram']); ?></li>
                                <li><strong>Storage:</strong> <?php echo htmlspecialchars($laptop['storage']); ?></li>
                                <li><strong>GPU:</strong> <?php echo htmlspecialchars($laptop['gpu']); ?></li>
                            </ul>
                            <form method="POST" class="mt-auto">
                                <input type="hidden" name="product_id" value="<?php echo $laptop['id']; ?>">
                                <button type="submit" name="remove_from_compare" class="btn btn-outline-custom w-100">
                                    <i class="fas fa-trash-alt"></i> Remove
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <form method="POST" class="text-center mb-5">
            <button type="submit" name="compare_now" class="btn btn-custom btn-lg">
                <i class="fas fa-balance-scale"></i> Compare Now
            </button>
        </form>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            <i class="fas fa-info-circle"></i> No laptops selected for comparison. Please add laptops from the catalog.
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById("resetComparison").addEventListener("click", function() {
    if (confirm("Are you sure you want to reset to default values?")) {
        fetch("reset_criteria.php", { method: "POST" }) 
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Comparison has been reset to default.");
                    location.reload();
                }
            });
    }
});
</script>

</body>
</html>

<?php include 'includes/footer.php'; ?>