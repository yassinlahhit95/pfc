<?php
$str = '<iframe src="https://www.google.com/maps/embed?pb=123" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>';
echo "Original:\n$str\n";
echo "Sanitized:\n" . strip_tags($str, '<iframe>') . "\n";
