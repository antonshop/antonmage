<?php
$row = 0;
$output_file = "output.csv";
$handle = fopen(dirname(__FILE__) . "/Content.csv", "r");

if(!file_exists($output_file)) {
	file_put_contents ($output_file, "");
} else {
	unlink ($output_file);
}
$output = fopen($output_file, 'w');

while ($data = fgetcsv($handle, 1000, ",")) {

	$row++;
	if ($row == 1) {

		$content = implode(",", $data) . "\n\r";
		// fwrite($output, $content);
		fputcsv($output, $data);

	} else {

		$data[18] = floatval($data[18]) + 50;
		$data[10] = "/" . basename($data[10]);
		$data[11] = "/" . basename($data[11]);
		$data[12] = "/" . basename($data[12]);
		$gallery  = explode(";", $data[38]);

    	foreach ($gallery as $key => $value) {
    		$gallery[$key] = "/" . basename($value);
    	}
    	$data[38] = implode(";", $gallery);
    	echo count($data);
    	// $content = implode(",", $data) . "\n\r";
    	// echo $content."<br />";
    	// fwrite($output, $content);
    	fputcsv($output, $data);
	}
}

fclose($handle);
fclose($output);