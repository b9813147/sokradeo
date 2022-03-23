<?php

namespace App\Services;

use App\Repositories\ObservationClassRepository;
use App\Repositories\ObservationUserRepository;

use App\Helpers\Custom\GlobalPlatform;
use App\Helpers\Code\AlphaNumeric;
use App\Types\ObservationClass\StatusType;
use App\Types\ObservationClass\ContentPublicType;

class ObservationClassService
{
    use GlobalPlatform;
    use AlphaNumeric;

    protected $obsrvClassRepository;
    protected $obsrvUserRepository;

    public function __construct(ObservationClassRepository $obsrvClassRepository, ObservationUserRepository $obsrvUserRepository)
    {
        $this->obsrvClassRepository = $obsrvClassRepository;
        $this->observationUserRepository = $obsrvUserRepository;
    }

    /**
     * Get the current user's observation class (standby, starting, ended)
     * @param object $authUser
     * @param bool $endedClass
     * @return ObservationClass || null
     */
    public function getCurrentUserObsrvClass(object $authUser, bool $endedClass = false)
    {
        // If include ended is true, get the current user's ended observation class
        $endedQuerySymbol = $endedClass ? '=' : '!=';

        // Get the current user's active observation class
        $obsrvActiveClass = $this->obsrvClassRepository
            ->getAllByUserIdAsQuery($authUser->id)
            ->where('status', $endedQuerySymbol, StatusType::ENDED_VAL)
            ->with([
                'group' => function ($query) {
                    $query->select('id', 'name');
                },
                'groupChannel' => function ($query) {
                    $query->select('id', 'name');
                },
                'rating' => function ($query) {
                    $query->select('id', 'name');
                },
                'groupSubjectField' => function ($query) {
                    $query->select('id', 'alias');
                }
            ]);

        // Get the current user's observation class
        $obsrvActiveClass = $obsrvActiveClass->latest()->first();

        // If found, return the observation class
        // Else, return null
        return $obsrvActiveClass ?? null;
    }

    /**
     * Create an Observation Class
     * @param object $authUser
     * @param array $obsrvData
     * @return ObservationClass
     */
    public function createObsrvClass(object $authUser, array $obsrvData)
    {
        // Determine locale id
        $lang = new \App\Libraries\Lang\Lang();
        $localeId = $lang->getConvertByLangString(\App::getLocale());

        // Data sanitization
        $this->sanitizeObsrvData($obsrvData);

        $attrs = [
            'user_id' => $authUser->id,
            'binding_number' => $this->generateBindingNumber($authUser->id),
            'classroom_code' => $this->generateClassroomCode(),
            'pin_code' => $this->generatePinCode(),
            'duration' => $obsrvData['duration'],
            'status' => StatusType::getStatusValue(StatusType::STANDBY), // ready
            'timestamp' => null,
            'group_id' => GlobalPlatform::convertChannelIdToGroupId($obsrvData['channelId']),
            'channel_id' => $obsrvData['channelId'],
            'name' => $obsrvData['lessonName'],
            'content_public' => ContentPublicType::getContentPublicValue($obsrvData['lessonSharing']),
            'teacher' => $authUser->name,
            'habook_id' => $authUser->habook,
            'rating_id' => $obsrvData['classificationId'],
            'group_subject_field_id' => $obsrvData['subjectId'],
            'grade_id' => $obsrvData['grade'],
            'lecture_date' => now(),
            'locale_id' => $localeId,
        ];
        $this->obsrvClassRepository->createObsrvClass($attrs);

        return $this->getCurrentUserObsrvClass($authUser);
    }

    /**
     * Start an existing Observation Class
     * @param object $authUser
     * @param int $obsrvClassId
     * @return ObservationClass
     */
    public function startObsrvClass(object $authUser, int $obsrvClassId)
    {
        // End all existing observation classes for the user
        $this->endAllObsrvClasses($authUser, $obsrvClassId);

        $obsrvClass = $this->obsrvClassRepository->getById($obsrvClassId);

        // Check if the user is the owner of the observation class
        if ($authUser->id !== $obsrvClass->user_id) {
            return null;
        }

        // Check if the class is in standby status
        if ($obsrvClass->status !== StatusType::getStatusValue(StatusType::STANDBY)) {
            return null;
        }

        // Update the class by
        //  - Setting the status to 'S' (starting)
        //  - Setting the timestamp
        $attrs = [
            'status' => StatusType::getStatusValue(StatusType::STARTING), // started
            'timestamp' => time(),
        ];
        $this->obsrvClassRepository->updateObsrvClass($obsrvClass->id, $attrs);

        return $this->getCurrentUserObsrvClass($authUser);
    }

    /**
     * End an existing Observation Class
     * @param object $authUser
     * @param int $obsrvClassId
     * @return ObservationClass
     */
    public function endObsrvClass(object $authUser, int $obsrvClassId)
    {
        $obsrvClass = $this->obsrvClassRepository->getById($obsrvClassId);

        // Check if the user is the owner of the observation class
        if ($authUser->id !== $obsrvClass->user_id) {
            return null;
        }

        // Check if the class is in starting status
        if ($obsrvClass->status !== StatusType::getStatusValue(StatusType::STARTING)) {
            return null;
        }

        // Update the class by
        //  - Setting the status to 'E' (ended)
        $attrs = [
            'status' => StatusType::getStatusValue(StatusType::ENDED),
        ];
        $this->obsrvClassRepository->updateObsrvClass($obsrvClass->id, $attrs);

        return $this->getCurrentUserObsrvClass($authUser, true);
    }

    /**
     * Add Extra time to the currently starting observation class
     * @param object $authUser
     * @param int $obsrvClassId
     * @param int $extraSeconds
     * @return ObservationClass
     */
    public function addExtraTime(object $authUser, int $obsrvClassId, int $extraSeconds)
    {
        $obsrvClass = $this->obsrvClassRepository->getById($obsrvClassId);

        // Check if the user is the owner of the observation class
        if ($authUser->id !== $obsrvClass->user_id) {
            return null;
        }

        // Check if the class is in starting status
        if ($obsrvClass->status !== StatusType::getStatusValue(StatusType::STARTING)) {
            return null;
        }

        // Update the class by
        //  - Adding the extra time to the duration
        $attrs = [
            'duration' => $obsrvClass->duration + $extraSeconds,
        ];
        $this->obsrvClassRepository->updateObsrvClass($obsrvClass->id, $attrs);

        return $this->getCurrentUserObsrvClass($authUser);
    }

    /**
     * Sanitizes the observation class data
     * @param array $obsrvData (passed by reference)
     * @return array
     */
    private function sanitizeObsrvData(array &$obsrvData)
    {
        // Sanitize the observation class data
        $obsrvData['duration'] = (int) $obsrvData['duration'];
        $obsrvData['channelId'] = (int) $obsrvData['channelId'];
        $obsrvData['lessonName'] = trim($obsrvData['lessonName']);
        $obsrvData['lessonSharing'] = strtolower(trim($obsrvData['lessonSharing']));
        $obsrvData['classificationId'] = (int) $obsrvData['classificationId'];
        $obsrvData['subjectId'] = (int) $obsrvData['subjectId'];
        $obsrvData['grade'] = (int) $obsrvData['grade'];
    }

    /**
     * End all existing Observation Class except the new one
     * @param object $authUser
     * @param int $newObsrvClassId
     */
    private function endAllObsrvClasses(object $authUser, int $newObsrvClassId)
    {
        $obsrvClasses = $this->obsrvClassRepository->getAllByUserIdAsQuery($authUser->id);
        $obsrvClasses
            ->where('id', '!=', $newObsrvClassId)
            ->where('status', '!=', StatusType::ENDED_VAL)
            ->update([
                'status' => StatusType::ENDED_VAL,
            ]);
    }

    /**
     * Generate a 4-digit pin code as a string
     * @return string
     */
    private function generatePinCode()
    {
        return AlphaNumeric::generateNum(4);
    }

    /**
     * Generate a binding number as a string
     * timestamp + 6-digit userId
     * @param int $userId
     * @return string
     */
    private function generateBindingNumber(int $userId)
    {
        $userIdPadded = str_pad($userId, 6, '0', STR_PAD_LEFT);
        return time() . $userIdPadded;
    }

    /**
     * Generate a 6-digit class code as a string
     * ORG starts with a letter 0
     * CN starts with a letter 9
     * If the class code is not unique, generate a new one
     * @return string
     */
    private function generateClassroomCode()
    {
        $curStation = GlobalPlatform::getCurrentStation();
        $classroomCode = $curStation == 'CN' ? '9' : '0';
        for ($i = 1; $i < 6; $i++) {
            $classroomCode .= mt_rand(0, 9);
        }

        // Check if the class code is unique (recursive)
        $obsrvClass = $this->obsrvClassRepository
            ->getByClassroomCodeAsQuery($classroomCode)
            ->where('status', '!=', StatusType::ENDED_VAL)
            ->first();
        if ($obsrvClass) return $this->generateClassroomCode();

        return $classroomCode;
    }
}
