<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/login',    function () { return view('welcome'); } )->name('login');
Route::get('/register', function () { return view('welcome'); } )->name('register');
*/
// Test
/* Test
Route::get('/auth/test-root',    'Auth\AuthRootController@index'   );
Route::get('/auth/test-admin',   'Auth\AuthAdminController@index'  );
Route::get('/auth/test-manager', 'Auth\AuthManagerController@index');
Route::get('/auth/test-teacher', 'Auth\AuthTeacherController@index');
*/

Route::get('/', function () {
    return redirect()->route('exhibition.tbavideo');
})->name('welcome');

// lang
Route::get('/lang/lang/{locale}', 'Lang\LangController@index')->name('lang.lang');

// auth
Route::get('/auth/check', 'Auth\LoginController@authCheck');
Route::get('/auth/login', 'Auth\LoginController@index')->name('auth.login');
Route::get('/auth/login/logout', 'Auth\LoginController@logout')->name('auth.login.logout');
Route::get('/auth/login/login-as-habook', 'Auth\LoginController@loginAsHabook')->name('auth.login.loginashabook');
Route::get('/auth/login/callback-habook', 'Auth\LoginController@callbackHabook')->name('auth.login.callbackhabook');
Route::get('/auth/user/info', 'Auth\LoginController@userInfo')->name('userInfo');
Route::get('/auth/user/login-to-sokapp', 'Auth\LoginController@loginToSokApp');
Route::get('/auth/user/login-to-ies5', 'Auth\LoginController@loginToIES5');
Route::get('/auth/habook/logout', function () {
    $url = config('srvs.habook.account')['url'] . 'logout?callback=' . route('welcome');
    return view('auth.habook.logout')->with('url', $url);
})->name('auth.habook.logout');

Route::get('/nxedu/callback', 'Auth\LoginController@nxCallback');
Route::get('/nxedu/login', 'Auth\LoginController@nxLogin');

// home
Route::get('/home/about', 'Home\AboutController@index')->name('home.about');

//district
Route::get('/district/{abbr}', 'Exhibition\DistrictController@index')->name('district.tbavideo');
Route::get('/district/tbavideo/get-exhibit-info/', 'Exhibition\DistrictController@getExhibitInfo');
Route::get('/district/tbavideo/get-fields/', 'Exhibition\DistrictController@getField');
Route::get('/exhibition/tbavideo/get-my-group-channel', 'Exhibition\TbaVideoController@getMyGroupChannel');
Route::get('/exhibition/tbavideo/get-district-channel', 'Exhibition\TbaVideoController@getDistrictGroupChannel');
Route::get('/exhibition/tbavideo/get-district-filter', 'Exhibition\TbaVideoController@getDistrictGroupByFilter');
Route::get('/exhibition/tbavideo/get-district-count', 'Exhibition\TbaVideoController@getDistrictGroupByCount');

// exhibition:tbavideo
Route::get('/exhibition/tbavideo', 'Exhibition\TbaVideoController@index')->name('exhibition.tbavideo');
Route::get('/exhibition/tbavideo/watch-as-open', 'Exhibition\TbaVideoController@watchAsOpen')->name('exhibition.tbavideo.watchasopen');
Route::post('/exhibition/tbavideo/filters', 'Exhibition\TbaVideoController@filters');
Route::get('/exhibition/tbavideo/get-exhibit-info', 'Exhibition\TbaVideoController@getExhibitInfo');
Route::get('/exhibition/tbavideo/get-filters', 'Exhibition\TbaVideoController@getFilters');
Route::get('/exhibition/tbavideo/search-keywords', 'Exhibition\TbaVideoController@searchKeywords');
Route::get('/exhibition/tbavideo/check-policy', 'Exhibition\TbaVideoController@checkPolicy');
Route::get('/exhibition/tbavideo/get-content-info', 'Exhibition\TbaVideoController@getContentInfo');
Route::put('/exhibition/tbavideo/set-content-info', 'Exhibition\TbaVideoController@setContentInfo');
Route::post('/exhibition/tbavideo/set-content-info', 'Exhibition\TbaVideoController@setContentInfo');
Route::get('/exhibition/tbavideo/exe-content-annex', 'Exhibition\TbaVideoController@exeContentAnnex');
Route::get('/exhibition/tbavideo/get-tba-video-sect-map', 'Exhibition\TbaVideoController@getTbaVideoSectMap');
Route::get('/exhibition/tbavideo/get-tba-eval-event-opts', 'Exhibition\TbaVideoController@getTbaEvalEventOpts');
Route::get('/exhibition/tbavideo/get-tba-info', 'Exhibition\TbaVideoController@getTbaInfo');
Route::get('/exhibition/tbavideo/get-tba-event-info', 'Exhibition\TbaVideoController@getTbaEventInfo');
Route::get('/exhibition/tbavideo/get-video-info', 'Exhibition\TbaVideoController@getVideoInfo');
Route::get('/exhibition/tbavideo/check-with-habook', 'Exhibition\TbaVideoController@checkWithHabook');
Route::get('/exhibition/tbavideo/content/{contentId}', function (Request $req) {
    return redirect('/exhibition/tbavideo#/content/' . $req->contentId);
})->name('exhibition.tbavideo.content');

// for user
Route::get('/user/tbavideo/get-exhibit-info', 'Exhibition\TbaVideoController@getExhibitInfoByUser');
Route::get('/user/tbavideo/get-comments', 'Exhibition\TbaVideoController@getCommentsByUser');
Route::get('/user/tbavideo/get-user-duties', 'Exhibition\TbaVideoController@getUserDuties');
Route::get('/user/tbavideo/get-user-info', 'Exhibition\TbaVideoController@getUserInfo');
Route::put('/user/tbavideo/set-default-channel', 'Exhibition\TbaVideoController@setDefaultChannel');
Route::apiResource('/member', 'Api\V1\Groups\MemberController');

//Route::get('login/{id}', function ($id) {
//    return auth()->loginUsingId($id);
//});
// modules
Route::middleware(['auth'])->group(function () {
// home
    Route::get('/home/main', 'Home\MainController@index')->name('home.main');

// cms:video
    Route::get('/cms/video', 'Cms\VideoController@index')->name('cms.video');
    Route::get('/cms/video/watch', 'Cms\VideoController@watch')->name('cms.video.watch');
    Route::get('/cms/video/list', 'Cms\VideoController@list');

// cms:tba
    Route::get('/cms/tba', 'Cms\TbaController@index')->name('cms.tba');
    Route::get('/cms/tba/watch', 'Cms\TbaController@watch')->name('cms.tba.watch');
    Route::get('/cms/tba/list', 'Cms\TbaController@list');
    Route::get('/cms/tba/export-tba-evaluate-events', 'Cms\TbaController@exportTbaEvaluateEvents');
    Route::get('cms/tba/export-tba-comments', 'Cms\TbaController@exportTbaComments');

// cms:tbavideo
    Route::get('/cms/tbavideo', 'Cms\TbaVideoController@index')->name('cms.tbavideo');
    Route::get('/cms/tbavideo/watch', 'Cms\TbaVideoController@watch')->name('cms.tbavideo.watch');
    Route::get('/cms/tbavideo/watch-by-cdn', 'Cms\TbaVideoController@watchByCdn')->name('cms.tbavideo.watchbycdn');
    Route::get('/cms/tbavideo/list', 'Cms\TbaVideoController@list');
    Route::get('/cms/tbavideo/get-tba-video-sect-map', 'Cms\TbaVideoController@getTbaVideoSectMap');
    Route::put('/cms/tbavideo/set-tba-video-maps', 'Cms\TbaVideoController@setTbaVideoMaps');
    Route::get('/cms/tbavideo/get-tba-eval-event-opts', 'Cms\TbaVideoController@getTbaEvalEventOpts');
    Route::get('/cms/tbavideo/get-tba-info', 'Cms\TbaVideoController@getTbaInfo');
    Route::get('/cms/tbavideo/get-tba-event-info', 'Cms\TbaVideoController@getTbaEventInfo');
    Route::post('/cms/tbavideo/create-tba-eval-event', 'Cms\TbaVideoController@createTbaEvalEvent');
    Route::post('/cms/tbavideo/update-tba-eval-event', 'Cms\TbaVideoController@updateTbaEvalEvent');
    Route::delete('/cms/tbavideo/delete-tba-eval-event', 'Cms\TbaVideoController@deleteTbaEvalEvent');
    Route::get('/cms/tbavideo/get-video-info', 'Cms\TbaVideoController@getVideoInfo');

// exhibition:tbavideo
    Route::get('/exhibition/tbavideo/watch', 'Exhibition\TbaVideoController@watch')->name('exhibition.tbavideo.watch');
    Route::put('/exhibition/tbavideo/set-tba-video-maps', 'Exhibition\TbaVideoController@setTbaVideoMaps');
    Route::get('/exhibition/tbavideo/get-playlist-info', 'Exhibition\TbaVideoController@getPlaylistInfo');
    Route::get('/exhibition/tbavideo/get-my-channel-info', 'Exhibition\TbaVideoController@getMyChannelInfo');
    Route::get('/exhibition/tbavideo/get-my-group-channel', 'Exhibition\TbaVideoController@getMyGroupChannel');
    Route::get('/exhibition/tbavideo/get-my-group-filter', 'Exhibition\TbaVideoController@getMyGroupByFilter');
    Route::get('/exhibition/tbavideo/get-my-group-count', 'Exhibition\TbaVideoController@getGroupChannelTotalByCount');
    Route::get('/exhibition/tbavideo/get-my-movies', 'Exhibition\TbaVideoController@getMyMovies'); // movie list with pagination
    Route::get('/exhibition/tbavideo/get-my-movie', 'Exhibition\TbaVideoController@getMyMovie'); // get a single movie
    Route::get('/exhibition/tbavideo/get-filter-movie', 'Exhibition\TbaVideoController@getFilterMovie'); // get filter  movie
    Route::get('/exhibition/tbavideo/get-my-observed-movies', 'Exhibition\TbaVideoController@getMyObservedMovies'); // get my observed movies
    Route::get('/exhibition/tbavideo/get-recommended-movies', 'Exhibition\TbaVideoController@getRecommendedMovies'); // get recommended movies
    Route::get('/exhibition/tbavideo/get-latest-movies', 'Exhibition\TbaVideoController@getLatestMovies'); // get latest movies
    Route::get('/exhibition/tbavideo/get-group-info', 'Exhibition\TbaVideoController@getGroupInfo');
    Route::get('/exhibition/tbavideo/get-hists', 'Exhibition\TbaVideoController@getHists');
    Route::delete('/exhibition/tbavideo/delete-hists', 'Exhibition\TbaVideoController@deleteHists');
    Route::get('/exhibition/tbavideo/get-subject-choices', 'Exhibition\TbaVideoController@getSubjectChoices');
    Route::get('/exhibition/tbavideo/get-submission-choices', 'Exhibition\TbaVideoController@getSubmissionChoices');
    Route::post('/exhibition/tbavideo/create-tba-eval-event', 'Exhibition\TbaVideoController@createTbaEvalEvent');
    Route::post('/exhibition/tbavideo/update-tba-eval-event', 'Exhibition\TbaVideoController@updateTbaEvalEvent');
    Route::delete('/exhibition/tbavideo/delete-tba-eval-event', 'Exhibition\TbaVideoController@deleteTbaEvalEvent');

// group:list
    Route::get('/group/list', 'Group\ListController@index')->name('group.list');
    Route::get('/group/list/list', 'Group\ListController@list');

// group:main
    Route::get('/group/{groupId}/main', 'Group\MainController@index')->name('group.main');
    Route::get('/group/{groupId}/main/channel/{channelId}/contents', 'Group\MainController@channelContents');

// group:manage
    Route::get('/group/{groupId}/manage', 'Group\ManageController@index')->name('group.manage');
    Route::get('/group/{groupId}/manage/members', 'Group\ManageController@members');
    Route::get('/group/{groupId}/manage/candidates', 'Group\ManageController@candidates');
    Route::get('/group/{groupId}/manage/get-member', 'Group\ManageController@getMember');
    Route::put('/group/{groupId}/manage/set-member', 'Group\ManageController@setMember');
    Route::get('/group/{groupId}/manage/channels', 'Group\ManageController@channels');
    Route::get('/group/{groupId}/manage/get-channel', 'Group\ManageController@getChannel');
    Route::put('/group/{groupId}/manage/set-channel', 'Group\ManageController@setChannel');
    Route::post('/group/{groupId}/manage/create-channel', 'Group\ManageController@createChannel');
    Route::get('/group/{groupId}/manage/channel/{channelId}/contents', 'Group\ManageController@channelContents');
    Route::get('/group/{groupId}/manage/channel/{channelId}/get-content', 'Group\ManageController@getChannelContent');
    Route::put('/group/{groupId}/manage/channel/{channelId}/set-content', 'Group\ManageController@setChannelContent');
    Route::get('/group/manage/set-member-as-expert', 'Group\ManageController@setMemberAsExpert');
    Route::get('/group/{groupId}/users', 'Management\GroupController@getGroupUsers');
    Route::post('/group/{groupId}/users', 'Management\GroupController@userJoinForGroup');

// group:watch:tbavideo
    Route::get('/group/{groupId}/watch/channel/{channelId}/tbavideo', 'Group\Watch\TbaVideoController@index')->name('group.watch.tbavideo');
    Route::get('/group/{groupId}/watch/channel/{channelId}/tbavideo/get-tba-video-sect-map', 'Group\Watch\TbaVideoController@getTbaVideoSectMap');
    Route::put('/group/{groupId}/watch/channel/{channelId}/tbavideo/set-tba-video-maps', 'Group\Watch\TbaVideoController@setTbaVideoMaps');
    Route::get('/group/{groupId}/watch/channel/{channelId}/tbavideo/get-tba-eval-event-opts', 'Group\Watch\TbaVideoController@getTbaEvalEventOpts');
    Route::get('/group/{groupId}/watch/channel/{channelId}/tbavideo/get-tba-info', 'Group\Watch\TbaVideoController@getTbaInfo');
    Route::get('/group/{groupId}/watch/channel/{channelId}/tbavideo/get-tba-event-info', 'Group\Watch\TbaVideoController@getTbaEventInfo');
    Route::post('/group/{groupId}/watch/channel/{channelId}/tbavideo/create-tba-eval-event', 'Group\Watch\TbaVideoController@createTbaEvalEvent');
    Route::post('/group/{groupId}/watch/channel/{channelId}/tbavideo/update-tba-eval-event', 'Group\Watch\TbaVideoController@updateTbaEvalEvent');
    Route::delete('/group/{groupId}/watch/channel/{channelId}/tbavideo/delete-tba-eval-event', 'Group\Watch\TbaVideoController@deleteTbaEvalEvent');
    Route::get('/group/{groupId}/watch/channel/{channelId}/tbavideo/get-video-info', 'Group\Watch\TbaVideoController@getVideoInfo');

// management:group
    Route::get('/management/group', 'Management\GroupController@index')->name('management.group');
    Route::get('/management/group/info', 'Management\GroupController@info')->name('management.group.info');
    Route::get('/management/group/manage', 'Management\GroupController@manage')->name('management.group.manage');
    Route::get('/management/group/list', 'Management\GroupController@list');
    Route::get('/management/group/get-group', 'Management\GroupController@getGroup');
    Route::put('/management/group/set-group', 'Management\GroupController@setGroup');
    Route::post('/management/group/create-group', 'Management\GroupController@createGroup');
    Route::get('/management/group/search-users', 'Management\GroupController@searchUsers');
    Route::get('/management/group/set-review-status', 'Management\GroupController@setGroupReviewStatus');

// management:module
    Route::get('/management/module', 'Management\ModuleController@index')->name('management.module');
    Route::get('/management/module/info', 'Management\ModuleController@info')->name('management.module.info');
    Route::get('/management/module/manage', 'Management\ModuleController@manage')->name('management.module.manage');
    Route::get('/management/module/list', 'Management\ModuleController@list');

// management:role
    Route::get('/management/role', 'Management\RoleController@index')->name('management.role');
    Route::get('/management/role/info', 'Management\RoleController@info')->name('management.role.info');
    Route::get('/management/role/manage', 'Management\RoleController@manage')->name('management.role.manage');
    Route::get('/management/role/list', 'Management\RoleController@list');
    Route::get('/management/role/get-role', 'Management\RoleController@getRole');
    Route::post('/management/role/create-role', 'Management\RoleController@createRole');

// management:user
    Route::get('/management/user', 'Management\UserController@index')->name('management.user');
    Route::get('/management/user/info', 'Management\UserController@info')->name('management.user.info');
    Route::get('/management/user/manage', 'Management\UserController@manage')->name('management.user.manage');
    Route::get('/management/user/list', 'Management\UserController@list');
    Route::get('/management/user/get-user', 'Management\UserController@getUser');
    Route::put('/management/user/set-user', 'Management\UserController@setUser');

// watch
    Route::get('/watch/tbavideo/{contentId}', function (Request $req) {
        return redirect()->route('exhibition.tbavideo.content', ['contentId' => $req->contentId]);
        //return redirect()->route('exhibition.tbavideo.watch', ['contentId' => $req->contentId]);
    })->name('watch.tbavideo');


    //  分享影片
    Route::post('/tbas/share/video/group/{channel_id}', 'Api\V1\Tbas\TbaController@shareVideo');
    Route::get('/tbas/share/video/group/{channel_id}', 'Api\V1\Tbas\TbaController@show');
    Route::delete('/tbas/share/video/group/{channel_id}', 'Api\V1\Tbas\TbaController@deleteShareVideo');
    Route::post('/tbas/uploadVideo/group/{channel_id}', 'Api\V1\Tbas\TbaController@uploadVideoToGroup');
    Route::put('/tbas/timepoints/{tbaId}', 'Api\V1\Tbas\TbaController@setTbaTimePoints');

    // Content
    Route::delete('tbas/channels/{channelId}/content/{contentId}', 'Api\V1\Tbas\TbaController@destroy');

    // 影片
    Route::apiResource('/tba/video', 'Api\V1\Videos\VideoController');

    // 我的通知清單
    Route::get('/exhibition/notification', 'Api\V1\Notification\NotificationController@show');
    // 分組
    Route::apiResource('/division', 'Api\V1\Division\DivisionController');
    Route::apiResource('/score', 'Api\V1\Score\ScoreController');

    // Favorite
    Route::apiResource('/favorite', 'Api\V1\Favorite\FavoriteController');

    // Comments (New Types)
    Route::apiResource('/comments', 'Api\V1\Comment\CommentController');
    Route::apiResource('/comment-tag-types', 'Api\V1\Comment\CommentTagTypeController');

    // Observation Classes
    Route::apiResource('/observation-classes', 'Api\V1\ObservationClasses\ObservationClassController');
});

Route::get('/management/group/isMember', 'Group\ManageController@isMember');
Route::post('/management/group/joinMemberGroup', 'Group\ManageController@joinMemberGroup');

//導入位置
Route::get('Player', 'Exhibition\TbaVideoController@goToPlayer');
Route::post('get-player-url', 'Exhibition\TbaVideoController@getPlayerSharedURL');

// File
Route::get('/exists/{tbaId}', 'File\FileController@exists');
// Ticket
Route::get('getTicket', 'Api\V1\HaBook\HaBookController@getTicket');

// Export
Route::post('/export/lesson', 'Exhibition\TbaVideoController@exportExcel');
Route::get('/export/lesson', 'Exhibition\TbaVideoController@generateExportUrl');

// exhibition:tbaVideo
Route::get('/exhibition/tbavideo/get-observation-info', 'Exhibition\TbaVideoController@getTbaInfoAndComments');
Route::get('/exhibition/tbavideo/get-channel-info', 'Exhibition\TbaVideoController@getChannel');
