@component('mail::message')
# {{ $topic }}

Your student account temporary password is <code>{{ $new_password }}</code>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
