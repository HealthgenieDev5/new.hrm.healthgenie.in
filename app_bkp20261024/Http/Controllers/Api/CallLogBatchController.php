<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCallLogBatchRequest;
use App\Models\CallRecord;
use App\Models\Device;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class CallLogBatchController extends Controller
{
    public function __invoke(StoreCallLogBatchRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $latestTimestamp = null;

        try {
            $response = DB::transaction(function () use ($validated, &$latestTimestamp) {
                $device = Device::firstOrCreate([
                    'device_id' => $validated['device_id'],
                ]);

                $device->fill([
                    'brand' => data_get($validated, 'device.brand'),
                    'model' => data_get($validated, 'device.model'),
                    'manufacturer' => data_get($validated, 'device.manufacturer'),
                    'android_version' => data_get($validated, 'device.android_version'),
                ]);

                $device->save();

                $acceptedCount = 0;
                $duplicateCount = 0;
                $failedCount = 0;

                foreach ($validated['calls'] as $call) {
                    $callTimestamp = Carbon::parse($call['timestamp'], 'UTC');

                    if ($latestTimestamp === null || $callTimestamp->greaterThan($latestTimestamp)) {
                        $latestTimestamp = $callTimestamp;
                    }

                    $inserted = CallRecord::query()->insertOrIgnore([
                        [
                            'device_id' => $device->id,
                            'phone_number' => $call['number'],
                            'call_type' => $call['type'],
                            'duration' => $call['duration'],
                            'call_timestamp' => $callTimestamp,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                    ]);

                    if ($inserted === 1) {
                        $acceptedCount++;
                    } else {
                        $duplicateCount++;
                    }
                }

                $device->forceFill([
                    'last_synced_at' => now(),
                ])->save();

                return [
                    'accepted_count' => $acceptedCount,
                    'duplicate_count' => $duplicateCount,
                    'failed_count' => $failedCount,
                ];
            });
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Unable to process call log batch.',
            ], 500);
        }

        return response()->json([
            ...$response,
            'latest_processed_timestamp' => $latestTimestamp?->toJSON() ?? Carbon::parse($validated['sent_at'])->toJSON(),
        ]);
    }
}
