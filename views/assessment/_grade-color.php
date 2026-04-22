<?php
/**
 * Partial view for grade level color codes
 * 
 * @var string $level The grade level (BE, AE, ME, EE)
 */

$colors = [
    'BE' => '#dc3545', // Red - Beginning
    'AE' => '#ffc107', // Yellow - Approaching
    'ME' => '#17a2b8', // Blue - Meets Expected
    'EE' => '#28a745', // Green - Exceeds Expected
];

echo $colors[$level] ?? '#6c757d'; // Default gray if level not found
