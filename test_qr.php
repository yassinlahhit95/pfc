<?php 
require 'vendor/autoload.php'; 
use Endroid\QrCode\Builder\Builder; 
use Endroid\QrCode\Color\Color; 
use Endroid\QrCode\ErrorCorrectionLevel; 

$b = new Builder(
    data: 'test', 
    size: 90, 
    margin: 4, 
    foregroundColor: new Color(30, 58, 110), 
    backgroundColor: new Color(255, 255, 255), 
    errorCorrectionLevel: ErrorCorrectionLevel::Medium
); 
print_r(get_class($b->build()));
