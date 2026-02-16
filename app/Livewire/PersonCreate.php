<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Person;
use App\Models\Family;
use Carbon\Carbon;

class PersonCreate extends Component
{
    public $show = false;

    public $first_name;
    public $last_name;
    public $birthdate;
    public $category;
    public $address;
    public $contact_number;
    public $family_id;

    public $editing = false;
    public $personId = null;

    protected $listeners = [
        'editPerson' => 'handleEditPerson',
    ];

    public function handleEditPerson($id)
    {
        $this->edit($id);
    }
    protected function rules()
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'birthdate' => 'nullable|date|before_or_equal:today',
            'category' => 'nullable|in:' . implode(',', Person::categories()),
            'address' => 'nullable|string',
            'contact_number' => 'nullable|string',
            'family_id' => 'nullable|exists:families,id',
        ];
    }

    public function open()
    {
        $this->resetValidation();
        $this->first_name = '';
        $this->last_name = '';
        $this->birthdate = '';
        $this->category = '';
        $this->address = '';
        $this->contact_number = '';
        $this->family_id = '';
        $this->editing = false;
        $this->personId = null;
        $this->show = true;
    }


    public function edit($id)
    {
        $person = Person::findOrFail($id);
        $this->personId = $person->id;
        $this->first_name = $person->first_name;
        $this->last_name = $person->last_name;
        $this->birthdate = $person->birthdate ? $person->birthdate->format('Y-m-d') : '';
        $this->category = $person->category ?? '';
        $this->address = $person->address ?? '';
        $this->contact_number = $person->contact_number ?? '';
        $this->family_id = $person->family_id ? (string)$person->family_id : '';

        $this->editing = true;
        $this->show = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'first_name' => trim($this->first_name),
            'last_name' => trim($this->last_name),
            'birthdate' => $this->birthdate ?: null,
            'category' => $this->category ?: null,
            'address' => $this->address ? trim($this->address) : null,
            'contact_number' => $this->contact_number ? trim($this->contact_number) : null,
            'family_id' => $this->family_id ? (int)$this->family_id : null,
        ];

        if ($this->editing) {
            $person = Person::findOrFail($this->personId);
            $person->update($data);
            $this->dispatch('personUpdated', personId: $person->id);
            $this->editing = false;
        } else {
            $person = Person::create($data);
            $this->dispatch('personCreated', personId: $person->id);
        }

        $this->resetFields();
        $this->show = false;
    }

    protected function resetFields()
    {
        $this->first_name = '';
        $this->last_name = '';
        $this->birthdate = '';
        $this->category = '';
        $this->address = '';
        $this->contact_number = '';
        $this->family_id = null;
        $this->personId = null;
    }

    public function render()
    {
        return view('livewire.person-create', [
            'families' => Family::orderBy('family_name')->get(),
            'categories' => Person::categories(),
            'today' => Carbon::today()->format('Y-m-d'),
        ]);
    }
}
