<?php

namespace App\Http\Controllers\Exhibition;

use App\Helpers\Custom\GlobalPlatform;
use App\Libraries\Lang\Lang;
use App\Services\App\UserService;
use App\Services\DistrictServices;
use App\Services\Exhibition\ExhibitionService;
use App\Services\Exhibition\TbaVideoService as TbaVideoExhibitionService;
use App\Types\Cms\CmsType;
use App\Types\Exhibition\SetType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class DistrictController extends Controller
{
    /**
     * @var ExhibitionService
     */
    private $exhibitionSrv;
    /**
     * @var TbaVideoExhibitionService
     */
    private $tbaVideoExhibitionSrv;
    /**
     * @var DistrictServices
     */
    private $districtServices;

    public function __construct(ExhibitionService $exhibitionSrv, TbaVideoExhibitionService $tbaVideoExhibitionSrv, DistrictServices $districtServices)
    {
        $this->module                = ['cate' => 'District', 'app' => 'tbavideo'];
        $this->exhibitionSrv         = $exhibitionSrv;
        $this->tbaVideoExhibitionSrv = $tbaVideoExhibitionSrv;
        $this->districtServices      = $districtServices;
    }

    public function index(Request $request, UserService $userSrv, $abbr)
    {
        $user = auth()->check() ? $userSrv->getUser(auth()->id()) : null;
        if ($user) {
            $groupId = $user->group_channel_id
                ? GlobalPlatform::convertChannelIdToGroupId($user->group_channel_id)
                : null;
            $user->group_channel_name = $groupId
                ? $user->groups()->where('id', $groupId)->first()->channels->first()->name
                : null;
        }

        $modulePath                = $this->parseModulePath($this->module, 'index');
        $convertAbbrToDistrictInfo = GlobalPlatform::convertAbbrToDistrictInfo($abbr);
        $convertByLangString       = Lang::getConvertByLangString(app()->getLocale());

        $data = [
            'module'  => $modulePath,
            'globals' => [
                'user'      => $user,
                'abbr'      => $abbr,
                'name'      => $convertAbbrToDistrictInfo->districtLang()->firstWhere('locales_id', $convertByLangString)->name ?? null,
                'thumbnail' => $convertAbbrToDistrictInfo->thumbnail ? '/storage/district/' . $convertAbbrToDistrictInfo->id . '/' . $convertAbbrToDistrictInfo->thumbnail : null
            ],
        ];

        return view($modulePath, $data);
    }

    public function getExhibitInfo(Request $req): \Illuminate\Http\JsonResponse
    {

        $abbr     = $req->input('abbr');
        $groupIds = GlobalPlatform::convertDistrictToGroupId($abbr);
        $data     = [
//            'cms'      => [],
            'channel'  => [],
            'district' => [],
        ];

//        $data['cms']['hits']     = $this->tbaVideoExhibitionSrv->getRanks(12, [['col' => 'hits', 'dir' => 'desc']], $groupIds);
//        $data['cms']['news']     = $this->tbaVideoExhibitionSrv->getRanks(12, [['col' => 'lecture_date', 'dir' => 'desc'], ['col' => 'created_at', 'dir' => 'DESC']], $groupIds);
        $data['channel']['excs'] = $this->exhibitionSrv->getGroupChannelSetsByDistrict(CmsType::TbaVideo, SetType::Excellent, $groupIds);
//        $data['channel']['hits'] = []; // 待修改:訂閱數為參考依據
        $data['district'] = $this->districtServices->getDistrictCount($abbr, $groupIds);

        return Response::json([
            'status' => true,
            'data'   => $data
        ]);
    }

    public function getField(Request $request)
    {
        $abbr = $request->input('abbr');

        $data = [
            'gradeFields'   => $this->districtServices->getGrade(),
            'subjectFields' => $this->districtServices->getSubjectField($abbr),
        ];

        return Response::json([
            'status' => true,
            'data'   => $data
        ]);
    }
}
