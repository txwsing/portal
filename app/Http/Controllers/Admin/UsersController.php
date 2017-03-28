<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\DeleteUser;
use App\Users\User;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function ban(User $user)
    {
        if ($user->isAdmin()) {
            $this->error('admin.users.cannot_ban_other_admins');
        } else {
            $user->ban();

            $this->success('admin.users.banned', ['name' => $user->name()]);
        }

        return redirect()->route('admin.users.show', $user->username());
    }

    public function unban(User $user)
    {
        $user->unban();

        $this->success('admin.users.unbanned', ['name' => $user->name()]);

        return redirect()->route('admin.users.show', $user->username());
    }

    public function delete(User $user)
    {
        if ($user->isAdmin()) {
            $this->error('admin.users.cannot_delete_other_admins');

            return redirect()->route('admin.users.show', $user->username());
        } else {
            $this->dispatchNow(new DeleteUser($user));

            $this->success('admin.users.deleted', ['name' => $user->name()]);
        }

        return redirect()->route('admin');
    }
}