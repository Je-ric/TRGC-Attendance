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
    public $barangay       = '';
    public $contact_person = '';
    public $contact_number = '';
    public $notes          = '';

    public $confirmDeleteId   = null;
    public $confirmDeleteName = '';

    protected $rules = [
        'family_name'    => 'required|string|max:255',
        'address'        => 'nullable|string',
        'barangay'       => 'nullable|string|max:100',
        'contact_person' => 'nullable|string|max:100',
        'contact_number' => 'nullable|string|max:30',
        'notes'          => 'nullable|string',
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
        $this->reset(['family_name','address','barangay','contact_person','contact_number','notes','familyId']);
        $this->editing = false;
        $this->dispatch('open-modal', id: 'family-form-modal');
    }

    public function edit($id)
    {
        $f = Family::findOrFail($id);
        $this->familyId        = $f->id;
        $this->family_name     = $f->family_name;
        $this->address         = $f->address ?? '';
        $this->barangay        = $f->barangay ?? '';
        $this->contact_person  = $f->contact_person ?? '';
        $this->contact_number  = $f->contact_number ?? '';
        $this->notes           = $f->notes ?? '';
        $this->editing         = true;
        $this->dispatch('open-modal', id: 'family-form-modal');
    }

    public function save()
    {
        $this->validate();

        $data = [
            'family_name'    => $this->family_name,
            'address'        => $this->address ?: null,
            'barangay'       => $this->barangay ?: null,
            'contact_person' => $this->contact_person ?: null,
            'contact_number' => $this->contact_number ?: null,
            'notes'          => $this->notes ?: null,
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

        $this->reset(['family_name','address','barangay','contact_person','contact_number','notes','familyId']);
        $this->editing = false;
        $this->dispatch('close-modal', id: 'family-form-modal');
    }

    public function confirmDelete($id)
    {
        $f = Family::findOrFail($id);
        $this->confirmDeleteId   = $f->id;
        $this->confirmDeleteName = $f->family_name;
        $this->dispatch('open-modal', id: 'family-delete-modal');
    }

    public function deleteFamily()
    {
        if (!$this->confirmDeleteId) return;
        $f    = Family::findOrFail($this->confirmDeleteId);
        $name = $f->family_name;
        $f->delete();
        $this->reset(['confirmDeleteId', 'confirmDeleteName']);
        $this->dispatch('close-modal', id: 'family-delete-modal');
        $this->dispatch('lw-toast', type: 'success', message: '"' . $name . '" removed.');
        $this->dispatch('familyDeleted');
    }

    public function render()
    {
        $families = Family::with('people')->withCount('people')->orderBy('family_name')->get();
        $categories = Person::CATEGORIES;

        $categoryCounts = array_fill_keys($categories, 0);
        Person::all()->each(function (Person $p) use (&$categoryCounts) {
            $cat = $p->effective_category;
            if (isset($categoryCounts[$cat])) $categoryCounts[$cat]++;
        });

        return view('livewire.family-management', compact('families', 'categoryCounts', 'categories'));
    }
}
