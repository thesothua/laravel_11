<?php
namespace Modules\UserManagement\Services;

use App\ApiResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService
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
        return $this->userModel->withoutCustomer()->with(['roles'])->get();
    }

    public function store($request)
    {
        try {
            // Begin the transaction
            DB::beginTransaction();

            // Create the user
            $user = $this->userModel->create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Create profile if provided
            if ($request->has('profile')) {
                $user->profile()->create($request->input('profile'));
            }

            // Create addresses if provided
            if ($request->has('addresses')) {
                $user->addresses()->createMany($request->input('addresses'));
            }

            // Assign roles to the user
            if ($request->has('roles')) {
                foreach ($request->roles as $key => $id) {
                    $user->assignRole($id);
                }
            }

            // $this->authService->sendResetLinkEmail($request);

            DB::commit();
            return $this->createdResponse('User created successfully.', $this->show($user->id, true));
        } catch (\Throwable $e) {
            // Rollback the transaction on error
            DB::rollBack();
            // Log the error for debugging
            Log::error('Error creating user: ' . $e->getMessage(), ['exception' => $e]);
            // Return a proper error response
            return response()->json([
                'message' => 'Failed to create user.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id, $self = false)
    {
        $user = $this->userModel->with('profile', 'roles', 'permissions', 'addresses', 'activities')->findOrFail($id);
        if ($self) {
            return $user;
        }
        return $this->successResponse('User fetch successfully.', $user);
    }

    public function update($request, $id)
    {
        try {
            // Begin the transaction
            DB::beginTransaction();

            $user = $this->userModel->with('profile')->findOrFail($id);

            // Track changes for the user model
            $originalUserData = $user->getOriginal();

            // Update profile if provided
            $profileChanges = [];
            if ($request->has('profile')) {
                $profile        = $user->profile()->updateOrCreate([], $request->input('profile'));
                $profileChanges = $profile->getChanges();
            }

            // Sync addresses by ID
            $addressChanges    = [];
            $deletedAddressIds = [];
            if ($request->has('addresses')) {
                $addresses = $request->input('addresses');
                foreach ($addresses as $addressData) {
                    if (isset($addressData['id'])) {
                        // Update existing address
                        $address = $user->addresses()->where('id', $addressData['id'])->first();
                        if ($address) {
                            $originalAddressData = $address->getOriginal();
                            $address->update($addressData);
                            $addressChanges[] = [
                                'id'      => $address->id,
                                'changes' => $address->getChanges(),
                            ];
                        }
                    } else {
                        // Create new address
                        $newAddress       = $user->addresses()->create($addressData);
                        $addressChanges[] = [
                            'id'      => $newAddress->id,
                            'changes' => $newAddress->getAttributes(),
                        ];
                    }
                }

                // Delete addresses not in the request
                $addressIds       = collect($addresses)->pluck('id')->filter()->toArray();
                $deletedAddresses = $user->addresses()->whereNotIn('id', $addressIds)->get();

                foreach ($deletedAddresses as $deletedAddress) {
                    $deletedAddressIds[] = $deletedAddress->id;
                    $deletedAddress->delete();
                }
            }

            // Update roles if provided
            $roleChanges = [];
            if ($request->has('roles')) {
                $originalRoles = $user->roles->pluck('id')->toArray();
                $newRoles      = $request->roles;
                $user->syncRoles($newRoles);

                $roleChanges = [
                    'removed' => array_diff($originalRoles, $newRoles),
                    'added'   => array_diff($newRoles, $originalRoles),
                ];
            }

            activity('user_activity') // Activity log name
                ->causedBy(Auth::id())    // The user causing the activity
                ->performedOn($user)      // Model on which activity is performed
                ->withProperties([
              
                    'profile_changes'   => $profileChanges,
                    'address_changes'   => $addressChanges,
                    'deleted_addresses' => $deletedAddressIds,
                    'role_changes'      => $roleChanges,
                ])
                ->log('User and Profile were updated.');

            DB::commit();
            return $this->successResponse('User updated successfully.', $this->show($user->id, true));

        } catch (\Throwable $e) {
            // Rollback the transaction on error
            DB::rollBack();

            // Return a proper error response
            return response()->json([
                'message' => 'Failed to update user.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // protected function logSingleActivity($model, $action, $originalData, $updatedData)
    // {
    //     // Compare the original and updated data to track changes
    //     $changes = [];

    //     // dd($originalData, $updatedData);

    //     // Check changes in the user
    //     if (! empty($originalData['user'])) {
    //         $userChanges = array_diff_assoc($updatedData['user'], $originalData['user']);
    //         if (! empty($userChanges)) {
    //             $changes['user'] = $userChanges;
    //         }
    //     }

    //     // Check changes in the profile
    //     if (! empty($originalData['profile'])) {
    //         $profileChanges = array_diff_assoc($updatedData['profile'], $originalData['profile']);
    //         if (! empty($profileChanges)) {
    //             $changes['profile'] = $profileChanges;
    //         }
    //     }

    //     // Log only if there are changes
    //     if (! empty($changes)) {
    //         ActivityLog::create([
    //             'user_id'    => Auth::id(),            // ID of the user performing the action
    //             'action'     => $action,               // Action performed (e.g., 'updated')
    //             'model_type' => get_class($model),     // Type of model (e.g., 'App\Models\User')
    //             'model_id'   => $model->id,            // ID of the model instance
    //             'changes'    => json_encode($changes), // Aggregated changes
    //         ]);
    //     }
    // }

    public function destroy($id)
    {
        // Find the user and delete it
        $user = $this->userModel->findOrFail($id);

        $user->delete();

        return $this->successResponse('User deleted successfully', null);
    }
}
