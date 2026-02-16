<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Family;
use App\Models\Person;

class FamilyManagement extends Component
{
    public $show = false;
    public $editing = false;
    public $familyId = null;

    public $family_name;
    public $address;
    public $contact_person;

    protected $rules = [
        'family_name' => 'required|string|max:255',
        'address' => 'nullable|string',
        'contact_person' => 'nullable|string',
    ];

    protected $listeners = [
        'editFamily' => 'handleEditFamily',
        'familyCreated' => '$refresh',
        'familyUpdated' => '$refresh',
        'familyDeleted' => '$refresh',
    ];

    public function open()
    {
        $this->resetValidation();
        $this->resetFields();
        $this->editing = false;
        $this->familyId = null;
        $this->show = true;
    }

    public function handleEditFamily($id)
    {
        $this->edit($id);
    }

    public function edit($id)
    {
        $family = Family::findOrFail($id);
        $this->familyId = $family->id;
        $this->family_name = $family->family_name;
        $this->address = $family->address;
        $this->contact_person = $family->contact_person;
        $this->editing = true;
        $this->show = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'family_name' => $this->family_name,
            'address' => $this->address,
            'contact_person' => $this->contact_person,
        ];

        if ($this->editing) {
            $family = Family::findOrFail($this->familyId);
            $family->update($data);
            $this->dispatch('familyUpdated');
        } else {
            Family::create($data);
            $this->dispatch('familyCreated');
        }

        $this->resetFields();
        $this->show = false;
    }

    public function deleteFamily($id)
    {
        $family = Family::findOrFail($id);
        $family->delete();
        $this->dispatch('familyDeleted');
    }

    protected function resetFields()
    {
        $this->family_name = '';
        $this->address = '';
        $this->contact_person = '';
        $this->familyId = null;
    }

    public function render()
    {
        $families = Family::with('people')->withCount('people')->orderBy('family_name')->get();
        $categories = Person::categories();

        $categoryCounts = [];
        foreach ($categories as $category) {
            $categoryCounts[$category] = 0;
        }

        Person::whereNotNull('birthdate')
            ->orWhereNotNull('category')
            ->get()
            ->each(function (Person $person) use (&$categoryCounts, $categories) {
                $category = $person->effective_category;
                if (in_array($category, $categories, true)) {
                    $categoryCounts[$category] = ($categoryCounts[$category] ?? 0) + 1;
                }
            });

        return view('livewire.family-management', compact('families', 'categoryCounts', 'categories'));
    }
}
