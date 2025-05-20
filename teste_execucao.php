<?php
$output = [];
$return_var = 0;
exec('C:\\xampp\\htdocs\\dashboard\\bot\\teste.bat', $output, $return_var);
echo "Return code: $return_var\n";
echo "Output: " . implode("\n", $output);
?>
