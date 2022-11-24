@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (config('app.app_url_logo') !== '' || config('app.app_url_logo') !== null)
<img src="{{ config('app.app_url_logo') }}" class="logo" alt="{{ config('app.name') }} Logo" width="48" height="48">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
