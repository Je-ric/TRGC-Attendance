<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\{AttendanceType, AttendanceSession, AttendanceRecord, Person, Family};
use App\Services\AttendanceSummaryService;
use Carbon\Carbon;

class AttendanceCheckin extends Component
{
    public AttendanceType $attendanceType;
    public $date;
    public $service_name   = '';
    public $checked        = [];
    public $viewMode       = 'flat';
    public $filterCategory = '';
    public $filterFamily   = null;
    public $search         = '';

    public function mount(AttendanceType $attendanceType)
    {
        $this->attendanceType = $attendanceType;
        $this->date           = Carbon::today()->format('Y-m-d');
        $this->loadExistingRecords();
    }

    public function updatedDate()
    {
        $this->loadExistingRecords();
    }

    protected function loadExistingRecords(): void
    {
        $this->checked = [];

        $session = AttendanceSession::where('attendance_type_id', $this->attendanceType->id)
            ->where('date', $this->date)
            ->when($this->service_name,
                fn($q) => $q->where('service_name', $this->service_name),
                fn($q) => $q->whereNull('service_name')
            )->first();

        if ($session) {
            $this->service_name = $session->service_name ?? '';
            AttendanceRecord::where('attendance_session_id', $session->id)
                ->where('status', 'present')
                ->pluck('person_id')
                ->each(fn($id) => $this->checked[$id] = true);
        }
    }

    protected function getOrCreateSession(): AttendanceSession
    {
        return AttendanceSession::firstOrCreate(
            ['attendance_type_id' => $this->attendanceType->id, 'date' => $this->date],
            ['service_name' => $this->service_name ?: null]
        );
    }

    #[On('togglePerson')]
    public function togglePerson($personId): void
    {
        $this->checked[$personId] = !($this->checked[$personId] ?? false);
        $session = $this->getOrCreateSession();

        if ($this->checked[$personId]) {
            AttendanceRecord::updateOrCreate(
                ['attendance_session_id' => $session->id, 'person_id' => $personId],
                ['status' => 'present']
            );
        } else {
            AttendanceRecord::where('attendance_session_id', $session->id)
                ->where('person_id', $personId)->delete();
        }
    }

    #[On('personCreated')]
    public function addNewPerson($personId): void
    {
        $this->checked[$personId] = true;
        $session = $this->getOrCreateSession();
        AttendanceRecord::updateOrCreate(
            ['attendance_session_id' => $session->id, 'person_id' => $personId],
            ['status' => 'present']
        );
    }

    public function setViewMode($mode): void
    {
        $this->viewMode = $mode;
    }

    public function save(): void
    {
        $this->validate([
            'service_name' => 'nullable|string|max:255',
            'date'         => 'required|date|before_or_equal:today',
        ]);

        // Upsert session
        $session = AttendanceSession::where('attendance_type_id', $this->attendanceType->id)
            ->where('date', $this->date)
            ->when($this->service_name,
                fn($q) => $q->where('service_name', $this->service_name),
                fn($q) => $q->whereNull('service_name')
            )->first()
            ?? AttendanceSession::create([
                'attendance_type_id' => $this->attendanceType->id,
                'date'               => $this->date,
                'service_name'       => $this->service_name ?: null,
            ]);

        // Rebuild records
        AttendanceRecord::where('attendance_session_id', $session->id)->delete();
        $presentIds = [];
        foreach ($this->checked as $personId => $present) {
            if ($present) {
                AttendanceRecord::create([
                    'attendance_session_id' => $session->id,
                    'person_id'             => $personId,
                    'status'                => 'present',
                ]);
                $presentIds[] = $personId;
            }
        }

        // Recompute summaries for everyone who was checked in this session
        $service = app(AttendanceSummaryService::class);
        foreach ($presentIds as $personId) {
            $service->recompute($personId);
        }

        $count = count($presentIds);
        $this->dispatch('lw-toast', type: 'success', message: "Attendance saved — {$count} present.");
        $this->dispatch('attendanceSaved');
    }

    public function render()
    {
        $query = Person::with('family')->orderBy('last_name')->orderBy('first_name');

        if ($this->filterFamily)   $query->where('family_id', $this->filterFamily);
        if ($this->search) {
            $query->where(fn($q) =>
                $q->where('first_name', 'like', "%{$this->search}%")
                  ->orWhere('last_name',  'like', "%{$this->search}%")
                  ->orWhere('contact_number', 'like', "%{$this->search}%")
            );
        }

        $allPeople = $query->get();
        if ($this->filterCategory) {
            $allPeople = $allPeople->filter(fn($p) => $p->effective_category === $this->filterCategory)->values();
        }

        $presentCount   = count(array_filter($this->checked));
        $categoryCounts = [];
        foreach ($this->checked as $personId => $isChecked) {
            if ($isChecked) {
                $person = $allPeople->firstWhere('id', $personId);
                if ($person) {
                    $cat = $person->effective_category;
                    $categoryCounts[$cat] = ($categoryCounts[$cat] ?? 0) + 1;
                }
            }
        }

        $latestSession = AttendanceSession::where('attendance_type_id', $this->attendanceType->id)
            ->orderByDesc('date')->orderByDesc('created_at')->first();

        $currentSession = AttendanceSession::where('attendance_type_id', $this->attendanceType->id)
            ->where('date', $this->date)
            ->when($this->service_name,
                fn($q) => $q->where('service_name', $this->service_name),
                fn($q) => $q->whereNull('service_name')
            )->first();

        return view('livewire.attendance-checkin', [
            'allPeople'      => $allPeople,
            'totalCount'     => $allPeople->count(),
            'presentCount'   => $presentCount,
            'categoryCounts' => $categoryCounts,
            'latestSession'  => $latestSession,
            'currentSession' => $currentSession,
            'families'       => Family::orderBy('family_name')->get(),
            'categories'     => Person::CATEGORIES,
        ]);
    }
}
