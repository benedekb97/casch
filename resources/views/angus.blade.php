<!DOCTYPE html>
<html lang="angus">
    <head>
        <title>Enkusz egy qki</title>
    </head>
    <body>
        <form action="{{ route('angus.response') }}" method="POST">
            {{ csrf_field() }}
            <input type="text" placeholder="Mi legyen a response" name="response" id="response">
            <input type="submit" value="Mentés">
            <p>Most épp ez van beállítva: <b>{{ $response }}</b> |
                <a href="{{ route('angus.empty') }}">Törlés</a></p>
        </form>
        Kérések:<br>
        @if($angus->count() == 0)
            <i>Nem lett még kérés küldve</i>
        @endif
        @foreach($angus as $request)<hr>
            Request:
            <pre>
                {{ $request->request }}
            </pre><br>
            Response:
            <pre>
                {{ $request->response }}
            </pre><br>
            IP: {{ $request->ip }}
        @endforeach
        <hr>
    {{ $angus->links() }}
    </body>
</html>
