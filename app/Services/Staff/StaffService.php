<?php

namespace App\Services\Staff;

use App\Schemas\Staff\StaffSchema;
use App\Commons\Http\ServiceResponse;
use App\Models\Role;
use App\Models\Staff;
use App\Models\User;
use App\Schemas\Staff\StaffQuery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StaffService implements StaffServiceInterface
{
    public function findAll(StaffQuery $queryParams): ServiceResponse
    {
        try {
            $queryParams->hydrateQuery();
            $query = User::with(['roles', 'staff.outlet'])
                ->whereRelation('roles', 'name', '=', 'staff')
                // ->when($queryParams->getParam(), function ($q) use ($queryParams) {
                //     /** @var Builder $q */
                //     return $q->where('name', 'LIKE', "%{$queryParams->getParam()}%");
                // })
                ->orderBy('username', 'ASC');
            $data = $query->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
            return ServiceResponse::statusOK("successfully get staff", $data);
        } catch (\Throwable $e) {
            dd($e->getMessage());
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findByID($id): ServiceResponse
    {
        try {
            $staff = User::with(['staff.outlet'])
                ->where('id', '=', $id)
                ->first();
            if (!$staff) {
                return ServiceResponse::notFound("staff not found");
            }
            return ServiceResponse::statusOK("successfully get staff", $staff);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
    public function create(StaffSchema $schema): ServiceResponse
    {
        DB::beginTransaction();
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();
            $roleId = "3a30c13f-4d48-4eae-a5e4-589777587fa6";
            $role = Role::where('id', $roleId)->first();
            if (!$role) {
                return ServiceResponse::notFound("role not found");
                DB::rollBack();
            }
            $dataUser = [
                'username' => $schema->getUsername(),
                'password' => Hash::make($schema->getPassword()),
            ];

            $user = User::create($dataUser);
            if (!$user->hasRole($role)) {
                $user->assignRole($role);
            }

            $dataProfile = [
                'user_id' => $user->id,
                'outlet_id' => $schema->getOutletId(),
                'name' => $schema->getName(),
                'phone' => $schema->getPhone(),
            ];
            Staff::create($dataProfile);
            DB::commit();
            return ServiceResponse::statusCreated("successfully create staff");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function patch($id, StaffSchema $schema): ServiceResponse
    {
        DB::beginTransaction();
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();

            $user = User::with(['staff'])
                ->where('id', '=', $id)
                ->first();

            if (!$user) {
                return ServiceResponse::notFound("user not found");
            }

            $dataUser = [
                'username' => $schema->getUsername(),
            ];

            if ($schema->getPassword()) {
                $dataUser['password'] = Hash::make($schema->getPassword());
            }
            $user->update($dataUser);
            $dataProfile = [
                'user_id' => $user->id,
                'outlet_id' => $schema->getOutletId(),
                'name' => $schema->getName(),
                'phone' => $schema->getPhone(),
            ];
            $user->staff()->update($dataProfile);
            DB::commit();
            return ServiceResponse::statusCreated("successfully update staff");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function delete($id): ServiceResponse
    {
        DB::beginTransaction();
        try {
            $user = User::with(['staff'])
                ->where('id', '=', $id)
                ->first();

            if (!$user) {
                return ServiceResponse::notFound("user not found");
            }
            $user->staff()->delete();
            $user->delete();
            DB::commit();
            return ServiceResponse::statusOK("successfully delete user");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
