<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Hiển thị trang thông tin tài khoản
     */
    public function show()
    {
        return view('profile.show');
    }

    /**
     * Cập nhật thông tin tài khoản
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->route('profile.show')->with('success', 'Thông tin tài khoản đã được cập nhật thành công!');
    }

    /**
     * Thay đổi mật khẩu
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.show')->with('success', 'Mật khẩu đã được thay đổi thành công!');
    }

    /**
     * Cập nhật avatar
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        // Xóa avatar cũ nếu có
        if ($user->avatar && file_exists(public_path('images/avatars/' . $user->avatar))) {
            unlink(public_path('images/avatars/' . $user->avatar));
        }

        // Upload avatar mới
        if ($request->hasFile('avatar')) {
            $image = $request->file('avatar');
            $imageName = time() . '_' . $user->id . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/avatars'), $imageName);

            $user->update([
                'avatar' => $imageName
            ]);
        }

        return redirect()->route('profile.show')->with('success', 'Ảnh đại diện đã được cập nhật thành công!');
    }

    /**
     * Xóa avatar
     */
    public function removeAvatar()
    {
        $user = Auth::user();

        if ($user->avatar && file_exists(public_path('images/avatars/' . $user->avatar))) {
            unlink(public_path('images/avatars/' . $user->avatar));
        }

        $user->update([
            'avatar' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ảnh đại diện đã được xóa thành công!'
        ]);
    }
}
