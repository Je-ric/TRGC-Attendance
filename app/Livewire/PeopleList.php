<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Person;
use App\Models\Family;

class PeopleList extends Component
{
    public $viewMode = 'flat'; // 'flat', 'family', 'category'
    public $filterCategory = '';
    public $filterFamily = null;

    protected $listeners = [
        'personCreated' => '$refresh',
        'personUpdated' => '$refresh',
        'personDeleted' => '$refresh',
    ];

    public function deletePerson($id)
    {
        $person = Person::findOrFail($id);
        $person->delete();

        $this->dispatch('personDeleted');
    }
    public function setViewMode($mode)
    {
        $this->viewMode = $mode;
    }

    public function render()
    {
        $query = Person::with('family')->orderBy('last_name')->orderBy('first_name');

        if ($this->filterFamily) {
            $query->where('family_id', $this->filterFamily);
        }

        $people = $query->get();

        if ($this->filterCategory) {
            $people = $people->filter(function (Person $person) {
                return $person->effective_category === $this->filterCategory;
            })->values();
        }

        $families = Family::withCount('people')->orderBy('family_name')->get();
        $categories = Person::categories();

        return view('livewire.people-list', compact('people', 'families', 'categories'));
    }
}
