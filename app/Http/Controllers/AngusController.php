<?php

namespace App\Http\Controllers;

use App\Models\Angus;
use App\Models\Response;
use Illuminate\Http\Request;
use Str;

class AngusController extends Controller
{
    public function receive(Request $request)
    {
        $angus = new Angus();

        $response = Response::all()->last();

        $response = $response ? $response->response : Str::random(10);

        $angus->response = json_encode(['StatusInterval' => $response]);
        $angus->request = $request->getContent();
        $angus->ip = $request->getClientIp();
        $angus->save();

        return response()->json(['StatusInterval' => $response]);
    }

    public function list()
    {
        $angus = Angus::paginate(5);
        $response = Response::all()->last();
        $response = $response ? $response->response : '10 random karakter';

        return view('angus', [
            'angus' => $angus,
            'response' => $response
        ]);
    }

    public function setResponse(Request $request)
    {
        $response = new Response();

        $response->response = $request->input('response');
        $response->save();

        return redirect()->route('angus.index');
    }

    public function setEmpty()
    {
        $responses = Response::all();
        /** @var Response $response */
        foreach($responses as $response)
        {
            $response->delete();
        }

        return redirect()->route('angus.index');
    }
}
