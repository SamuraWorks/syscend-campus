<?php

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    Route::get('/students/search', function (Request $request) {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json(['data' => []]);
        }

        $students = Student::query()
            ->where('school_id', $request->user()->school_id)
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('admission_no', 'like', "%{$query}%");
            })
            ->select('id', 'first_name', 'last_name', 'admission_no', 'class_id', 'section_id')
            ->limit(20)
            ->get();

        return response()->json(['data' => $students]);
    });

    Route::post('/payments/webhook/orange-money', function (Request $request) {
        \Log::info('Orange Money webhook received', [
            'transaction_id' => $request->input('data?.transactionId') ?? 'unknown',
            'status' => $request->input('data?.status') ?? 'unknown',
            'timestamp' => now()->toIso8601String(),
        ]);
        return response()->json(['status' => 'received']);
    });

});
