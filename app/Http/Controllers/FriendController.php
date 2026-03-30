<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\FriendDto;
use App\Support\Http\JodiRequest;

class FriendController extends Controller
{
    public function getAll(JodiRequest $request)
    {
        return response()->json(FriendDto::collect($this->user()->friends->all()));
    }
}
