<?php
/**
 * PHP Script to create YAML fixture file for symfony from CSV file
 */
if (!isset($argv[1])) {
    echo "Please pass CSV file name as an argument to the script.\n";
    exit;
}

$filename_ext = $argv[1];
list($filename, $ext) = explode(".", $argv[1]);

if (!file_exists($filename_ext)) {
    echo "File does not exist.\n";
    exit;
}

if ($ext !== 'csv') {
    echo "File is not a CSV file.\n";
    exit;
}

$handle = fopen($filename_ext, "r");
$row = 0;
$header = [];

$class_name = "PiiQuery";
$row_class_name = $class_name . ":" . "\n";
$output_data = $row_class_name;

while ($data = fgetcsv($handle)) {
    if ($row === 0) {
        for ($i = 0; $i < count($data); $i++) {
            $header[] = $data[$i];
        }
        $row = $row + 1;
        continue;
    }

    $record_label = $class_name . "_" . $data[0];
    $record_space = "  ";
    $row_record_label = $record_space . $record_label . ":" . "\n";
    $output_data = $output_data . $row_record_label;

    for ($i = 0; $i < count($data); $i++) {
        $delimiter = null;
        $column_data = null;

        if ($data[$i]!=='') {
            $is_multiline = false;
            $multiline = '';

            if (stripos($data[$i], PHP_EOL) !== FALSE) {
                $is_multiline = true;
                $multiline = "|" . PHP_EOL . $record_space . $record_space . $record_space;

                $col_data = str_replace(PHP_EOL, PHP_EOL . $record_space . $record_space . $record_space, $data[$i]);
            } else {
                $is_multiline = false;
                $multiline = '';

                $col_data = $data[$i];
            }

            if (stripos($data[$i], "'") !== false) {
                $delimiter = '"';
            } else {
                $delimiter = "'";
            }

            if (stripos($data[$i], ' ') !== false) {
                $column_data = $multiline . $delimiter . $col_data . $delimiter;
            } else {
                $column_data = $col_data;
            }
        } else {
            $column_data = 'null';
        }
        $row_column_data = $record_space . $record_space . $header[$i] . ": " . $column_data . "\n";
        $output_data = $output_data . $row_column_data;
    }
}

$output_filename = $filename . '.yml';
file_put_contents($output_filename, $output_data);
fclose($handle);
echo "Done! File $filename.yml has been generated!\n";
