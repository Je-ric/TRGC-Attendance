<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\{AttendanceType, AttendanceSession, AttendanceRecord, Person, Family};
use Carbon\Carbon;
use Livewire\Attributes\On;

class AttendanceCheckin extends Component
{
    public AttendanceType $attendanceType;
    public $date;
    public $service_name;
    public $checked = [];
    public $viewMode = 'flat'; // 'flat', 'family', 'category'
    public $filterCategory = '';
    public $filterFamily = null;
    public $search = '';

    public function mount(AttendanceType $attendanceType)
    {
        $this->attendanceType = $attendanceType;
        $this->date = Carbon::today()->format('Y-m-d');
        $this->loadExistingRecords();
    }

    public function updatedDate()
    {
        $this->loadExistingRecords();
    }

    public function updatedServiceName()
    {
        // Don't reload if service name is being cleared for editing
        if ($this->service_name !== '') {
            $this->loadExistingRecords();
        }
    }

    public function clearServiceName()
    {
        $this->service_name = '';
        $this->loadExistingRecords();
    }

    protected function loadExistingRecords()
    {
        $this->checked = [];

        // Load existing session for this date and type
        $query = AttendanceSession::where('attendance_type_id', $this->attendanceType->id)
            ->where('date', $this->date);

        if ($this->service_name) {
            $query->where('service_name', $this->service_name);
        } else {
            $query->whereNull('service_name');
        }

        $session = $query->first();

        if ($session) {
            $this->service_name = $session->service_name;
            $records = AttendanceRecord::where('attendance_session_id', $session->id)
                ->where('status', 'present')
                ->pluck('person_id')
                ->toArray();

            foreach ($records as $personId) {
                $this->checked[$personId] = true;
            }
        }
    }

    #[On('togglePerson')]
    public function togglePerson($personId)
    {
        $this->checked[$personId] = !($this->checked[$personId] ?? false);
    }

    #[On('personCreated')]
    public function addNewPerson($personId)
    {
        $this->checked[$personId] = true;
    }

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function save()
    {
        $this->validate([
            'service_name' => 'nullable|string',
            'date' => 'required|date|before_or_equal:today',
        ]);

        $sessionData = [
            'attendance_type_id' => $this->attendanceType->id,
            'date' => $this->date,
        ];

        if ($this->service_name) {
            $sessionData['service_name'] = $this->service_name;
        }

        $session = AttendanceSession::updateOrCreate(
            $sessionData,
            [
                'service_name' => $this->service_name ?: null,
            ]
        );

        // Remove all existing records for this session
        AttendanceRecord::where('attendance_session_id', $session->id)->delete();

        // Add new records
        foreach ($this->checked as $personId => $present) {
            if ($present) {
                AttendanceRecord::create([
                    'attendance_session_id' => $session->id,
                    'person_id' => $personId,
                    'status' => 'present'
                ]);
            }
        }

        session()->flash('success', 'Attendance saved successfully!');
        $this->dispatch('attendanceSaved');
    }

    public function render()
    {
        // Get all people with filters
        $query = Person::with('family')->orderBy('last_name')->orderBy('first_name');

        if ($this->filterFamily) {
            $query->where('family_id', $this->filterFamily);
        }

        // Apply search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('first_name', 'like', "%{$this->search}%")
                  ->orWhere('last_name', 'like', "%{$this->search}%")
                  ->orWhere('contact_number', 'like', "%{$this->search}%");
            });
        }

        $allPeople = $query->get();

        if ($this->filterCategory) {
            $allPeople = $allPeople->filter(function (Person $person) {
                return $person->effective_category === $this->filterCategory;
            })->values();
        }

        // Get counts
        $totalCount = $allPeople->count();
        $presentCount = count(array_filter($this->checked));

        // Calculate category counts from checked people
        $categoryCounts = [];
        foreach ($this->checked as $personId => $isChecked) {
            if ($isChecked) {
                $person = $allPeople->firstWhere('id', $personId);
                if ($person) {
                    $category = $person->effective_category;
                    $categoryCounts[$category] = ($categoryCounts[$category] ?? 0) + 1;
                }
            }
        }

        // Get latest session
        $latestSession = AttendanceSession::where('attendance_type_id', $this->attendanceType->id)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        // Get current session for service name display
        $currentSession = AttendanceSession::where('attendance_type_id', $this->attendanceType->id)
            ->where('date', $this->date)
            ->when($this->service_name, function($q) {
                $q->where('service_name', $this->service_name);
            }, function($q) {
                $q->whereNull('service_name');
            })
            ->first();

        $families = Family::orderBy('family_name')->get();
        $categories = Person::categories();

        return view('livewire.attendance-checkin', compact(
            'allPeople',
            'totalCount',
            'presentCount',
            'categoryCounts',
            'latestSession',
            'currentSession',
            'families',
            'categories'
        ));
    }
}
