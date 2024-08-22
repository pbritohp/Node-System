<?php
include('dbconnect.php');

// Get the table name and ID from the URL parameters
$table = isset($_GET['table']) ? $_GET['table'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Validate the table name to prevent SQL injection
$validTables = ['fatExt', 'fatIN', 'planCult', 'planstr', 'SWOT'];
if (!in_array($table, $validTables)) {
    die('Invalid table name');
}

// Define the file name
$filename = $table . "_export.csv";

// Set headers to download the file rather than displaying it
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

// Create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// Fetch data from the database
$query = "SELECT * FROM $table WHERE idPlan = $id";
$result = mysqli_query($link, $query);

// Get the column names
$fields = mysqli_fetch_fields($result);
$headers = [];
foreach ($fields as $field) {
    $headers[] = $field->name;
}

// Output the column headings
fputcsv($output, $headers);

// Output the rows
while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, $row);
}

// Close the file pointer
fclose($output);
exit();
?>
