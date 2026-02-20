@extends('layouts.app')

@section('content')
    <div class="space-y-5">
        <div>
            <h2 class="brand-font text-3xl text-[#6B0F1A]">Family Management</h2>
            <p class="text-sm text-slate-600 mt-1">Maintain family groupings, contacts, and household members.</p>
        </div>
        <livewire:family-management />
    </div>
@endsection
