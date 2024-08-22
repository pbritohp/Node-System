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

// Fetch data from the database
$query = "SELECT * FROM $table WHERE idPlan = $id";
$result = mysqli_query($link, $query);

// Create a new XML document
$xml = new DOMDocument('1.0', 'UTF-8');
$xml->formatOutput = true;

// Create a root element
$root = $xml->createElement($table);
$xml->appendChild($root);

// Add rows to the XML document
while ($row = mysqli_fetch_assoc($result)) {
    $item = $xml->createElement('item');

    foreach ($row as $key => $value) {
        $element = $xml->createElement($key, htmlspecialchars($value));
        $item->appendChild($element);
    }

    $root->appendChild($item);
}

// Set headers to download the file rather than displaying it
header('Content-Type: application/xml');
header('Content-Disposition: attachment; filename=' . $table . '_export.xml');

// Output the XML document
echo $xml->saveXML();
exit();
?>
