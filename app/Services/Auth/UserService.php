<?php /** @noinspection ALL */

namespace App\Services\Auth;

use Carbon\Carbon;
use File;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repositories\UserRepository;
use App\Types\Auth\AccountType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Melihovv\Base64ImageDecoder\Base64ImageDecoder;

class UserService
{
    private $userRepo = null;

    //
    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    //
    public function loginAsHabook($accId, $accData)
    {
        try {
            $user = $this->userRepo->getUserByAcc(AccountType::Habook, $accId);

            if ($accData['thumbnail']) {
                $dataUri              = $accData['thumbnail'];
                $decoder              = new Base64ImageDecoder($dataUri, ['jpeg', 'jpg', 'png', 'gif']);
                $fileName             = 'thumbnail' . "." . $decoder->getFormat();
                $file                 = $decoder->getDecodedContent();
                $accData['thumbnail'] = $fileName;

                Storage::makeDirectory('public/user/' . $user->id);
                File::put(storage_path('app/public/user/' . $user->id) . '/' . $fileName, $file);

                if (!$user->roles()->exists()) {
                    $this->userRepo->setUser($user->id, $accData, [6]);
                } else {
                    $this->userRepo->setUser($user->id, $accData, $user->roles()->pluck('id'));
                }
            } else {
                if (!$user->roles()->exists()) {
                    $this->userRepo->setUser($user->id, $accData, [6]);
                } else {
                    $this->userRepo->setUser($user->id, $accData, $user->roles()->pluck('id'));
                }
            }

            if (!$user->roles()->exists()) {
                $this->userRepo->setUser($user->id, [], [6]);
            }
            return $user;
        } catch (ModelNotFoundException $e) {
            if ($accData['thumbnail']) {
                $dataUri              = $accData['thumbnail'];
                $decoder              = new Base64ImageDecoder($dataUri, ['jpeg', 'jpg', 'png', 'gif']);
                $fileName             = 'thumbnail' . "." . $decoder->getFormat();
                $file                 = $decoder->getDecodedContent();
                $accData['thumbnail'] = $fileName;

                $user = $this->userRepo->createUserByAcc(AccountType::Habook, $accId, $accData);

                Storage::makeDirectory('public/user/' . $user->id);
                File::put(storage_path('app/public/user/' . $user->id) . '/' . $fileName, $file);
                $this->userRepo->setUser($user->id, [], [6]);

                return $this->userRepo->getUserByAcc(AccountType::Habook, $accId);
            }
            $user = $this->userRepo->createUserByAcc(AccountType::Habook, $accId, $accData);
            $this->userRepo->setUser($user->id, [], [6]);


            return $this->userRepo->getUserByAcc(AccountType::Habook, $accId);
        }
    }

    //
    public function signin($user)
    {
        auth()->loginUsingId($user->id);
        $this->bindRoles($user->roles);
        \Log::info('login', ['user_id' => $user->id, 'loginTime' => Carbon::now()->format('Y-m-d h:i:s')]);
    }

    //
    public function bindRoles($roles)
    {
        $types = $roles->map(function ($v) {
            return $v->type;
        });
        $this->bindRolesByType($types);
    }

    //
    public function bindRolesByType($roleTypes)
    {
        $roleModuleMap = Config::get('module_role');

        $modules = [];
        foreach ($roleTypes as $v) {
            array_push($modules, $roleModuleMap[$v]);
        }

        $modules = collect($modules)->mapToGroups(function ($roleModules) {
            $map = [];
            foreach ($roleModules as $k => $v) {
                $map[$k] = $v;
            }
            return $map;
        });

        $modules->transform(function ($v, $k) {
            $v = collect($v);
            if ($v->containsStrict(true)) {
                return true;
            }
            return $v->flatten()->unique();
        });

        session()->put('roles', $roleTypes->toArray());
        session()->put('modules', $modules->toArray());

        return $modules;
    }

}
