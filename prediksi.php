<?php
require 'vendor/autoload.php';

use Phpml\ModelManager;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sample'])) {
    $sample = json_decode(json_encode($_POST['sample']), true); // Convert to array
    $modelManager = new ModelManager();
    $classifier = $modelManager->restoreFromFile('uploads/model.dat');

    $prediction = $classifier->predict([$sample]);

    echo json_encode([
        'status' => 'success',
        'prediction' => $prediction[0]
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
