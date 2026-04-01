<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Person;
use App\Models\Family;
use Carbon\Carbon;

class PersonCreate extends Component
{
    public $editing  = false;
    public $personId = null;

    public $first_name        = '';
    public $last_name         = '';
    public $birthdate         = '';
    public $gender            = '';
    public $civil_status      = '';
    public $category          = '';
    public $membership_status = 'Regular Attendee';
    public $joined_date       = '';
    public $date_of_baptism   = '';
    public $address           = '';
    public $contact_number    = '';
    public $email             = '';
    public $family_id         = '';
    public $notes             = '';

    protected $listeners = ['editPerson' => 'edit'];

    protected function rules(): array
    {
        return [
            'first_name'        => 'required|string|max:100',
            'last_name'         => 'required|string|max:100',
            'birthdate'         => 'nullable|date|before_or_equal:today',
            'gender'            => 'nullable|in:' . implode(',', Person::GENDERS),
            'civil_status'      => 'nullable|in:' . implode(',', Person::CIVIL_STATUSES),
            'category'          => 'nullable|in:' . implode(',', Person::CATEGORIES),
            'membership_status' => 'nullable|in:' . implode(',', Person::MEMBERSHIP_STATUSES),
            'joined_date'       => 'nullable|date|before_or_equal:today',
            'date_of_baptism'   => 'nullable|date|before_or_equal:today',
            'address'           => 'nullable|string',
            'contact_number'    => 'nullable|string|max:30',
            'email'             => 'nullable|email|max:150',
            'family_id'         => 'nullable|exists:families,id',
            'notes'             => 'nullable|string',
        ];
    }

    public function open()
    {
        $this->resetValidation();
        $this->reset(['first_name','last_name','birthdate','gender','civil_status','category',
                      'joined_date','date_of_baptism','address','contact_number','email','family_id','notes','personId']);
        $this->membership_status = 'Regular Attendee';
        $this->editing = false;
        $this->dispatch('open-modal', id: 'person-modal');
    }

    public function edit($id)
    {
        $p = Person::findOrFail($id);
        $this->personId          = $p->id;
        $this->first_name        = $p->first_name;
        $this->last_name         = $p->last_name;
        $this->birthdate         = $p->birthdate?->format('Y-m-d') ?? '';
        $this->gender            = $p->gender ?? '';
        $this->civil_status      = $p->civil_status ?? '';
        $this->category          = $p->category ?? '';
        $this->membership_status = $p->membership_status ?? 'Regular Attendee';
        $this->joined_date       = $p->joined_date?->format('Y-m-d') ?? '';
        $this->date_of_baptism   = $p->date_of_baptism?->format('Y-m-d') ?? '';
        $this->address           = $p->address ?? '';
        $this->contact_number    = $p->contact_number ?? '';
        $this->email             = $p->email ?? '';
        $this->family_id         = $p->family_id ? (string) $p->family_id : '';
        $this->notes             = $p->notes ?? '';
        $this->editing           = true;
        $this->dispatch('open-modal', id: 'person-modal');
    }

    public function save()
    {
        $this->validate();

        $data = [
            'first_name'        => trim($this->first_name),
            'last_name'         => trim($this->last_name),
            'birthdate'         => $this->birthdate ?: null,
            'gender'            => $this->gender ?: null,
            'civil_status'      => $this->civil_status ?: null,
            'category'          => $this->category ?: null,
            'membership_status' => $this->membership_status ?: 'Regular Attendee',
            'joined_date'       => $this->joined_date ?: null,
            'date_of_baptism'   => $this->date_of_baptism ?: null,
            'address'           => $this->address ? trim($this->address) : null,
            'contact_number'    => $this->contact_number ? trim($this->contact_number) : null,
            'email'             => $this->email ? trim($this->email) : null,
            'family_id'         => $this->family_id ? (int) $this->family_id : null,
            'notes'             => $this->notes ? trim($this->notes) : null,
        ];

        if ($this->editing) {
            $person = Person::findOrFail($this->personId);
            $person->update($data);
            $this->dispatch('lw-toast', type: 'success', message: $person->full_name . ' updated.');
            $this->dispatch('personUpdated', personId: $person->id);
        } else {
            $person = Person::create($data);
            $this->dispatch('lw-toast', type: 'success', message: $person->full_name . ' added.');
            $this->dispatch('personCreated', personId: $person->id);
        }

        $this->reset(['first_name','last_name','birthdate','gender','civil_status','category',
                      'joined_date','date_of_baptism','address','contact_number','email','family_id','notes','personId']);
        $this->membership_status = 'Regular Attendee';
        $this->editing = false;
        $this->dispatch('close-modal', id: 'person-modal');
    }

    public function render()
    {
        return view('livewire.person-create', [
            'families'          => Family::orderBy('family_name')->get(),
            'categories'        => Person::CATEGORIES,
            'membershipStatuses'=> Person::MEMBERSHIP_STATUSES,
            'genders'           => Person::GENDERS,
            'civilStatuses'     => Person::CIVIL_STATUSES,
            'today'             => Carbon::today()->format('Y-m-d'),
        ]);
    }
}
