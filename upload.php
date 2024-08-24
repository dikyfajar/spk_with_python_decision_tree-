<?php
require 'vendor/autoload.php';

use Phpml\Classification\DecisionTree;
use Phpml\Dataset\CsvDataset;
use Phpml\ModelManager;
use Phpml\Metric\Accuracy;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file']['tmp_name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["file"]["name"]);

    if (move_uploaded_file($file, $target_file)) {
        // Load the CSV file
        $dataset = new CsvDataset($target_file, 7, true);

        // Extract samples and labels
        $samples = [];
        $labels = [];
        foreach ($dataset->getSamples() as $index => $sample) {
            $samples[] = $sample;
            $labels[] = $dataset->getTargets()[$index];
        }

        // Split the data into training and testing sets
        $splitIndex = (int)(0.6 * count($samples)); // 70% train, 30% test
        $trainSamples = array_slice($samples, 0, $splitIndex);
        $trainLabels = array_slice($labels, 0, $splitIndex);
        $testSamples = array_slice($samples, $splitIndex);
        $testLabels = array_slice($labels, $splitIndex);

        // Train the Decision Tree classifier
        $classifier = new DecisionTree();
        $classifier->train($trainSamples, $trainLabels);

        // Save the model
        $modelManager = new ModelManager();
        $modelManager->saveToFile($classifier, 'uploads/model.dat');

        // Make predictions
        $trainPredictions = $classifier->predict($trainSamples);
        $testPredictions = $classifier->predict($testSamples);

        // Calculate accuracy
        $trainAccuracy = Accuracy::score($trainLabels, $trainPredictions);
        $testAccuracy = Accuracy::score($testLabels, $testPredictions);

        // Prepare processed data to send back
        $processedTrainData = [];
        foreach ($trainSamples as $index => $sample) {
            $processedTrainData[] = array_merge($sample, [$trainLabels[$index], $trainPredictions[$index]]);
        }

        $processedTestData = [];
        foreach ($testSamples as $index => $sample) {
            $processedTestData[] = array_merge($sample, [$testLabels[$index], $testPredictions[$index]]);
        }

        // Generate decision tree plot
        // $plotFile = 'uploads/decision_tree_plot.png';
        $plotFile = 'uploads/tree.png';
        $python_path = 'C:\Users\Lenovo\AppData\Local\Programs\Python\Python312\python.exe';  // Sesuaikan path ini
        // $python_path = 'C:\Users\Lenovo\AppData\Local\Microsoft\WindowsApps\python.exe';
        // $script_path = 'plot_decision_tree.py';
        $script_path = 'tree_copy.py';
        $command = escapeshellcmd("$python_path $script_path $target_file $plotFile");
        $output = shell_exec($command);
        // var_dump($output);
        // die;
        $output = str_replace("\n", "", $output);

        // Return success response with processed data and plot path
        echo json_encode([
            'status' => 'success',
            'message' => 'File uploaded and model trained successfully.',
            // 'trainAccuracy' => $trainAccuracy,
            // 'testAccuracy' => $testAccuracy,
            'trainData' => $processedTrainData,
            'testData' => $processedTestData,
            'plotFile' => $plotFile,
            'samples' => $samples,
            'output' => $output
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'There was an error uploading the file.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
