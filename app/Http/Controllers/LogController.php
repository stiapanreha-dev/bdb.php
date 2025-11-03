<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogController extends Controller
{
    public function clientLog(Request $request)
    {
        $level = $request->input('level', 'info');
        $message = $request->input('message', '');
        $context = $request->input('context', []);

        $logMessage = "[CLIENT] {$message}";

        switch ($level) {
            case 'error':
                Log::error($logMessage, $context);
                break;
            case 'warning':
                Log::warning($logMessage, $context);
                break;
            default:
                Log::info($logMessage, $context);
                break;
        }

        return response()->json(['success' => true]);
    }
}
