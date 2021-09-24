<?php

namespace Modules\Admin\Repositories;

use DB;
use Modules\Admin\Models\User;
use Modules\Admin\Models\UserCertificateExpirySettings;
use Modules\Admin\Models\RemainderMailNotifications;
use App\Services\HelperService;
use App\Repositories\MailQueueRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UserCertificateExpirySettingsRepository
{

    protected $model, $userRepository, $mailQueueRepository;

    public function __construct(
        UserCertificateExpirySettings $userCertificateExpirySettings,
        UserRepository $userRepository,
        MailQueueRepository $mailQueueRepository
    ) {
        $this->model = $userCertificateExpirySettings;
        $this->userRepository = $userRepository;
        $this->mailQueueRepository = $mailQueueRepository;
    }

    /**
     * Get all lookup list
     *
     * @param empty
     * @return array
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get all lookup list
     *
     * @param empty
     * @return array
     */
    public function getAllByType($type, $orderByField = 'id')
    {
        return $this->model->where("settings_type", $type)->orderBy($orderByField)->get();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->model->find($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $data
     * @return object
     */
    public function save($data, $type)
    {
        $this->model->where('settings_type', $type)->delete();
        foreach ($data as $key => $eachSetting) {
            $dataArr = array(
                "settings_type" => $type,
                "parameter" => ($key + 1),
                "value" => $eachSetting
            );
            $this->model->create($dataArr);
        }
        return true;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return object
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    /*
     * Send user certificate expiry reminder mail
     * @return boolean
     */

    public function sendUserCertificateExpiryReminders()
    {
        // $users = $this->userRepository->getAllUsersID();
        $users = User::where("active", 1)->pluck("id")->toArray();
        if (!empty($users)) {
            foreach ($users as $userId) {
                $userDetails = $this->userRepository->getUserDetails($userId);
                $todayTimeString = strtotime(date('Y-m-d'));
                $now = Carbon::now()->toDateString();

                if (empty($userDetails)) {
                    continue;
                }

                $dataArray = [];
                if (!empty($userDetails->securityClearanceUser)) {
                    foreach ($userDetails->securityClearanceUser as $securityClearanceUser) {
                        if (empty($securityClearanceUser->valid_until)) {
                            continue;
                        }

                        $validTil = strtotime($securityClearanceUser->valid_until);
                        $ky = $securityClearanceUser->id . "_security_clearence";
                        $expiredCertificate = false;

                        $message = $securityClearanceUser->getStatusAttribute();
                        if ($todayTimeString >= $validTil) {
                            $expiredCertificate = true;
                        }

                        $dataArray[$ky] = ['status' => $expiredCertificate, 'user_id' => $securityClearanceUser->user_id, 'model' => 'securityClearanceUser', 'id' => $securityClearanceUser->id, 'dueInDays' => $message["textDiff"], 'name' => 'Security Clearance (' . $securityClearanceUser->securityClearanceLookups->security_clearance . ')', 'valid_until' => $securityClearanceUser->valid_until];
                    }
                }

                if (!empty($userDetails->userCertificate)) {
                    foreach ($userDetails->userCertificate as $userCertificate) {
                        if (empty($userCertificate->expires_on)) {
                            continue;
                        }

                        $validTil = strtotime($userCertificate->expires_on);
                        $ky = $userCertificate->id . "_user_certificate";
                        $expiredCertificate = false;

                        $message = $userCertificate->getStatusAttribute();
                        if ($todayTimeString >= $validTil) {
                            $expiredCertificate = true;
                        }
                        $dataArray[$ky] = ['status' => $expiredCertificate, 'user_id' => $userCertificate->user_id, 'model' => 'userCertificate', 'id' => $userCertificate->id, 'dueInDays' => $message["textDiff"], 'name' => $userCertificate->trashedCertificateMaster->certificate_name, 'valid_until' => $userCertificate->expires_on];
                    }
                }
                if (!empty($dataArray)) {
                    $mailSettings = $this->getAllByType('mail');
                    $reminderEmailEntryArr = [];
                    foreach ($mailSettings as $mailSetting) {
                        foreach ($dataArray as $ky => $data) {
                            $expiryDate = date_create($data['valid_until']);
                            $daysDiff = Carbon::parse($now)->diffInDays($data['valid_until'], true);
                            $vDateStr = strtotime($data['valid_until']);

                            //check email already triggered
                            $reminderEmailNotificationStatus = $this->reminderEmailNotificationExists($data['id'], $data['model'], "user_certificate_expiry_notification_reminder_" . $mailSetting->parameter, $userId, $data['valid_until']);
                            if ((($mailSetting->value >= $daysDiff) || ($todayTimeString > $vDateStr)) && ($mailSetting->value > 0) && ($reminderEmailNotificationStatus == false) && (!in_array($ky, $reminderEmailEntryArr))) {
                                //reminder email notification entry
                                $status = $this->makeEntryToRemainderEmailNotification($data['id'], $data['model'], "user_certificate_expiry_notification_reminder_" . $mailSetting->parameter, $userId, $expiryDate);
                                if ($status) {
                                    $reminderEmailEntryArr[] = $ky;
                                    //mail queue entry here
                                    $data['valid_until'] = date_format($expiryDate, "M d, Y");
                                    $emailReminderStatus = $this->sendUserCertificateExpiryReminderMail($data, "user_certificate_expiry_notification_reminder_" . $mailSetting->parameter, $userDetails);
                                }
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    /*
     * fetch certificate expiry notification details
     * @return array
     */

    public function getCertificateExpiryDetailsByLoggedInUser()
    {
        $notifications = [];
        $loggedInUserId = Auth::id();
        $userDetails = $this->userRepository->getUserDetails($loggedInUserId);
        $todayTimeString = strtotime(date('Y-m-d'));
        $now = Carbon::now()->toDateString();

        if (empty($userDetails)) {
            return $notifications;
        }

        $dataArray = [];
        if (!empty($userDetails->securityClearanceUser)) {
            foreach ($userDetails->securityClearanceUser as $securityClearanceUser) {
                if (empty($securityClearanceUser->valid_until)) {
                    continue;
                }

                $validTil = strtotime($securityClearanceUser->valid_until);
                $ky = $securityClearanceUser->id . "_security_clearence";
                $expiredCertificate = false;

                $message = $securityClearanceUser->getStatusAttribute();
                if ($todayTimeString >= $validTil) {
                    $expiredCertificate = true;
                }
                $dataArray[$ky] = ['status' => $expiredCertificate, 'user_id' => $securityClearanceUser->user_id, 'model' => 'securityClearanceUser', 'id' => $securityClearanceUser->id, 'dueInDays' => $message["textDiff"], 'name' => 'Security Clearance (' . $securityClearanceUser->securityClearanceLookups->security_clearance . ')', 'valid_until' => $securityClearanceUser->valid_until];
            }
        }

        if (!empty($userDetails->userCertificate)) {
            foreach ($userDetails->userCertificate as $userCertificate) {
                if (empty($userCertificate->expires_on)) {
                    continue;
                }

                $validTil = strtotime($userCertificate->expires_on);
                $ky = $userCertificate->id . "_user_certificate";
                $expiredCertificate = false;

                $message = $userCertificate->getStatusAttribute();
                if ($todayTimeString >= $validTil) {
                    $expiredCertificate = true;
                }
                $dataArray[$ky] = ['status' => $expiredCertificate, 'user_id' => $userCertificate->user_id, 'model' => 'userCertificate', 'id' => $userCertificate->id, 'dueInDays' => $message["textDiff"], 'name' => $userCertificate->trashedCertificateMaster->certificate_name, 'valid_until' => $userCertificate->expires_on];
            }
        }

        if (!empty($dataArray)) {
            $notificationSettings = $this->getAllByType('notification');
            foreach ($notificationSettings as $notificationSetting) {
                foreach ($dataArray as $ky => $data) {
                    $daysDiff = Carbon::parse($now)->diffInDays($data['valid_until'], true);
                    $vDateStr = strtotime($data['valid_until']);

                    if ((($notificationSetting->value >= $daysDiff) || ($todayTimeString > $vDateStr)) && ($notificationSetting->value > 0) && (!in_array($ky, $notifications))) {
                        $expiryDate = date_create($data['valid_until']);
                        $data['valid_until'] = date_format($expiryDate, "M d, Y");
                        array_push($notifications, $data);
                    }
                }
            }
        }

        return $notifications;
    }

    /*
     * make mail queue entry
     * @param $data, $emailTemplteSelected, $userDetails
     * @return boolean
     */

    public function sendUserCertificateExpiryReminderMail($data, $emailTemplteSelected, $userDetails)
    {
        $helper_variables = array(
            '{receiverFullName}' => HelperService::sanitizeInput($userDetails->getFullNameAttribute()),
            '{expiredUserCertificateName}' => $data['name'],
            '{userCertificateExpiryDate}' => $data['valid_until'],
            '{userCertificateExpiryDueInDays}' => $data['dueInDays']
        );

        //for email reminder
        $emailResult = $this->mailQueueRepository->prepareMailTemplate($emailTemplteSelected, null, $helper_variables, null, 0, $userDetails->id, null, $userDetails->alternate_email);
        return $emailResult;
    }

    /*
     * check whether the certificate expiry mail already send or not
     * @param $id, $model, $notificationType, $userId, $expiryDate
     * return boolean
     */

    public function reminderEmailNotificationExists($id, $model, $notificationType, $userId, $expiryDate)
    {
        $documentCount = RemainderMailNotifications::where("notification_type", $notificationType)
            ->where("model", $model)
            ->where("document_id", $id)
            ->where("expiry_date", $expiryDate)
            ->where("user_id", $userId)
            ->count();
        return ($documentCount > 0) ? true : false;
    }

    /*
     * make entry to reminder email notifications
     * @param $id, $model, $notificationType, $userId, $expiryDate
     * return boolean
     */

    public function makeEntryToRemainderEmailNotification($id, $model, $notificationType, $userId, $expiryDate)
    {
        try {
            $remainderMailNotifications = new RemainderMailNotifications();
            $remainderMailNotifications->notification_type = $notificationType;
            $remainderMailNotifications->model = $model;
            $remainderMailNotifications->document_id = $id;
            $remainderMailNotifications->user_id = $userId;
            $remainderMailNotifications->expiry_date = $expiryDate;
            $remainderMailNotifications->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    //    private function getDueDaysNotificationMessage($dStrToTime, $sStrToTime, $expired) {
    //        $daysDiff = Carbon::parse($sStrToTime)->diffInDays($dStrToTime, false);
    //        $absMonthsDiff = Carbon::parse($sStrToTime)->diffInMonths($dStrToTime);
    //        $absYearsDiff = Carbon::parse($sStrToTime)->diffInYears($dStrToTime);
    //        $absDaysDiff = abs($daysDiff);
    //        $expiringToday = false;
    //
    //        $msg = "";
    //        //day check
    //        if ($absDaysDiff >= 30) {
    //            //month check
    //            if ($absMonthsDiff == 1) {
    //                $msg .= $absMonthsDiff . " month ";
    //            } elseif ($absMonthsDiff < 12) {
    //                $msg .= $absMonthsDiff . " months ";
    //            } else {
    //                //year check
    //                if ($absYearsDiff == 1) {
    //                    $msg .= $absYearsDiff . " year ";
    //                } else {
    //                    $msg .= $absYearsDiff . " years ";
    //                }
    //            }
    //        } elseif ($absDaysDiff > 1) {
    //            $msg .= $absDaysDiff . " days ";
    //        } elseif ($absDaysDiff == 0) {
    //            $msg .= "Today";
    //            $expiringToday = true;
    //        } else {
    //            $msg .= $absDaysDiff . " day ";
    //        }
    //
    //        $expiryMsg = ($expiringToday) ? "Expiring " : (($expired) ? "Expired " : "Expiring in ");
    //        $expiryMsg .= $msg;
    //        $expiryMsg .= ($expiringToday) ? "" : (($expired) ? "ago" : "");
    //        return $expiryMsg;
    //    }
}
