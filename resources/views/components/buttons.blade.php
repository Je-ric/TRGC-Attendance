{{--
  Usage:
    <x-buttons type="edit"   wire:click="edit(1)" />
    <x-buttons type="delete" wire:click="delete(1)" label="Remove" />
    <x-buttons type="save" />
    <x-buttons type="cancel" wire:click="close" />
--}}
@props(['type' => 'edit', 'label' => null, 'small' => false])

@php $sz = $small ? 'py-1 px-2.5 text-xs' : 'py-1.5 px-3 text-sm'; @endphp

@if($type === 'edit')
    <button {{ $attributes->merge(['class' => "ui-btn ui-btn-edit $sz"]) }}>
        <i class='bx bx-edit-alt'></i>
        {{ $label ?? 'Edit' }}
    </button>
@elseif($type === 'delete')
    <button {{ $attributes->merge(['class' => "ui-btn ui-btn-delete $sz"]) }}>
        <i class='bx bx-trash'></i>
        {{ $label ?? 'Delete' }}
    </button>
@elseif($type === 'save')
    <button {{ $attributes->merge(['class' => "ui-btn ui-btn-primary $sz"]) }}>
        <i class='bx bx-save'></i>
        {{ $label ?? 'Save' }}
    </button>
@elseif($type === 'cancel')
    <button {{ $attributes->merge(['class' => "ui-btn ui-btn-ghost $sz"]) }}>
        <i class='bx bx-x'></i>
        {{ $label ?? 'Cancel' }}
    </button>
@elseif($type === 'add')
    <button {{ $attributes->merge(['class' => "ui-btn ui-btn-primary $sz"]) }}>
        <i class='bx bx-plus-circle'></i>
        {{ $label ?? 'Add' }}
    </button>
@endif