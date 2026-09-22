<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
echo "\nTotal Shows: " . App\Models\Show::count() . "\n";
echo "Total Movies: " . App\Models\Movie::count() . "\n";
echo "Showing Movies: " . App\Models\Movie::where('status', 'showing')->count() . "\n";
