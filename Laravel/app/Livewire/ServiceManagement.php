<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Service;
use Carbon\Carbon;

class ServiceManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $filterCategory = 'all';
    public $filterSpecial = 'all';

    protected $paginationTheme = 'tailwind';

    public function render()
    {
        $query = Service::with('attendanceRecords.person')
            ->orderByDesc('date')
            ->orderByDesc('time');

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        if ($this->filterCategory !== 'all') {
            $query->where('service_category', $this->filterCategory);
        }

        if ($this->filterSpecial === 'special') {
            $query->where('is_special_event', true);
        } elseif ($this->filterSpecial === 'regular') {
            $query->where('is_special_event', false);
        }

        $services = $query->paginate(10);

        return view('livewire.service-management', [
            'services' => $services,
        ]);
    }

    public function deleteService(Service $service)
    {
        $service->delete();
        $this->dispatch('lw-toast', type: 'success', message: 'Service deleted successfully.');
    }

    public function quickSundayService()
    {
        $nextSunday = Carbon::now()->next(Carbon::SUNDAY);
        
        $service = Service::create([
            'name' => 'Sunday Morning Service',
            'date' => $nextSunday->format('Y-m-d'),
            'time' => '09:00',
            'location' => 'Main Sanctuary',
            'is_special_event' => false,
            'service_category' => 'Sunday Morning',
            'notes' => null,
        ]);

        return redirect()->route('services.checkin', $service);
    }
}
