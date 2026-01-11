<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\FriendDto;
use Illuminate\Http\Request;

class FriendsController extends Controller
{
    public function getAll(Request $request)
    {
        return response()->json(FriendDto::collect($this->user()->friends->all()));
    }
}
