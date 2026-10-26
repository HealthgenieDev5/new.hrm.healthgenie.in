<?php

namespace App\Interfaces;

use App\Http\Requests\StoreWorkerRequest;
use App\Http\Requests\UpdateWorkerLongFormRequest;
use App\Http\Requests\UpdateWorkerRequest;
use App\Models\Worker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

interface WorkerServiceInterface
{
    public function getColumns(): array;
    public function getDataTables(Request $request): JsonResponse;
    public function storeWorker(StoreWorkerRequest $request): void;
    public function updateWorker(UpdateWorkerRequest $request, Worker $worker): void;
    public function updateWorkerLongForm(UpdateWorkerLongFormRequest $request, Worker $worker): void;
    public function getCreateView(): View;
}
