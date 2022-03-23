<?php

namespace App\Http\Controllers\Auth;

use App\Models\GroupChannel;
use App\Models\Oauth2Member;
use App\Models\User;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\Controller;
use App\Services\Auth\UserService;
use App\Services\Habook\ApiService as ApiHabookService;
use App\Services\Habook\Api2Service as ApiHabook2Service;

class LoginController extends Controller
{
    private $legalRoute = 'exhibition.tbavideo'; // home.main
    private $illegalRoute = 'auth.login';
    private $mySokRoute = null;

    private $userSrv = null;

    //
    public function __construct(UserService $userSrv)
    {
        $this->module     = ['cate' => 'Auth', 'app' => 'Login'];
        $this->mySokRoute = route($this->legalRoute) . '#/myMovie'; // exhibition/tbavideo#/myMovie
        $this->userSrv    = $userSrv;
    }

    //
    public function index(Request $req)
    {
        if (auth()->check()) {
            $to = $req->to;
            if ($to !== null && !filter_var($to, FILTER_VALIDATE_URL)) {
                $to = base64_decode($req->to);
                return redirect($to);
            } else {
                return redirect()->route($this->legalRoute);
            }
        } else {
            $modulePath = $this->parseModulePath($this->module);

            $data = [
                'module' => $modulePath
            ];

            return view($modulePath, $data);
        }
    }

    //
    public function logout()
    {
        auth()->logout();
        \request()->session()->flush();
        return redirect()->route($this->illegalRoute);
    }

    //
    public function loginAsHabook(Request $req)
    {
        auth()->logout();

        $to = is_null($req->to) ? url()->previous() : $req->to;


        if ($to === 'default') {
            $to = route($this->legalRoute);
        }

        $accHabookSrvConfig = Config::get('srvs.habook.account');
        $url                = $accHabookSrvConfig['url'] . '?callback=' . route('auth.login.callbackhabook') . '?to=' . $to;

        return redirect($url);
    }

    //
    public function callbackHabook(Request $req, ApiHabookService $apiHabookSrv)
    {
        auth()->logout();

        $to     = $req->to;
        $ticket = $req->ticket;

        if (!filter_var($to, FILTER_VALIDATE_URL)) {
            $to = base64_decode($req->to);
        }

        if (count(explode('?', $to)) > 1) {
            parse_str(explode('?', $to)[1], $output);
        }

        try {

            $userInfo = $apiHabookSrv->getUserInfo($ticket);
        } catch (Exception $e) {

            return redirect()->route($this->illegalRoute);
        }

        $req->session()->put('idToken', $userInfo->idToken);

        $accId = $req->id;

        $accData = [
            'name'      => $userInfo->name,
            'email'     => $userInfo->email,
            'thumbnail' => $userInfo->profilePicture,
        ];
        $user    = $this->userSrv->loginAsHabook($accId, $accData);

        $this->userSrv->signin($user);

        // This is a default route loggin in from the homepage
        if ($req->to === url('exhibition/tbavideo')) {
            $to = $this->mySokRoute;
        }

        return is_null($to) ? redirect()->route($this->legalRoute) : redirect($to);
    }

    public function userInfo(Request $request, ApiHabookService $apiHabookSrv)
    {

        $idToken = $request->session()->get('idToken');

        $userInfo = $apiHabookSrv->getTicket($idToken);

        $accHabookSrvConfig = Config::get('srvs.habook.account');
        $url                = $accHabookSrvConfig['url'] . '?ticket=' . $userInfo->ticket;

        return $url;
    }

    /**
     * Log in to SOKAPP with ticket
     * This will return a full url, use client-side to redirect
     * @return lluminate\Http\JsonResponse
     */
    public function loginToSokApp(Request $request, ApiHabook2Service $apiHabook2Srv)
    {
        try {
            $idToken   = $request->session()->get('idToken');
            $ssoTicket = $apiHabook2Srv->getSsoTicketFromIdToken($idToken);
            $url       = env('MIX_SOKAPP_URL') . '?' . http_build_query([
                    'code' => $ssoTicket
                ]);
            return response()->json([
                'status' => 1,
                'url'    => $url,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'msg'    => $e->getMessage(),
            ]);
        }
    }

    /**
     * Log in to IES5 with ticket
     * This will return a full url, use client-side to redirect
     * @return lluminate\Http\JsonResponse
     */
    public function loginToIES5(Request $request, ApiHabook2Service $apiHabook2Srv)
    {
        try {
            $idToken      = $request->session()->get('idToken');
            $ssoTicket    = $apiHabook2Srv->getSsoTicketFromIdToken($idToken);
            $ies5Identity = $apiHabook2Srv->getIES5Identity();
            $url          = env('MIX_IES5_URL') . '?' . http_build_query([
                    'code'     => $ssoTicket,
                    'identity' => $ies5Identity,
                ]);
            return response()->json([
                'status' => 1,
                'url'    => $url,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'msg'    => $e->getMessage(),
            ]);
        }
    }

    /**
     *  寧夏學校專用
     * @return \Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function nxLogin()
    {
        auth()->logout();
        $callback = urlencode(url('/nxedu/callback'));
        $url      = "https://cas.nxeduyun.com/sso/login?&service=$callback";

        return redirect($url);
    }

    /**
     * 寧夏學校專用
     * @param Request $request
     * @return \Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function nxCallback(Request $request)
    {
        $ticket   = $request->input('ticket');
        $callback = urlencode(url('/nxedu/callback'));
        $url      = "https://cas.nxeduyun.com/sso/proxyValidate?ticket=$ticket&service=$callback";

        $client    = new Client();
        $response  = $client->get($url);
        $content   = $response->getBody()->getContents();
        $simplexml = simplexml_load_string($content, "SimpleXMLElement", 0, "cas", true);
        $simplexml = (array)$simplexml->authenticationSuccess->attributes;
        $account   = $simplexml['username'];
        $userId    = Oauth2Member::query()->where('oauth2_account', $account)->pluck('users_id')->first();

        $user = User::query()->findOrFail($userId);
        $user->update(['name' => $simplexml['name']]);

        $this->userSrv->signin($user);

        $groupChannel = GroupChannel::query()->where('id', $user->group_channel_id)->first();

        if ($groupChannel) {
            if ($groupChannel instanceof GroupChannel) {
                return redirect("/exhibition/tbavideo#/myChannel/$groupChannel->id");
            }
        }

        return redirect('/exhibition/tbavideo');
    }

    public function authCheck()
    {
        return response()->json(auth()->check());
    }
}
