<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\{Service, AttendanceRecord, Person, Family};
use App\Services\AttendanceSummaryService;
use Carbon\Carbon;

class AttendanceCheckin extends Component
{
    public Service $service;
    public $checked        = [];
    public $viewMode       = 'flat';
    public $filterCategory = '';
    public $filterFamily   = null;
    public $search         = '';

    public function mount(Service $service)
    {
        $this->service = $service;
        $this->loadExistingRecords();
    }

    protected function loadExistingRecords(): void
    {
        $this->checked = [];

        AttendanceRecord::where('service_id', $this->service->id)
            ->where('status', 'present')
            ->pluck('person_id')
            ->each(fn($id) => $this->checked[$id] = true);
    }

    #[On('togglePerson')]
    public function togglePerson($personId): void
    {
        $this->checked[$personId] = !($this->checked[$personId] ?? false);

        if ($this->checked[$personId]) {
            AttendanceRecord::updateOrCreate(
                ['service_id' => $this->service->id, 'person_id' => $personId],
                ['status' => 'present', 'check_in_time' => Carbon::now()->format('H:i:s')]
            );
        } else {
            AttendanceRecord::where('service_id', $this->service->id)
                ->where('person_id', $personId)->delete();
        }
    }

    #[On('personCreated')]
    public function addNewPerson($personId): void
    {
        $this->checked[$personId] = true;
        AttendanceRecord::updateOrCreate(
            ['service_id' => $this->service->id, 'person_id' => $personId],
            ['status' => 'present', 'check_in_time' => Carbon::now()->format('H:i:s')]
        );
    }

    public function setViewMode($mode): void
    {
        $this->viewMode = $mode;
    }

    public function save(): void
    {
        // Rebuild records
        AttendanceRecord::where('service_id', $this->service->id)->delete();
        $presentIds = [];
        foreach ($this->checked as $personId => $present) {
            if ($present) {
                AttendanceRecord::create([
                    'service_id' => $this->service->id,
                    'person_id'  => $personId,
                    'status'     => 'present',
                    'check_in_time' => Carbon::now()->format('H:i:s'),
                ]);
                $presentIds[] = $personId;
            }
        }

        // Recompute summaries for everyone who was checked in this service
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

        return view('livewire.attendance-checkin', [
            'allPeople'      => $allPeople,
            'totalCount'     => $allPeople->count(),
            'presentCount'   => $presentCount,
            'categoryCounts' => $categoryCounts,
            'families'       => Family::orderBy('family_name')->get(),
            'categories'     => Person::CATEGORIES,
        ]);
    }
}
