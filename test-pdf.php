<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pdf = Barryvdh\DomPDF\Facade\Pdf::loadHTML('<html><body><h1>Hello World</h1><p>This is a test.</p></body></html>');
file_put_contents('test.pdf', $pdf->output());
echo "Done\n";
