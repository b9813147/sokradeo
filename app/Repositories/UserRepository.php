<?php

namespace App\Repositories;

use App\Notifications\EventChannel;
use App\Services\NotificationMessageService;
use Carbon\Carbon;
use LogicException;
use App\Models\User;
use App\Types\Auth\AccountType;

class UserRepository extends BaseRepository
{
    protected $model;

    //
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    //
    public function list($page = 1)
    {
        return User::paginate(null, ['*'], 'page', $page);
    }

    //
    public function getUser($userId)
    {
        $userModel = User::with([
            'roles',
            'notifications' => function ($q) {
                $q->orderBy('created_at', 'desc');
            },
            'groups'        => function ($group) {
                $group->with('channels');
            },
            'districtUser'  => function ($q) {
                $q->with([
                    'district' => function ($q) {
                        $q->with('districtLang');
                    }
                ]);
                $q->select('user_id', 'districts_id', 'member_duty', 'member_status');
            }
        ])->findOrFail($userId);


        return $userModel;
    }

    //
    public function getUserByAcc($accType, $accId)
    {
        $colAcc = AccountType::getDbColNameByAccType($accType);
        $user   = User::where($colAcc, $accId)->firstOrFail();
        return $this->getUser($user->id);
    }

    //
    public function getUserByClientAcc($clientId, $clientUser)
    {
        $user = User::where('client_id', $clientId)->where('client_user', $clientUser)->firstOrFail();
        return $this->getUser($user->id);
    }

    //
    public function setUser($userId, $userData, $roles)
    {
        $user = User::findOrFail($userId);
        $user->fill($userData);
        $user->save();
        $user->roles()->sync($roles);
    }

    //
    public function createUserByAcc($accType, $accId, $accData)
    {
        $colAcc = AccountType::getDbColNameByAccType($accType);
        if (User::where($colAcc, $accId)->exists()) {
            throw new LogicException('account is already exist');
        }
        $accData[$colAcc] = $accId;
        $user             = User::create($accData);

        app(NotificationMessageService::class)->findWhere((['status' => 1, ['validity', '>=', Carbon::now()->format('Y-m-d')]]))
            ->each(function ($q) use ($user) {
                $user->notify(new EventChannel((array)json_decode($q->content)));
            });

        return $this->getUser($user->id);
    }

    //
    public function createUserByClientAcc($clientId, $clientUser, $accData)
    {
        if (User::where('client_id', $clientId)->where('client_user', $clientUser)->exists()) {
            throw new LogicException('account is already exist');
        }
        $accData['client_id']   = $clientId;
        $accData['client_user'] = $clientUser;
        $user                   = User::create($accData);
        return $this->getUser($user->id);
    }

    //
    public function getUserOrCreateByAcc($accType, $accId, $accData)
    {
        $colAcc = AccountType::getDbColNameByAccType($accType);
        $user   = User::updateOrCreate([$colAcc => $accId], $accData);
        return $this->getUser($user->id);
    }

    //
    public function getUserOrCreateByClientAcc($clientId, $clientUser, $accData)
    {
        $user = User::updateOrCreate(['client_id' => $clientId, 'client_user' => $clientUser], $accData);
        return $this->getUser($user->id);
    }

    //
    public function searchUsers($name)
    {
        if (empty($name)) {
            return [];
        }

        return User::select('id', 'name')->where('name', 'like', $name . '%')->get();
    }

    /**
     * Get Basic User Info for Tba Player
     * @param $userId
     * @return App\Models\User
     */
    public function getUserInfo($userId)
    {
        return $this->model
            ->select('id', 'name')
            ->where('id', $userId)
            ->firstOrFail();
    }

    /**
     * 使用者加入頻道
     * @param $user_id
     * @param $group_id
     * @param array $attributes
     * @return bool
     */
    public function userJoinForGroup($user_id, $group_id, array $attributes = []): bool
    {
        $isSuccessful = true;
        try {
            $model = $this->model->query()->findOrFail($user_id)->groups()->where('group_id', $group_id);
            if (!$model->exists()) {
                $model->attach($group_id, $attributes);
            }
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return false;
        }
        return $isSuccessful;
    }

    /**
     * Set User group channel id
     * @param $userId
     * @param $groupChannelId
     * @return User
     */
    public function setUserGroupChannelId(int $userId, int $groupChannelId): User
    {
        $user = $this->model->query()->findOrFail($userId);
        $user->group_channel_id = $groupChannelId;
        $user->save();
        return $user;
    }
}
