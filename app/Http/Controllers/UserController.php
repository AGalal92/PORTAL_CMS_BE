<?php

namespace App\Http\Controllers;

use App\Models\User; // Import the User model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    // Fetch all users
    public function index()
    {
        try {
            $users = User::all(); // Fetch all users from the database
            return response()->json($users, 200); // Return users as JSON
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch users', 'error' => $e->getMessage()], 500);
        }
    }

    // Update a user
    public function update(Request $request, $id)
    {
        Log::info('Incoming Request:', $request->all());

        try {
            $user = User::findOrFail($id); // Find the user by ID or throw a 404 error

            // Validate the request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $id,
                'password' => 'nullable|string|min:8', // Optional password field
            ]);

            // Update user fields
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];

            // Only update the password if provided
            if (!empty($validatedData['password'])) {
                $user->password = bcrypt($validatedData['password']);
            }

            $user->save();

            return response()->json(['message' => 'User updated successfully', 'user' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update user', 'error' => $e->getMessage()], 500);
        }
    }


    // Delete a user
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id); // Find the user by ID or throw a 404 error
            $user->delete(); // Delete the user

            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete user', 'error' => $e->getMessage()], 500);
        }
    }
}
