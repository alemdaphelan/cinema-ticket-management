<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing DB speed...\n";

$start = microtime(true);
$count = \App\Models\Movie::count();
echo "Movie::count() returned $count and took " . (microtime(true) - $start) . " seconds\n";

$start = microtime(true);
$movies = \App\Models\Movie::query()->orderBy('created_at', 'desc')->paginate(12);
echo "Movie::query() took " . (microtime(true) - $start) . " seconds\n";
