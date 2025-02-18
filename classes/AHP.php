<?php
class AHP {
    private $pairwiseMatrix;
    private $normalizedMatrix;
    private $priorities;
    private $weightedSums;
    private $lambdaValues;
    private $lambdaMax;
    private $CI;
    private $CR;
    private $calculationDetails;

    public function __construct($userCriteria = null) {
        // Kriteria default jika user tidak memasukkan input
        $default_criteria_comparison = [
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

        // Gunakan input pengguna jika tersedia, jika tidak pakai default
        $criteria_comparison = (!empty($userCriteria)) ? $userCriteria : $default_criteria_comparison;

        $this->initializePairwiseMatrix($criteria_comparison);
    }

    private function initializePairwiseMatrix($criteria_comparison) {
        // Inisialisasi matriks 6x6 dengan nilai 1 di diagonal
        $this->pairwiseMatrix = array_fill(0, 6, array_fill(0, 6, 1));

        // Isi matriks berdasarkan perbandingan
        foreach ($criteria_comparison as $comparison) {
            $row = intval(substr($comparison[0], 1)) - 1;
            $col = intval(substr($comparison[1], 1)) - 1;
            $value = floatval($comparison[2]);

            $this->pairwiseMatrix[$row][$col] = $value;
            $this->pairwiseMatrix[$col][$row] = 1 / $value;
        }
    }

    private function calculatePriorities() {
        $columnSums = array_fill(0, 6, 0);

        // Hitung jumlah kolom
        foreach ($this->pairwiseMatrix as $row) {
            foreach ($row as $colIndex => $value) {
                $columnSums[$colIndex] += $value;
            }
        }

        $this->calculationDetails['columnSums'] = $columnSums;

        // Normalisasi matriks dan hitung prioritas
        $this->normalizedMatrix = [];
        $this->priorities = array_fill(0, 6, 0);

        foreach ($this->pairwiseMatrix as $rowIndex => $row) {
            foreach ($row as $colIndex => $value) {
                $this->normalizedMatrix[$rowIndex][$colIndex] = $value / $columnSums[$colIndex];
                $this->priorities[$rowIndex] += $this->normalizedMatrix[$rowIndex][$colIndex];
            }
            $this->priorities[$rowIndex] /= 6;
        }
    }

    private function calculateConsistency() {
        // Hitung weighted sums
        $this->weightedSums = array_fill(0, 6, 0);
        foreach ($this->pairwiseMatrix as $rowIndex => $row) {
            foreach ($row as $colIndex => $value) {
                $this->weightedSums[$rowIndex] += $value * $this->priorities[$colIndex];
            }
        }

        // Hitung lambda values
        $this->lambdaValues = [];
        foreach ($this->weightedSums as $index => $value) {
            $this->lambdaValues[$index] = $value / $this->priorities[$index];
        }

        // Hitung λ max, CI, dan CR
        $this->lambdaMax = array_sum($this->lambdaValues) / 6;
        $this->CI = ($this->lambdaMax - 6) / 5;
        $this->CR = $this->CI / 1.24; // RI untuk n=6 adalah 1.24
    }

    // public function compareLaptops($laptops) {
    //     $this->calculatePriorities();
    //     $this->calculateConsistency();

    //     // Simpan detail perhitungan
    //     $this->calculationDetails = [
    //         'pairwiseMatrix' => $this->pairwiseMatrix,
    //         'normalizedMatrix' => $this->normalizedMatrix,
    //         'priorities' => $this->priorities,
    //         'weightedSums' => $this->weightedSums,
    //         'lambdaValues' => $this->lambdaValues,
    //         'lambdaMax' => $this->lambdaMax,
    //         'CI' => $this->CI,
    //         'CR' => $this->CR
    //     ];

    //     // Hitung skor akhir untuk setiap laptop
    //     $results = [];
    //     // foreach ($laptops as $laptop) {
    //     //     $score = 0;
    //     //     $score += $laptop['processor_score'] * $this->priorities[0];
    //     //     $score += $laptop['ram_score'] * $this->priorities[1];
    //     //     $score += $laptop['storage_score'] * $this->priorities[2];
    //     //     $score += $laptop['gpu_score'] * $this->priorities[3];
    //     //     $score += $laptop['display_score'] * $this->priorities[4];
    //     //     $score += $laptop['harga_score'] * $this->priorities[5];

    //     //     $results[$laptop['name']] = [
    //     //         'score' => $score,
    //     //         'details' => [
    //     //             'processor' => $laptop['processor_score'] * $this->priorities[0],
    //     //             'ram' => $laptop['ram_score'] * $this->priorities[1],
    //     //             'storage' => $laptop['storage_score'] * $this->priorities[2],
    //     //             'gpu' => $laptop['gpu_score'] * $this->priorities[3],
    //     //             'display' => $laptop['display_score'] * $this->priorities[4],
    //     //             'harga' => $laptop['harga_score'] * $this->priorities[5]
    //     //         ]
    //     //     ];
    //     // }
    //     foreach ($laptops as $laptop) {
    //         $score = 0;
    //         $details = [];
        
    //         $details['processor'] = $laptop['processor_score'] * $this->priorities[0];
    //         echo $laptop['processor_score'];
    //         $details['ram'] = $laptop['ram_score'] * $this->priorities[1];
    //         $details['storage'] = $laptop['storage_score'] * $this->priorities[2];
    //         $details['gpu'] = $laptop['gpu_score'] * $this->priorities[3];
    //         $details['display'] = $laptop['display_score'] * $this->priorities[4];
    //         $details['harga'] = $laptop['harga_score'] * $this->priorities[5];
        
    //         $score = array_sum($details); // Total semua nilai
        
    //         $results[$laptop['name']] = [
    //             'score' => $score,
    //             'details' => $details
    //         ];
    //     }
        

    //     // Urutkan hasil berdasarkan skor tertinggi
    //     uasort($results, function($a, $b) {
    //         return $b['score'] <=> $a['score'];
    //     });

    //     return $results;
    // }

    public function compareLaptops($laptops) {
        $this->calculatePriorities();
        $this->calculateConsistency();
    
        // Simpan detail perhitungan
        $this->calculationDetails = [
            'pairwiseMatrix' => $this->pairwiseMatrix,
            'normalizedMatrix' => $this->normalizedMatrix,
            'priorities' => $this->priorities, // ✅ Menggunakan Prioritas dari tahap 3️⃣
            'weightedSums' => $this->weightedSums,
            'lambdaValues' => $this->lambdaValues,
            'lambdaMax' => $this->lambdaMax,
            'CI' => $this->CI,
            'CR' => $this->CR
        ];
    
        // **Pastikan urutan kriteria sesuai dengan mapping yang benar**
        // C1 = harga, C2 = layar, C3 = CPU, C4 = GPU, C5 = Storage, C6 = RAM
        $results = [];
        foreach ($laptops as $laptop) {
            $score = 0;
            $details = [];
    
            // **Mengalikan nilai alternatif dengan bobot prioritas yang benar**
            $details['harga']   = $laptop['harga_score'] * $this->priorities[0]; // C1
            $details['display'] = $laptop['display_score'] * $this->priorities[1]; // C2
            $details['cpu']     = $laptop['processor_score'] * $this->priorities[2]; // C3
            $details['gpu']     = $laptop['gpu_score'] * $this->priorities[3]; // C4
            $details['storage'] = $laptop['storage_score'] * $this->priorities[4]; // C5
            $details['ram']     = $laptop['ram_score'] * $this->priorities[5]; // C6
    
            // Total skor akhir
            $score = array_sum($details);
    
            // Simpan hasil ke array
            $results[$laptop['id']] = [
                'name' => $laptop['name'],
                'score' => $score,
                'details' => $details
            ];
        }
    
        // Urutkan hasil berdasarkan skor tertinggi
        uasort($results, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });
    
        return $results;
    }
    
    

    public function getCalculationDetails() {
        return $this->calculationDetails;
    }
}
?>
