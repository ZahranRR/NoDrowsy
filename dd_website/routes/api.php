<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IotController;

Route::post('/sensor', [IotController::class, 'store']);
Route::get('/sensor/latest', [IotController::class, 'latest']);
Route::post('/baseline/reset',   [IotController::class, 'resetBaseline']);

// Route::post('/predict', function(\Illuminate\Http\Request $request) {
//     try {
//         \Illuminate\Support\Facades\Log::info('Predict called', ['count' => count($request->input('landmarks', []))]);
        
//         $response = \Illuminate\Support\Facades\Http::timeout(10)->post(
//             'http://127.0.0.1:5000/predict',
//             $request->all()
//         );
        
//         \Illuminate\Support\Facades\Log::info('Flask response', ['status' => $response->status()]);
//         return $response->json();
        
//     } catch (\Exception $e) {
//         \Illuminate\Support\Facades\Log::error('Flask error: ' . $e->getMessage());
//         return response()->json(['error' => $e->getMessage(), 'confidence' => 0.0]);
//     }
// });

// Route::get('/test-flask', function() {
//     try {
//         $response = \Illuminate\Support\Facades\Http::timeout(5)->get('http://127.0.0.1:5000/health');
//         return $response->json();
//     } catch (\Exception $e) {
//         return response()->json(['error' => $e->getMessage()]);
//     }
// });