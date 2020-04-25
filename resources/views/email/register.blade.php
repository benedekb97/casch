Kedves {{ $user->name }}!<br>
<br>
Üdvözlünk, a Cards Against Schönherz honlapon!<br>
<br>
Mielőtt elkezdenél játszani aktiválnod kell felhasználódat, amit a következő linken tehetsz meg:<br>
<a href="{{ route('activate.code', ['code' => $user->internal_id]) }}">{{ route('activate.code', ['code' => $user->internal_id]) }}</a><br>
<br>

Üdv,<br>
<i>Cards Against Schönherz</i>
