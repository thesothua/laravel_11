<?php
namespace Modules\UserManagement\Services;

use App\ApiResponse;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CustomerService
{
    use ApiResponse;
    public $userModel;
    public $authService;

    public function __construct(User $userModel, AuthService $authService)
    {
        $this->userModel   = $userModel;
        $this->authService = $authService;
    }

    public function collection($request)
    {
        return $this->userModel->customer()->paginate(10);
    }

    public function show($id, $self = false)
    {
        $user = $this->userModel->customer()->with('addresses')->findOrFail($id);
        if ($self) {
            return $user;
        }
        return $this->successResponse('Customer fetch successfully.', $user);
    }

    public function update($request, $id)
    {
        try {
            // Begin the transaction
            DB::beginTransaction();

            // Find the user by ID
            $user = $this->userModel->findOrFail($id);

            // Update user details
            $user->update([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => $request->has('password') ? Hash::make($request->password) : $user->password,
            ]);

            // Update profile if provided
            if ($request->has('profile')) {
                $user->profile()->updateOrCreate([], $request->input('profile'));
            }

            // Sync addresses by ID
            if ($request->has('addresses')) {
                $addresses = $request->input('addresses');
                foreach ($addresses as $addressData) {
                    if (isset($addressData['id'])) {
                        // Update existing address
                        $user->addresses()->where('id', $addressData['id'])->update($addressData);
                    } else {
                        // Create new address
                        $user->addresses()->create($addressData);
                    }
                }
                // Optionally, delete addresses not in the request
                $addressIds = collect($addresses)->pluck('id')->filter()->toArray();
                $user->addresses()->whereNotIn('id', $addressIds)->delete();
            }

            // Update roles if provided
            if ($request->has('roles')) {
                // Sync roles to remove old roles and assign new ones
                $user->syncRoles($request->roles);
            }

            DB::commit();
            return $this->successResponse('User updated successfully.', $this->show($user->id, true));
        } catch (\Throwable $e) {
            // Rollback the transaction on error
            DB::rollBack();

            // Log the error for debugging
            Log::error('Error updating user: ' . $e->getMessage(), ['exception' => $e]);

            // Return a proper error response
            return response()->json([
                'message' => 'Failed to update user.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        // Find the user and delete it
        $user = $this->userModel->findOrFail($id);

        $user->delete();

        return $this->successResponse('User deleted successfully', null);
    }
}
