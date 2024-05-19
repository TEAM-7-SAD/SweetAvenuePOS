<?php
include_once 'default-timezone.php';

function should_execute_script() {
    // Get the current day of the week
    $current_day = date('N');
    
    // Check if the current day is between Monday (1) and Sunday (7)
    return $current_day >= 1 && $current_day <= 7;
}

if (should_execute_script()) {
    // Execute the Python script
    $output = shell_exec('../sales-prediction-algorithm/regression-model.py');
    echo $output;
} else {
    echo "Script execution is not allowed today.";
}
