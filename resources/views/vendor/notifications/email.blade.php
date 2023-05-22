<script>
    document.addEventListener('DOMContentLoaded', function () {
        var showPasswordButton = document.getElementById('show-password');
        var passwordElement = document.getElementById('password');

        showPasswordButton.addEventListener('click', function () {
            if (passwordElement.style.display === 'none') {
                passwordElement.style.display = 'inline';
                showPasswordButton.innerText = 'Ocultar contraseña';
            } else {
                passwordElement.style.display = 'none';
                showPasswordButton.innerText = 'Mostrar contraseña';
            }
        });
    });
</script>

<x-mail::message>
{{-- Greeting --}}
@if (! empty($greeting))
{!! $greeting !!}
@else
@if ($level === 'error')
# @lang('Whoops!')
@else
# @lang('Hello!')
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{!! $line !!}

@endforeach

{{-- Action Button --}}
@isset($actionText)
<?php
    $color = match ($level) {
        'success', 'error' => $level,
        default => 'primary',
    };
?>
<x-mail::button :url="$actionUrl" :color="$color">
{{ $actionText }}
</x-mail::button>
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{!! $line !!}

@endforeach

<div class="linear-divisor-salutation mt-3 mb-2"></div>
{{-- Salutation --}}
@if (! empty($salutation))
{!! $salutation !!}
@else
@lang('Regards'),<br>
@endif<br>

<span class="typography-greeting-text font-italic">{{ config('app.name') }}</span>

{{-- Subcopy --}}
@isset($actionText)
<x-slot:subcopy>
@lang(
    "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser:',
    [
        'actionText' => $actionText,
    ]
) <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
</x-slot:subcopy>
@endisset
</x-mail::message>
