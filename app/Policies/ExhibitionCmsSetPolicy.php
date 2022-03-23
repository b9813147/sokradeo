<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ExhibitionCmsSet;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExhibitionCmsSetPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the exhibitionCmsSet.
     *
     * @param  \App\Models\User  $user
     * @param  \App\ExhibitionCmsSet  $exhibitionCmsSet
     * @return mixed
     */
    public function view(User $user, ExhibitionCmsSet $exhibitionCmsSet)
    {
        return ExhibitionCmsSet::where([
                'cms_id'   => $exhibitionCmsSet->cms_id,
                'cms_type' => $exhibitionCmsSet->cms_type,
        ])->exists();
    }

    /**
     * Determine whether the user can create exhibitionCmsSets.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the exhibitionCmsSet.
     *
     * @param  \App\Models\User  $user
     * @param  \App\ExhibitionCmsSet  $exhibitionCmsSet
     * @return mixed
     */
    public function update(User $user, ExhibitionCmsSet $exhibitionCmsSet)
    {
        //
    }

    /**
     * Determine whether the user can delete the exhibitionCmsSet.
     *
     * @param  \App\Models\User  $user
     * @param  \App\ExhibitionCmsSet  $exhibitionCmsSet
     * @return mixed
     */
    public function delete(User $user, ExhibitionCmsSet $exhibitionCmsSet)
    {
        //
    }
}
