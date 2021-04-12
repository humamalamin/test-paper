@component('mail::message')
# Dear {{ __('Sir / Ma\'am') }} {{ $user->name }},

<p>
    You'r account must is verification, please insert unique code :
</p>
<label style="font-size: 40px">{{ $user->number_verified }}</label>
<br>

{{ __('Thank You') }},<br>
{{ config('app.name') }}
@endcomponent
