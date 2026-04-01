<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Family;
use App\Models\Person;

class FamilyManagement extends Component
{
    public $editing  = false;
    public $familyId = null;

    public $family_name    = '';
    public $address        = '';
    public $contact_person = '';

    // For delete confirmation modal
    public $confirmDeleteId   = null;
    public $confirmDeleteName = '';

    protected $rules = [
        'family_name'    => 'required|string|max:255',
        'address'        => 'nullable|string',
        'contact_person' => 'nullable|string',
    ];

    protected $listeners = [
        'editFamily'    => 'edit',
        'familyCreated' => '$refresh',
        'familyUpdated' => '$refresh',
        'familyDeleted' => '$refresh',
    ];

    public function open()
    {
        $this->resetValidation();
        $this->reset(['family_name', 'address', 'contact_person', 'familyId']);
        $this->editing = false;
        $this->dispatch('open-modal', id: 'family-form-modal');
    }

    public function edit($id)
    {
        $family = Family::findOrFail($id);
        $this->familyId       = $family->id;
        $this->family_name    = $family->family_name;
        $this->address        = $family->address ?? '';
        $this->contact_person = $family->contact_person ?? '';
        $this->editing        = true;
        $this->dispatch('open-modal', id: 'family-form-modal');
    }

    public function save()
    {
        $this->validate();

        $data = [
            'family_name'    => $this->family_name,
            'address'        => $this->address ?: null,
            'contact_person' => $this->contact_person ?: null,
        ];

        if ($this->editing) {
            Family::findOrFail($this->familyId)->update($data);
            $this->dispatch('lw-toast', type: 'success', message: '"' . $this->family_name . '" updated.');
            $this->dispatch('familyUpdated');
        } else {
            Family::create($data);
            $this->dispatch('lw-toast', type: 'success', message: '"' . $this->family_name . '" added.');
            $this->dispatch('familyCreated');
        }

        $this->reset(['family_name', 'address', 'contact_person', 'familyId']);
        $this->editing = false;
        $this->dispatch('close-modal', id: 'family-form-modal');
    }

    public function confirmDelete($id)
    {
        $family = Family::findOrFail($id);
        $this->confirmDeleteId   = $family->id;
        $this->confirmDeleteName = $family->family_name;
        $this->dispatch('open-modal', id: 'family-delete-modal');
    }

    public function deleteFamily()
    {
        if (!$this->confirmDeleteId) return;

        $family = Family::findOrFail($this->confirmDeleteId);
        $name   = $family->family_name;
        $family->delete();

        $this->reset(['confirmDeleteId', 'confirmDeleteName']);
        $this->dispatch('close-modal', id: 'family-delete-modal');
        $this->dispatch('lw-toast', type: 'success', message: '"' . $name . '" removed.');
        $this->dispatch('familyDeleted');
    }

    public function render()
    {
        $families = Family::with('people')->withCount('people')->orderBy('family_name')->get();
        $categories = Person::categories();

        $categoryCounts = array_fill_keys($categories, 0);
        Person::all()->each(function (Person $person) use (&$categoryCounts) {
            $cat = $person->effective_category;
            if (isset($categoryCounts[$cat])) $categoryCounts[$cat]++;
        });

        return view('livewire.family-management', compact('families', 'categoryCounts', 'categories'));
    }
}
