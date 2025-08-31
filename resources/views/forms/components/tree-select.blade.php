@php
    use Filament\Support\Facades\FilamentView;

    $isDisabled = $isDisabled();
    $statePath = $getStatePath();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <x-filament::input.wrapper
        :disabled="$isDisabled"
        :valid="! $errors->has($statePath)"
        :attributes="
            \Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())
                ->class(['filament-forms-tree-select-component'])
        "
    >
        <x-filament::input.select
            :disabled="$isDisabled"
            :id="$getId()"
            :autofocus="$isAutofocused()"
            :required="$isRequired() && ! $isConcealed()"
            :state-path="$statePath"
            :placeholder="$getPlaceholder()"
            :valid="! $errors->has($statePath)"
            :attributes="
                \Filament\Support\prepare_inherited_attributes($getExtraInputAttributeBag())
                    ->merge([
                        'wire:model' . ($shouldLiveValidate() ? '.live' : '') => $statePath,
                    ], escape: false)
            "
        >
            @if ($getPlaceholder() !== null)
                <option value="">{{ $getPlaceholder() }}</option>
            @endif

            @foreach ($getOptions() as $value => $label)
                <option
                    value="{{ $value }}"
                    @selected($isOptionSelected($value))
                >
                    {{ $label }}
                </option>
            @endforeach
        </x-filament::input.select>
    </x-filament::input.wrapper>
</x-dynamic-component>