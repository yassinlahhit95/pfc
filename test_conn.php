<?php
mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR);
try {
    $c = mysqli_connect('p:localhost', 'yassjjzw_adminpfc', 'Yassin1995***', 'yassjjzw_pfc');
    echo "Connected p:localhost\n";
} catch (Exception $e) {
    echo "p:localhost failed: " . $e->getMessage() . "\n";
}
try {
    $c = mysqli_connect('localhost', 'yassjjzw_adminpfc', 'Yassin1995***', 'yassjjzw_pfc');
    echo "Connected localhost\n";
} catch (Exception $e) {
    echo "localhost failed: " . $e->getMessage() . "\n";
}
try {
    $c = mysqli_connect('127.0.0.1', 'yassjjzw_adminpfc', 'Yassin1995***', 'yassjjzw_pfc');
    echo "Connected 127.0.0.1\n";
} catch (Exception $e) {
    echo "127.0.0.1 failed: " . $e->getMessage() . "\n";
}
