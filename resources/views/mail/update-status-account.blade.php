@component('mail::message')
# Dear {{ __('Sir / Ma\'am') }} {{ $user->name }},

<p>
    You'r account has been activated, please login use :
</p>
<table>
    <tr>
        <td>E-mail</td>
        <td>: {{ $user->email }}</td>
    </tr>
    <tr>
        <td>Password</td>
        <td>: xxxxxxx</td>
    </tr>
</table>
<br>

{{ __('Thank You') }},<br>
{{ config('app.name') }}
@endcomponent
