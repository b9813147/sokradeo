<?php

// tests
Route::apiResource('tests', 'Api\Test\MainController');

// educationalstages
Route::apiResource('educationalstages', 'Api\V1\EducationalStages\EducationalStageController');

// groups:group
Route::apiResource('groups', 'Api\V1\Groups\GroupController');
Route::post('groups/get-school-group', 'Api\V1\Groups\GroupController@getSchoolGroup');
// groups:member
Route::apiResource('groups/{groupId}/member', 'Api\V1\Groups\MemberController');
// groups:channel
Route::apiResource('groups/{groupId}/channel', 'Api\V1\Groups\ChannelController');
// groups:content
Route::apiResource('groups/channel/{channelId}/content', 'Api\V1\Groups\ContentController');
Route::post('groups/content/schoolcode', 'Api\V1\Groups\ContentController@storeWithSchoolCode');
Route::post('groups/content/member-channel', 'Api\V1\Groups\ContentController@storeWithMemberChannel');
Route::post('groups/content/batch', 'Api\V1\Groups\ContentController@setGradesAndSubjectsByBatch');

// locales
Route::apiResource('locales', 'Api\V1\Locales\LocaleController');

// subjectfields
Route::apiResource('subjectfields', 'Api\V1\SubjectFields\SubjectFieldController');

// tbas
Route::apiResource('tbas', 'Api\V1\Tbas\TbaController');

// tbavideos
Route::apiResource('tbavideos',                 'Api\V1\TbaVideos\TbaVideoController');
Route::apiResource('tbavideos/{tbaId}/sectmap', 'Api\V1\TbaVideos\SectMapController' );

// videos
Route::apiResource('videos', 'Api\V1\Videos\VideoController');

// contest
Route::apiResource('contests/member', 'Api\V1\Contests\MemberController');
Route::post('contests/member-duty', 'Api\V1\Contests\MemberController@setGroupUserDuty');
Route::post('contests/submission', 'Api\V1\Contests\ContentController@getSubmissionStatus');
Route::post('contests/member-channel', 'Api\V1\Contests\MemberController@updateMemberChannel');

// Groups_Subject
Route::post('groups/subject', 'Api\V1\Groups\GroupSubjectController@store');
Route::post('groups/import', 'Api\V1\Groups\GroupSubjectController@import');
