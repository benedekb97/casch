Kedves {{ $user->name }}!<br>
<br>
Új hibajelentést adtak le!<br>
<br>
<table>
    <tr>
        <th>Oldal</th>
        <td>{{ $report->page }}</td>
    </tr>
    <tr>
        <th>Leírás</th>
        <td>{{ $report->description }}</td>
    </tr>
    <tr>
        <th>Stack trace</th>
        <td>
            {{ $report->trace ? $report->trace : '<i>Nem küldött</i>' }}
        </td>
    </tr>
    <tr>
        <th>Beküldő</th>
        <td>
            {{ $report->user ? $report->user->name : '<i>Ismeretlen felhasználó</i>' }}
        </td>
    </tr>
</table>
<br>

Üdv,<br>
<i>Cards Against Schönherz</i>
