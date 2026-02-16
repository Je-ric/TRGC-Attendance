<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Person;

class PersonSearch extends Component
{
    public $search = '';
    public $results = [];
    public $selected = [];

    protected $listeners = [
        'personCreated' => '$refresh',
        'attendanceSaved' => '$refresh',
    ];

    public function mount()
    {
        $this->loadResults();
    }

    public function updatedSearch()
    {
        $this->loadResults();
    }

    protected function loadResults()
    {
        if (empty($this->search)) {
            $this->results = Person::with('family')->orderBy('last_name')->orderBy('first_name')->get();
        } else {
            $this->results = Person::with('family')
                ->where(function($query) {
                    $query->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%")
                        ->orWhere('contact_number', 'like', "%{$this->search}%");
                })
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get();
        }
    }

    public function toggle($personId)
    {
        $this->dispatch('togglePerson', personId: $personId);
    }

    public function render()
    {
        return view('livewire.person-search');
    }
}
