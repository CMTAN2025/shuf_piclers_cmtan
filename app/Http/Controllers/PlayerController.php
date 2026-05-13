<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function index()
    {
        return view('index', ['players' => Player::all()]);
    }
    
    public function store(Request $request)
    {
        $player = Player::create(['name' => $request->name, 'dupr' => $request->dupr ?: null]);
        return response()->json($player);
    }

    public function destroy(Player $player)
    {
        $player->delete();
        return response()->json(['ok' => true]);
    }

    public function shuffle(Request $request)
    {
        $selected = Player::whereIn('id', $request->players ?? [])->get()->toArray();
        shuffle($selected);
        return response()->json(array_chunk($selected, 2));
    }
}
