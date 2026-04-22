<?php

namespace app\components;

use Yii;
use app\models\Assessment;
use app\models\Users;
use app\models\Students;

/**
 * Notification Service
 * Sends in-app and email notifications for assessment events
 */
class NotificationService
{
    const TYPE_ASSESSMENT_CREATED = 'assessment_created';
    const TYPE_ASSESSMENT_UPDATED = 'assessment_updated';
    const TYPE_ASSESSMENT_SUBMITTED = 'assessment_submitted';
    const TYPE_GRADES_ADDED = 'grades_added';
    const TYPE_FEEDBACK_READY = 'feedback_ready';
    const TYPE_REVIEW_REQUIRED = 'review_required';
    const TYPE_REPORT_GENERATED = 'report_generated';
    const TYPE_STUDENT_SELECTED = 'student_selected';
    const TYPE_EVIDENCE_UPLOADED = 'evidence_uploaded';
    const TYPE_ASSESSMENT_COMPLETED = 'assessment_completed';
    const TYPE_SUPERVISOR_ASSIGNED = 'supervisor_assigned';
    const TYPE_SUPERVISOR_REASSIGNED = 'supervisor_reassigned';

    /**
     * Send notification for assessment creation
     * @param Assessment $assessment
     */
    public static function notifyAssessmentCreated(Assessment $assessment)
    {
        $examiner = $assessment->examiner;
        if (!$examiner) {
            return;
        }

        $message = "Assessment created for student {$assessment->student_reg_no}";
        self::notify($examiner->user_id, self::TYPE_ASSESSMENT_CREATED, $message, [
            'assessment_id' => $assessment->assessment_id,
            'student' => $assessment->student_reg_no,
            'school' => $assessment->school ? $assessment->school->school_name : 'N/A'
        ]);
    }

    /**
     * Send notification when grades are all added
     * @param Assessment $assessment
     */
    public static function notifyGradesComplete(Assessment $assessment)
    {
        $examiner = $assessment->examiner;
        if (!$examiner) {
            return;
        }

        $message = "All grades have been entered for assessment of {$assessment->student_reg_no}";
        self::notify($examiner->user_id, self::TYPE_GRADES_ADDED, $message, [
            'assessment_id' => $assessment->assessment_id,
            'total_score' => $assessment->total_score,
            'overall_level' => $assessment->overall_level
        ]);
    }

    /**
     * Send feedback ready notification to student/supervisor
     * @param Assessment $assessment
     */
    public static function notifyFeedbackReady(Assessment $assessment)
    {
        $examiner = $assessment->examiner;
        if (!$examiner) {
            return;
        }

        $message = "Feedback is ready for student {$assessment->student_reg_no}";
        self::notify($examiner->user_id, self::TYPE_FEEDBACK_READY, $message, [
            'assessment_id' => $assessment->assessment_id,
            'student' => $assessment->student_reg_no,
            'report_url' => Yii::$app->urlManager->createAbsoluteUrl(['/assessment/report-student', 'assessment_id' => $assessment->assessment_id])
        ]);
    }

    /**
     * Send notification to coordinators that review is required
     * @param Assessment $assessment
     */
    public static function notifyReviewRequired(Assessment $assessment)
    {
        // Find all Zone Coordinators
        $coordinators = Users::find()
            ->where(['role_id' => self::getRoleId('Zone Coordinator')])
            ->all();

        $message = "Assessment of {$assessment->student_reg_no} requires review";
        
        foreach ($coordinators as $coordinator) {
            self::notify($coordinator->user_id, self::TYPE_REVIEW_REQUIRED, $message, [
                'assessment_id' => $assessment->assessment_id,
                'student' => $assessment->student_reg_no,
                'score' => $assessment->total_score,
                'level' => $assessment->overall_level
            ]);
        }
    }

    /**
     * Send notification to TP Office with full report
     * @param Assessment $assessment
     */
    public static function notifyTpOffice(Assessment $assessment)
    {
        // Find all TP Office users
        $tpOffice = Users::find()
            ->where(['role_id' => self::getRoleId('TP Office')])
            ->all();

        $message = "New assessment report available for {$assessment->student_reg_no}";
        
        foreach ($tpOffice as $user) {
            self::notify($user->user_id, self::TYPE_REPORT_GENERATED, $message, [
                'assessment_id' => $assessment->assessment_id,
                'student' => $assessment->student_reg_no,
                'score' => $assessment->total_score,
                'report_url' => Yii::$app->urlManager->createAbsoluteUrl(['/assessment/report-office', 'assessment_id' => $assessment->assessment_id])
            ]);
        }
    }

    /**
     * Send notification when student is selected for assessment
     * @param Assessment $assessment
     */
    public static function notifyStudentSelected(Assessment $assessment)
    {
        $examiner = $assessment->examiner;
        if (!$examiner) {
            return;
        }

        $message = "Student {$assessment->student_reg_no} has been selected for assessment";
        self::notify($examiner->user_id, self::TYPE_STUDENT_SELECTED, $message, [
            'assessment_id' => $assessment->assessment_id,
            'student' => $assessment->student_reg_no,
            'school' => $assessment->school ? $assessment->school->school_name : 'N/A'
        ]);
    }

    /**
     * Send notification when evidence is uploaded
     * @param Assessment $assessment
     * @param int $evidenceCount Number of evidence files uploaded
     */
    public static function notifyEvidenceUploaded(Assessment $assessment, $evidenceCount = 0)
    {
        $examiner = $assessment->examiner;
        if (!$examiner) {
            return;
        }

        $message = "Evidence uploaded for assessment of {$assessment->student_reg_no} ({$evidenceCount} files)";
        self::notify($examiner->user_id, self::TYPE_EVIDENCE_UPLOADED, $message, [
            'assessment_id' => $assessment->assessment_id,
            'student' => $assessment->student_reg_no,
            'evidence_count' => $evidenceCount
        ]);
    }

    /**
     * Send assessment completion confirmation
     * @param Assessment $assessment
     */
    public static function notifyAssessmentCompleted(Assessment $assessment)
    {
        $examiner = $assessment->examiner;
        if (!$examiner) {
            return;
        }

        $message = "Assessment completed and submitted for {$assessment->student_reg_no}";
        self::notify($examiner->user_id, self::TYPE_ASSESSMENT_COMPLETED, $message, [
            'assessment_id' => $assessment->assessment_id,
            'student' => $assessment->student_reg_no,
            'total_score' => $assessment->total_score,
            'overall_level' => $assessment->overall_level,
            'report_url' => Yii::$app->urlManager->createAbsoluteUrl(['/assessment/report-student', 'assessment_id' => $assessment->assessment_id])
        ]);
    }

    /**
     * Send assessment validation confirmation
     * @param Assessment $assessment
     */
    public static function notifyAssessmentValidated(Assessment $assessment)
    {
        $examiner = $assessment->examiner;
        if (!$examiner) {
            return;
        }

        $message = "Your assessment for {$assessment->student_reg_no} has been validated by the Zone Coordinator";
        self::notify($examiner->user_id, self::TYPE_ASSESSMENT_COMPLETED, $message, [
            'assessment_id' => $assessment->assessment_id,
            'student' => $assessment->student_reg_no,
            'total_score' => $assessment->total_score,
            'overall_level' => $assessment->overall_level,
            'status' => 'validated'
        ]);
    }

    /**
     * Send notification when a supervisor gets assigned a student
     * @param Users $supervisor
     * @param Users|Students $student
     */
    public static function notifySupervisorAssigned(Users $supervisor, $student)
    {
        $zoneName = $student->zone ? $student->zone->zone_name : 'Unknown Zone';
        $message = "You have been assigned to student {$student->student_reg_no} ({$student->name}) in {$zoneName}.";
        self::notify($supervisor->user_id, self::TYPE_SUPERVISOR_ASSIGNED, $message, [
            'student' => $student->student_reg_no,
            'student_name' => $student->name,
            'zone' => $zoneName,
            'school' => $student->school ? $student->school->school_name : 'N/A',
        ]);
    }

    /**
     * Send notification when a supervisor gets reassigned to a student
     * @param Users $supervisor
     * @param Users|Students $student
     */
    public static function notifySupervisorReassigned(Users $supervisor, $student)
    {
        $zoneName = $student->zone ? $student->zone->zone_name : 'Unknown Zone';
        $message = "You have been reassigned to student {$student->student_reg_no} ({$student->name}) in {$zoneName}.";
        self::notify($supervisor->user_id, self::TYPE_SUPERVISOR_REASSIGNED, $message, [
            'student' => $student->student_reg_no,
            'student_name' => $student->name,
            'zone' => $zoneName,
            'school' => $student->school ? $student->school->school_name : 'N/A',
        ]);
    }

    /**
     * Send generic notification
     * @param int $userId Recipient user ID
     * @param string $type Notification type
     * @param string $message Message text
     * @param array $data Additional data
     */
    public static function notify($userId, $type, $message, $data = [])
    {
        try {
            // Store in-app notification
            self::storeNotification($userId, $type, $message, $data);
            
            // Send email notification
            self::sendEmailNotification($userId, $type, $message, $data);
        } catch (\Exception $e) {
            Yii::error('Notification failed: ' . $e->getMessage(), 'notification');
        }
    }

    /**
     * Store in-app notification
     * @param int $userId
     * @param string $type
     * @param string $message
     * @param array $data
     */
    private static function storeNotification($userId, $type, $message, $data = [])
    {
        $notificationDir = Yii::getAlias('@runtime/notifications');
        
        if (!is_dir($notificationDir)) {
            mkdir($notificationDir, 0777, true);
        }

        $notification = [
            'id' => uniqid(),
            'user_id' => $userId,
            'type' => $type,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s'),
            'read' => false
        ];

        $fileName = $notificationDir . '/' . $userId . '_notifications.json';
        $notifications = [];

        if (file_exists($fileName)) {
            $notifications = json_decode(file_get_contents($fileName), true) ?? [];
        }

        array_unshift($notifications, $notification);
        
        // Keep only last 100 notifications per user
        $notifications = array_slice($notifications, 0, 100);
        
        file_put_contents($fileName, json_encode($notifications, JSON_PRETTY_PRINT));
    }

    /**
     * Send email notification
     * @param int $userId
     * @param string $type
     * @param string $message
     * @param array $data
     */
    private static function sendEmailNotification($userId, $type, $message, $data = [])
    {
        $user = Users::findOne($userId);
        if (!$user || !$user->email) {
            return;
        }

        $subject = self::getNotificationSubject($type);
        
        $body = "Hello {$user->name},\n\n";
        $body .= "{$message}\n\n";
        
        if (isset($data['report_url'])) {
            $body .= "View Report: {$data['report_url']}\n\n";
        }

        if (isset($data['student_name']) && isset($data['zone'])) {
            $body .= "Student: {$data['student_name']} ({$data['student']})\n";
            $body .= "Zone: {$data['zone']}\n";
            if (isset($data['school'])) {
                $body .= "School: {$data['school']}\n";
            }
            $body .= "\n";
        }
        
        $body .= "System automatically generated email - Please do not reply.\n";
        $body .= "TP Assessment System\n";

        Yii::$app->mailer->compose()
            ->setTo($user->email)
            ->setSubject($subject)
            ->setTextBody($body)
            ->send();

        Yii::info("Email notification sent to {$user->email}: $subject", 'notification');
    }

    /**
     * Get notification subject
     * @param string $type
     * @return string
     */
    private static function getNotificationSubject($type)
    {
        $subjects = [
            self::TYPE_ASSESSMENT_CREATED => 'New Assessment Created',
            self::TYPE_ASSESSMENT_UPDATED => 'Assessment Updated',
            self::TYPE_ASSESSMENT_SUBMITTED => 'Assessment Submitted',
            self::TYPE_GRADES_ADDED => 'Grades Completed',
            self::TYPE_FEEDBACK_READY => 'Feedback Ready',
            self::TYPE_REVIEW_REQUIRED => 'Review Required',
            self::TYPE_REPORT_GENERATED => 'Assessment Report Generated',
            self::TYPE_STUDENT_SELECTED => 'Student Selected for Assessment',
            self::TYPE_EVIDENCE_UPLOADED => 'Evidence Uploaded',
            self::TYPE_ASSESSMENT_COMPLETED => 'Assessment Completed',
            self::TYPE_SUPERVISOR_ASSIGNED => 'Student Assignment Notification',
            self::TYPE_SUPERVISOR_REASSIGNED => 'Student Reassignment Notification'
        ];

        return $subjects[$type] ?? 'Notification';
    }

    /**
     * Get role ID by name
     * @param string $roleName
     * @return int|null
     */
    private static function getRoleId($roleName)
    {
        $role = \app\models\Role::findOne(['role_name' => $roleName]);
        return $role ? $role->role_id : null;
    }

    /**
     * Get notifications for user
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public static function getUserNotifications($userId, $limit = 50)
    {
        try {
            $notificationDir = Yii::getAlias('@runtime/notifications');
            $fileName = $notificationDir . '/' . $userId . '_notifications.json';

            if (!file_exists($fileName)) {
                return [];
            }

            $notifications = json_decode(file_get_contents($fileName), true) ?? [];
            return array_slice($notifications, 0, $limit);
        } catch (\Exception $e) {
            Yii::error('Failed to get notifications: ' . $e->getMessage(), 'notification');
            return [];
        }
    }

    /**
     * Mark notification as read
     * @param int $userId
     * @param string $notificationId
     */
    public static function markAsRead($userId, $notificationId)
    {
        try {
            $notificationDir = Yii::getAlias('@runtime/notifications');
            $fileName = $notificationDir . '/' . $userId . '_notifications.json';

            if (!file_exists($fileName)) {
                return;
            }

            $notifications = json_decode(file_get_contents($fileName), true) ?? [];
            
            foreach ($notifications as &$notification) {
                if ($notification['id'] === $notificationId) {
                    $notification['read'] = true;
                    break;
                }
            }

            file_put_contents($fileName, json_encode($notifications, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
            Yii::error('Failed to mark notification as read: ' . $e->getMessage(), 'notification');
        }
    }

    /**
     * Get unread notification count
     * @param int $userId
     * @return int
     */
    public static function getUnreadCount($userId)
    {
        $notifications = self::getUserNotifications($userId);
        return count(array_filter($notifications, function($n) {
            return !$n['read'];
        }));
    }
}
