<?php
// Simple health check
http_response_code(200);
echo json_encode([
    'status' => 'ok',
    'time' => date('Y-m-d H:i:s'),
    'laravel' => app()->version()
]);
