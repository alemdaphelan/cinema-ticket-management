<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$shows = App\Models\Show::with('movie')->get();
foreach ($shows as $show) {
    echo "Movie: {$show->movie->title} - Date: {$show->start_time}\n";
}
