@props(['messages'])

@if ($messages)
    <div {{ $attributes }}>
        {{ implode(' ', (array) $messages) }}
    </div>
@endif
