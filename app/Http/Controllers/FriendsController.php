<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\FriendDto;
use Illuminate\Http\Request;

class FriendsController extends Controller
{
    public function index(Request $request)
    {
        return inertia('CurrentUser/Friends', [
            'friends' => FriendDto::collect($this->user()->friends->all()),
        ]);
    }
}
