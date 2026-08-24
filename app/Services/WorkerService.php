<?php

namespace App\Services;

use App\Http\Requests\StoreWorkerRequest;
use App\Http\Requests\UpdateWorkerLongFormRequest;
use App\Http\Requests\UpdateWorkerRequest;
use App\Interfaces\WorkerServiceInterface;
use App\Models\Psp;
use App\Models\Shift;
use App\Models\Worker;
use App\Repositories\WorkerRepository;
// use App\Services\Contracts\WorkerServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class WorkerService implements WorkerServiceInterface
{

    private $user;
    protected WorkerRepository $workerRepository;

    /**
     * Create a new class instance.
     */
    public function __construct(WorkerRepository $workerRepository)
    {
        $this->workerRepository = $workerRepository;
        /** @var \App\Models\User|null $user */
        $this->user = Auth::user();
    }

    public function getColumns(): array
    {
        if ($this->user->hasRole(['Super Admin', 'HR', 'Admin', 'Hr Executive'])) {
            return $this->getAdminHrColumns();
        }

        if ($this->user->hasRole(['PSP'])) {
            return $this->getPspColumns();
        }

        return [];
    }

    /**
     * Get columns common for Psp.
     */
    private function getPspColumns(): array
    {
        return [
            ['data' => 'code', 'title' => 'Code'],
            ['data' => 'name', 'title' => 'Name'],
            ['data' => 'status', 'title' => 'Status', 'orderable' => false, 'searchable' => false],
            ['data' => 'shift', 'title' => 'Shift', 'orderable' => false, 'searchable' => false],
            ['data' => 'gender', 'title' => 'Gender'],
            ['data' => 'mobile_number', 'title' => "Mobile number"],
            ['data' => 'photo', 'title' => "Photo"],
            ['data' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false],
        ];
    }

    /**
     * Get columns for Super Admin and HR.
     */
    private function getAdminHrColumns(): array
    {
        return [
            ['data' => 'code', 'title' => 'Code'],
            ['data' => 'name', 'title' => 'Name'],
            ['data' => 'psp', 'title' => 'PSP', 'orderable' => false, 'searchable' => false],
            ['data' => 'status', 'title' => 'Status', 'orderable' => false, 'searchable' => false],
            ['data' => 'shift', 'title' => 'Shift', 'orderable' => false, 'searchable' => false],

            // ['data' => 'workingarea', 'title' => 'Working Area'],
            ['data' => 'date_of_joining', 'title' => 'DOJ'],
            ['data' => 'date_of_leaving', 'title' => 'DOL'],
            ['data' => 'last_working__absconded_date', 'title' => 'LWD/ABSC_DT'],
            ['data' => 'notice_period', 'title' => 'Notice Period LOA'],
            ['data' => 'notice_period_served', 'title' => 'Notice Period Served'],


            ['data' => 'designation', 'title' => 'Designation', 'orderable' => false, 'searchable' => false],
            ['data' => 'gender', 'title' => 'Gender'],
            ['data' => 'father_name', 'title' => "Father's Name"],
            ['data' => 'mobile_number', 'title' => "Mobile number"],
            ['data' => 'photo', 'title' => "Photo"],
            ['data' => 'adhar_number', 'title' => "Adhar Number"],
            ['data' => 'adhar_front', 'title' => "Adhar <small>Front</small>"],
            ['data' => 'adhar_back', 'title' => "Adhar <small>Back</small>"],

            ['data' => 'division', 'title' => "Division <small></small>", 'orderable' => false, 'searchable' => false],
            ['data' => 'department', 'title' => "Department <small></small>", 'orderable' => false, 'searchable' => false],
            ['data' => 'subdepartment', 'title' => "SubDepartment <small></small>", 'orderable' => false, 'searchable' => false],
            ['data' => 'workingarea', 'title' => 'Working Area', 'orderable' => false, 'searchable' => false],
            ['data' => 'productionproduct', 'title' => "ProductionProduct <small></small>", 'orderable' => false, 'searchable' => false],
            ['data' => 'line', 'title' => "Line <small></small>", 'orderable' => false, 'searchable' => false],
            ['data' => 'process', 'title' => "Process <small></small>", 'orderable' => false, 'searchable' => false],
            ['data' => 'level', 'title' => "Level <small></small>", 'orderable' => false, 'searchable' => false],
            ['data' => 'workertype', 'title' => "Workertype <small></small>", 'orderable' => false, 'searchable' => false],


            ['data' => 'uan', 'title' => "UAN <small>available</small>"],
            ['data' => 'uan_number', 'title' => "UAN <small>number</small>"],
            ['data' => 'uan_photo', 'title' => "UAN <small>Photo</small>"],

            ['data' => 'esic', 'title' => "Esic <small>available</small>"],
            ['data' => 'esic_number', 'title' => "Esic <small>number</small>"],


            ['data' => 'form_11_photo', 'title' => "Form 11 <small>Photo</small>"],
            ['data' => 'husband_name', 'title' => "Spouse Name"],
            ['data' => 'lop_photo', 'title' => "LOP <small>Photo</small>"],
            ['data' => 'marital_status', 'title' => "Marital status <small></small>"],

            ['data' => 'permanent_address', 'title' => "<small>Permanent</small> Address"],
            ['data' => 'permanent_district', 'title' => "<small>Permanent</small> District"],
            ['data' => 'permanent_pincode', 'title' => "<small>Permanent</small> Pincode"],
            ['data' => 'permanent_state', 'title' => "<small>Permanent</small> State"],

            ['data' => 'present_address', 'title' => "<small>Present</small> Address"],
            ['data' => 'present_district', 'title' => "<small>Present</small> District"],
            ['data' => 'present_pincode', 'title' => "<small>Present</small> Pincode"],
            ['data' => 'present_state', 'title' => "<small>Present</small> State"],

            ['data' => 'religion', 'title' => "Religion <small></small>"],

            ['data' => 'affidavit_photo', 'title' => "Affidavite <small>Photo</small>"],
            ['data' => 'date_of_birth', 'title' => "D.O.B. <small></small>"],

            ['data' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false],
        ];
    }

    public function getDataTables(Request $request): JsonResponse
    {
        $workers = $this->workerRepository->getAllWorkers($request);

        return DataTables::of($workers)
            ->editColumn('date_of_birth', function ($worker) {
                return $worker->date_of_birth ? \Carbon\Carbon::parse($worker->date_of_birth)->format('d M Y') : 'N/A';
            })
            ->editColumn('date_of_joining', function ($worker) {
                return $worker->date_of_joining ? \Carbon\Carbon::parse($worker->date_of_joining)->format('d M Y') : 'N/A';
            })
            ->editColumn('date_of_leaving', function ($worker) {
                return $worker->date_of_leaving ? \Carbon\Carbon::parse($worker->date_of_leaving)->format('d M Y') : 'N/A';
            })
            ->editColumn('photo', function ($worker) {
                return $worker->photo ? '<div class="symbol symbol-45px overflow-hidden"><img src="' . Storage::url($worker->photo) . '" alt="' . $worker->name . '" style="object-fit:cover;"></div>' : 'N/A';
            })
            ->editColumn('permanent_address', function ($worker) {
                return $worker->permanent_address ? '<p class="mx-auto text-wrap text-justify mb-0 lh-sm" style="width: max-content; max-width: 150px;"><small>' . $worker->permanent_address . '</small></p>' : 'N/A';
            })
            ->editColumn('present_address', function ($worker) {
                return $worker->present_address ? '<p class="mx-auto text-wrap mb-0 lh-sm" style="width: max-content; max-width: 150px;"><small>' . $worker->present_address . '</small></p>' : 'N/A';
            })
            ->addColumn('last_working__absconded_date', function ($worker) {
                return $worker->last_working__absconded_date ? \Carbon\Carbon::parse($worker->last_working__absconded_date)->format('d M Y') : 'N/A';
            })
            ->addColumn('notice_period', function ($worker) {
                return $worker->notice_period ? $worker->notice_period . '<small> Days</small>' : 'N/A';
            })
            ->addColumn('notice_period_served', function ($worker) {
                return $worker->notice_period_served ? $worker->notice_period_served . '<small> Days</small>' : 'N/A';
            })
            ->addColumn('psp', function ($worker) {
                return $worker?->psp?->name ?? 'N/A';
            })
            ->addColumn('shift', function ($worker) {
                return $worker?->shift?->name ?? 'N/A';
            })
            ->addColumn('status', function ($worker) {
                return $worker->employment_status ?? 'N/A';
            })
            ->addColumn('division', function ($worker) {
                return $worker?->division?->name ?? 'N/A';
            })
            ->addColumn('department', function ($worker) {
                return $worker?->department?->name ?? 'N/A';
            })
            ->addColumn('subdepartment', function ($worker) {
                return $worker?->subdepartment?->name ?? 'N/A';
            })
            ->addColumn('workingarea', function ($worker) {
                return $worker?->workingArea?->name ?? 'N/A';
            })
            ->addColumn('productionproduct', function ($worker) {
                return $worker?->productionProduct?->name ?? 'N/A';
            })
            ->addColumn('line', function ($worker) {
                return $worker?->line?->name ?? 'N/A';
            })
            ->addColumn('process', function ($worker) {
                return $worker?->process?->name ?? 'N/A';
            })
            ->addColumn('level', function ($worker) {
                return $worker?->level?->name ?? 'N/A';
            })
            ->addColumn('workertype', function ($worker) {
                return $worker?->workertype?->name ?? 'N/A';
            })
            ->addColumn('designation', function ($worker) {
                return $worker?->designation?->name ?? 'N/A';
            })
            ->addColumn('husband_name', function ($worker) {
                return $worker?->husband_name ?? 'N/A';
            })
            ->addColumn('uan', function ($worker) {
                return $worker?->uan > 0 ? $worker?->uan : 'N/A';
            })
            ->addColumn('uan_number', function ($worker) {
                return $worker?->uan_number ?? 'N/A';
            })
            ->addColumn('uan_photo', function ($worker) {
                return $worker?->uan_photo ? '<div class="symbol symbol-45px overflow-hidden"><img src="' . $worker?->uan_photo . '" alt="UAN"></div>' : 'N/A';
            })
            ->addColumn('esic', function ($worker) {
                return $worker?->esic > 0 ? $worker?->esic : 'N/A';
            })
            ->addColumn('esic_number', function ($worker) {
                return $worker?->esic_number ?? 'N/A';
            })
            ->addColumn('permanent_pincode', function ($worker) {
                return $worker?->permanent_pincode ?? 'N/A';
            })
            ->addColumn('permanent_district', function ($worker) {
                return $worker?->permanent_district ?? 'N/A';
            })
            ->addColumn('permanent_state', function ($worker) {
                return $worker?->permanent_state ?? 'N/A';
            })
            ->addColumn('present_pincode', function ($worker) {
                return $worker?->present_pincode ?? 'N/A';
            })
            ->addColumn('present_district', function ($worker) {
                return $worker?->present_district ?? 'N/A';
            })
            ->addColumn('present_state', function ($worker) {
                return $worker?->present_state ?? 'N/A';
            })
            ->addColumn('adhar_front', function ($worker) {
                return $worker->adhar_front ? '<div class="symbol symbol-45px overflow-hidden"><img src="' . Storage::url($worker->adhar_front) . '" alt="AF"></div>' : '';
            })
            ->addColumn('adhar_back', function ($worker) {
                return $worker->adhar_back ? '<div class="symbol symbol-45px overflow-hidden"><img src="' . Storage::url($worker->adhar_back) . '" alt="AB"></div>' : '';
            })
            ->addColumn('form_11_photo', function ($worker) {
                return $worker->form_11_photo ? '<div class="symbol symbol-45px overflow-hidden"><img src="' . $worker->form_11_photo . '" alt="Form 11"></div>' : 'N/A';
            })
            ->addColumn('affidavit_photo', function ($worker) {
                return $worker->affidavit_photo ? '<div class="symbol symbol-45px overflow-hidden"><img src="' . $worker->affidavit_photo . '" alt="AFDV"></div>' : 'N/A';
            })
            ->addColumn('lop_photo', function ($worker) {
                return $worker->lop_photo ? '<div class="symbol symbol-45px overflow-hidden"><img src="' . $worker->lop_photo . '" alt="LOP"></div>' : 'N/A';
            })
            ->addColumn('action', function ($worker) {
                $actionHtml = '<a href="' . route('workers.show', $worker->id) . '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 edit-worker" target="_blank">
                            <span class="svg-icon svg-icon-3">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </span>
                        </a>';

                $actionHtml .= '<a href="' . route('workers.edit', $worker->id) . '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 edit-worker">
                            <span class="svg-icon svg-icon-3">
                                <i class="fa fa-pencil-alt" aria-hidden="true"></i>
                            </span>
                        </a>';

                /** @var \App\Models\User|null $user */
                $user = Auth::user();

                if ($user->hasRole(['Super Admin', 'HR', 'Admin', 'Hr Executive'])) {
                    $actionHtml .= '<form action="' . route('workers.destroy', $worker->id) . '" method="POST" style="display:inline-block;">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 delete-worker" onclick="return confirm(\'Are you sure you want to delete this worker?\')">
                                <span class="svg-icon svg-icon-3">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </span>
                            </button>
                        </form>';
                }
                if ($user->hasPermission('Edit Worker Long Form')) {
                    $actionHtml .= '<a href="' . route('workers.edit-long-form', $worker->id) . '" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 edit-long-form-worker">
                        <span class="svg-icon svg-icon-3">
                            <i class="far fa-edit"></i>
                        </span>
                    </a>';
                }
                return '<div class="d-flex justify-content-center">' . $actionHtml . '</div>';
            })
            ->rawColumns([
                'action',
                'photo',
                'permanent_address',
                'present_address',
                'adhar_front',
                'adhar_back',
                'uan_photo',
                'form_11_photo',
                'affidavit_photo',
                'lop_photo',
            ])
            ->make(true);
    }

    /**
     * Store Worker
     */
    public function storeWorker(StoreWorkerRequest $request): void
    {
        // Validate and store worker
        $worker = $this->workerRepository->createWorker($request->validated());

        // Handle media files
        $mediaFields = $this->handleMediaUpload($request, $worker->id);

        // Update worker with media paths
        $this->workerRepository->updateWorker($worker, $mediaFields);
    }

    private function handleMediaUpload($request, int $workerId): array
    {
        $mediaFields = [];

        if ($request->hasFile('photo')) {
            $mediaFields['photo'] = $this->storeFile($request->file('photo'), $workerId, 'photo', 'worker');
        }
        if ($request->hasFile('adhar_front')) {
            $mediaFields['adhar_front'] = $this->storeFile($request->file('adhar_front'), $workerId, 'adhar_front', 'worker/adhar');
        }
        if ($request->hasFile('adhar_back')) {
            $mediaFields['adhar_back'] = $this->storeFile($request->file('adhar_back'), $workerId, 'adhar_back', 'worker/adhar');
        }
        if ($request->hasFile('uan_photo')) {
            $mediaFields['uan_photo'] = $this->storeFile($request->file('uan_photo'), $workerId, 'uan_photo', 'worker/uan');
        }
        if ($request->hasFile('form_11_photo')) {
            $mediaFields['form_11_photo'] = $this->storeFile($request->file('form_11_photo'), $workerId, 'form_11_photo', 'worker/form_11');
        }
        if ($request->hasFile('affidavit_photo')) {
            $mediaFields['affidavit_photo'] = $this->storeFile($request->file('affidavit_photo'), $workerId, 'affidavit_photo', 'worker/affidavit');
        }
        if ($request->hasFile('lop_photo')) {
            $mediaFields['lop_photo'] = $this->storeFile($request->file('lop_photo'), $workerId, 'lop_photo', 'worker/lop');
        }

        return $mediaFields;
    }

    private function storeFile($file, int $workerId, string $fileName, string $folder): string
    {
        $uploadDir = "{$folder}/{$workerId}";
        $timestamp = time();
        $filename = "{$fileName}_{$timestamp}.{$file->getClientOriginalExtension()}";
        return $file->storeAs($uploadDir, $filename, 'public');
    }


    /**
     * Update Worker
     */
    public function updateWorker(UpdateWorkerRequest $request, Worker $worker): void
    {
        // Get validated data
        $updateData = $request->validated();

        // Handle media file uploads if they exist
        $mediaFields = $this->handleMediaUpload($request, $worker->id);

        // Merge media fields with validated data
        $updateData = array_merge($updateData, $mediaFields);

        // Perform a single update call
        $this->workerRepository->updateWorker($worker, $updateData);
    }

    /**
     * Get the create worker view based on user role.
     */
    public function getCreateView(): View
    {
        $shifts = Shift::all();

        if ($this->user->hasRole('PSP')) {
            // if ($this->user->id == 4) {
            //     abort(403, 'You do not have permission to add new workers.');
            // }

            return view("workers.role_psp.create", [
                "pageTitle" => "Add New Worker",
                "user" => $this->user,
                "shifts" => $shifts,
            ]);
        }

        return view("workers.create", [
            "pageTitle" => "Add New Worker",
            "psps" => Psp::all(),
            "shifts" => $shifts,
        ]);
    }

    /**
     * Update Worker
     */
    public function updateWorkerLongForm(UpdateWorkerLongFormRequest $request, Worker $worker): void
    {
        // Get validated data
        $updateData = $request->validated();

        // Handle media file uploads if they exist
        $mediaFields = $this->handleMediaUpload($request, $worker->id);

        // Merge media fields with validated data
        $updateData = array_merge($updateData, $mediaFields);

        // Perform a single update call
        $this->workerRepository->updateWorker($worker, $updateData);
    }
}
