<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json(['status' => 'ok'], 200);
});

Route::get('/ready', function () {
    return response()->json(['status' => 'ready'], 200);
});

Route::get('/info', function () {
    return response()->json([
        'pod'       => gethostname(),
        'timestamp' => now()->toIso8601String(),
        'memory'    => [
            'used_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'peak_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
        ],
    ], 200);
});

Route::get('/load-test', function (Request $request) {
    $duration   = min(max((int) $request->query('duration', 10), 1), 60);
    $iterations = min(max((int) $request->query('iterations', 1000), 100), 1_000_000);

    $start = microtime(true);
    $count = 0;

    while ((microtime(true) - $start) < $duration) {
        for ($i = 0; $i < $iterations; $i++) {
            sqrt(rand(1, 100000));
        }
        $count++;
    }

    return response()->json([
        'status'     => 'done',
        'duration_s' => round(microtime(true) - $start, 2),
        'iterations' => $count * $iterations,
        'pod'        => gethostname(),
    ], 200);
});