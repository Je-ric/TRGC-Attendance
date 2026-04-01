<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Person;
use App\Models\Family;

class PeopleList extends Component
{
    public $viewMode          = 'flat';
    public $filterCategory    = '';
    public $filterFamily      = null;
    public $filterMembership  = '';
    public $search            = '';

    public $confirmDeleteId   = null;
    public $confirmDeleteName = '';

    protected $listeners = [
        'personCreated' => '$refresh',
        'personUpdated' => '$refresh',
        'personDeleted' => '$refresh',
    ];

    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function confirmDelete($id)
    {
        $person = Person::findOrFail($id);
        $this->confirmDeleteId   = $person->id;
        $this->confirmDeleteName = $person->full_name;
        $this->dispatch('open-modal', id: 'person-delete-modal');
    }

    public function deletePerson()
    {
        if (!$this->confirmDeleteId) return;
        $person = Person::findOrFail($this->confirmDeleteId);
        $name   = $person->full_name;
        $person->delete();
        $this->reset(['confirmDeleteId', 'confirmDeleteName']);
        $this->dispatch('close-modal', id: 'person-delete-modal');
        $this->dispatch('lw-toast', type: 'success', message: "$name removed.");
        $this->dispatch('personDeleted');
    }

    public function render()
    {
        $query = Person::with('family')->orderBy('last_name')->orderBy('first_name');

        if ($this->filterFamily) {
            $query->where('family_id', $this->filterFamily);
        }

        if ($this->filterMembership) {
            $query->where('membership_status', $this->filterMembership);
        }

        if ($this->search) {
            $query->where(fn($q) =>
                $q->where('first_name', 'like', "%{$this->search}%")
                  ->orWhere('last_name', 'like', "%{$this->search}%")
                  ->orWhere('contact_number', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
            );
        }

        $people = $query->get();

        if ($this->filterCategory) {
            $people = $people->filter(fn(Person $p) => $p->effective_category === $this->filterCategory)->values();
        }

        return view('livewire.people-list', [
            'people'           => $people,
            'families'         => Family::withCount('people')->orderBy('family_name')->get(),
            'categories'       => Person::CATEGORIES,
            'membershipStatuses' => Person::MEMBERSHIP_STATUSES,
        ]);
    }
}
