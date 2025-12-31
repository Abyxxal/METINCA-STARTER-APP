<?php

// Load Laravel
require __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Test the API directly
$request = \Illuminate\Http\Request::create('/api/competencies/skills', 'GET');
$response = $kernel->handle($request);

echo "Status: " . $response->status() . "\n";
echo "Content:\n";
echo $response->getContent();
echo "\n";
