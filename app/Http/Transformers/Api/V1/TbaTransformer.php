<?php

namespace App\Http\Transformers\Api\V1;

use App\Http\Transformers\Transformer;
use App\Models\User;
use App\Types\Auth\AccountType;
use App\Types\Src\SrcType;
use App\Types\Tba\AnnexType;

class TbaTransformer extends Transformer
{
    public function execute() {

        $this->setEval();
        $this->setAnnex();
    }

    private function setEval() {

        if (! $this->req->exists('eval')) {
            return;
        }

        $eval = $this->req->eval;
        $this->req->merge(['eval' => null]);

        foreach ($eval['users'] as $i => $userInfo) {
            if (! AccountType::check($userInfo['accType'])) {
                continue;
            }

            $conds = [];
            if ($userInfo['accType'] === AccountType::Client) {
                $conds['client_id'  ] = $this->req->client->id;
                $conds['client_user'] = $userInfo['accUser'];
            } else {
                $accCol = AccountType::getDbColNameByAccType($userInfo['accType']);
                $conds[$accCol] = $userInfo['accUser'];
            }
            $user = User::firstOrCreate($conds, ['name' => $userInfo['name'], 'email' => $userInfo['email']]);
            $eval['users'][$i]['id'] = $user->id;
        }

        $this->req->merge(['eval' => $eval]);
    }

    private function setAnnex() {

        if (! $this->req->exists('annex')) {
            return;
        }

        $annex = $this->req->annex;
        $this->req->merge(['annex' => null]);

        $list = [];
        foreach ($annex['list'] as $v) {
            $v = collect($v);

            $srcType = AnnexType::getSrcType($v->get('type'));
            if (! $srcType) {
                continue;
            }

            $data = null;
            switch ($srcType) {
                case SrcType::File:
                    $data = $v->only(['name', 'ext', 'path']);
                    break;
                case SrcType::Uri:
                    $data = $v->only(['url']);
                    break;
                default:
                    continue 2;
            }

            array_push($list, [
                    'type' => $v->get('type'),
                    'name' => $v->get('name'),
                    'data' => $data->toArray(),
            ]);
        }

        $annex['list'] = $list;
        $this->req->merge(['annex' => $annex]);
    }
}
