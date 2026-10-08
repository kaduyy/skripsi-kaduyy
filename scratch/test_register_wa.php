<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Models\User;

echo "=== TEST REGISTER WHATSAPP NORMALIZATION ===" . PHP_EOL;

$testNumbers = [
    '081298765432' => '6281298765432',
    '81298765433' => '6281298765433',
    '6281298765434' => '6281298765434',
    '+62 812-9876-5435' => '6281298765435',
];

foreach ($testNumbers as $input => $expected) {
    auth()->logout();
    $email = 'watest_' . time() . '_' . rand(100, 999) . '@test.com';
    
    $session = $app['session']->driver();
    $session->start();
    $token = $session->token();
    
    $request = Request::create('/register', 'POST', [
        '_token' => $token,
        'name' => 'WA Test User',
        'no_whatsapp' => $input,
        'email' => $email,
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);
    $request->setLaravelSession($session);
    
    $response = $app->handle($request);
    
    $user = User::where('email', $email)->first();
    if ($user && $user->no_whatsapp === $expected) {
        echo "[PASS] Input: " . str_pad($input, 20) . " -> Saved as: " . $user->no_whatsapp . PHP_EOL;
        $user->delete();
    } else {
        echo "[FAIL] Input: " . str_pad($input, 20) . " -> Saved: " . ($user ? $user->no_whatsapp : 'USER NOT CREATED') . " (Expected: $expected)" . PHP_EOL;
        if ($session->has('errors')) {
            $errors = $session->get('errors')->getBag('default')->all();
            echo "       Validation Errors: " . json_encode($errors) . PHP_EOL;
        }
        if (isset($response->exception) && $response->exception) {
            echo "       Error: " . $response->exception->getMessage() . PHP_EOL;
        }
    }
}

echo "=== ALL REGISTRATION TESTS FINISHED ===" . PHP_EOL;
