<?php

namespace app\components;

use Yii;
use yii\base\Component;

/**
 * AuditLogger Component
 * Logs all assessment-related activities for audit trail
 */
class AuditLogger extends Component
{
    const LOG_DIR = '@runtime/audit-logs';

    /**
     * Log an audit event
     * @param string $action Action performed (create, update, delete, submit, review, approve)
     * @param string $entityType Type of entity (assessment, grade)
     * @param int $entityId Entity ID
     * @param array $changes Array of changes made
     * @param string $notes Additional notes
     * @return bool
     */
    public static function log($action, $entityType, $entityId, $changes = [], $notes = '')
    {
        try {
            $logDir = Yii::getAlias(self::LOG_DIR);
            
            // Create log directory if it doesn't exist
            if (!is_dir($logDir)) {
                mkdir($logDir, 0777, true);
            }

            $user = Yii::$app->user->identity;
            $userId = $user ? $user->id : 'unknown';
            $userName = $user ? (property_exists($user, 'name') ? $user->name : $user->username) : 'Anonymous';
            $roleId = $user && property_exists($user, 'role_id') ? $user->role_id : 0;
            $userRole = $roleId ? \app\components\RbacHelper::getUserRole($roleId) : 'Test User';

            $logEntry = [
                'timestamp' => date('Y-m-d H:i:s'),
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'user_id' => $userId,
                'user_name' => $userName,
                'user_role' => $userRole,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'changes' => $changes,
                'notes' => $notes,
                'timestamp_unix' => time()
            ];

            // Log to file
            $fileName = date('Y-m-d') . '_audit.log';
            $filePath = $logDir . '/' . $fileName;
            
            $logLine = json_encode($logEntry) . "\n";
            file_put_contents($filePath, $logLine, FILE_APPEND);

            return true;
        } catch (\Exception $e) {
            Yii::error('Audit logging failed: ' . $e->getMessage(), 'audit');
            return false;
        }
    }

    /**
     * Get audit logs for an entity
     * @param string $entityType Type of entity
     * @param int $entityId Entity ID
     * @param int $days Number of days to look back
     * @return array Array of audit log entries
     */
    public static function getEntityLogs($entityType, $entityId, $days = 30)
    {
        try {
            $logDir = Yii::getAlias(self::LOG_DIR);
            
            if (!is_dir($logDir)) {
                return [];
            }

            $logs = [];
            $startDate = date('Y-m-d', strtotime("-$days days"));

            // Read log files
            $files = scandir($logDir);
            
            foreach ($files as $file) {
                if (strpos($file, '.log') === false) {
                    continue;
                }

                $filePath = $logDir . '/' . $file;
                $fileDate = str_replace('_audit.log', '', $file);

                if ($fileDate < $startDate) {
                    continue;
                }

                $handle = fopen($filePath, 'r');
                while (($line = fgets($handle)) !== false) {
                    $entry = json_decode(trim($line), true);
                    
                    if ($entry && $entry['entity_type'] === $entityType && $entry['entity_id'] === $entityId) {
                        $logs[] = $entry;
                    }
                }
                fclose($handle);
            }

            // Sort by timestamp (newest first)
            usort($logs, function($a, $b) {
                return $b['timestamp_unix'] - $a['timestamp_unix'];
            });

            return $logs;
        } catch (\Exception $e) {
            Yii::error('Failed to get audit logs: ' . $e->getMessage(), 'audit');
            return [];
        }
    }

    /**
     * Get all recent audit logs
     * @param int $limit Number of entries to return
     * @return array
     */
    public static function getRecentLogs($limit = 100)
    {
        try {
            $logDir = Yii::getAlias(self::LOG_DIR);
            
            if (!is_dir($logDir)) {
                return [];
            }

            $logs = [];
            $files = array_reverse(scandir($logDir));

            foreach ($files as $file) {
                if (strpos($file, '.log') === false) {
                    continue;
                }

                $filePath = $logDir . '/' . $file;
                $handle = fopen($filePath, 'r');
                
                while (($line = fgets($handle)) !== false && count($logs) < $limit) {
                    $entry = json_decode(trim($line), true);
                    if ($entry) {
                        $logs[] = $entry;
                    }
                }
                fclose($handle);

                if (count($logs) >= $limit) {
                    break;
                }
            }

            return $logs;
        } catch (\Exception $e) {
            Yii::error('Failed to get recent logs: ' . $e->getMessage(), 'audit');
            return [];
        }
    }

    /**
     * Clear old audit logs (older than specified days)
     * @param int $days Delete logs older than this many days
     * @return bool
     */
    public static function clearOldLogs($days = 90)
    {
        try {
            $logDir = Yii::getAlias(self::LOG_DIR);
            
            if (!is_dir($logDir)) {
                return true;
            }

            $cutoffDate = date('Y-m-d', strtotime("-${days} days"));
            $files = scandir($logDir);

            foreach ($files as $file) {
                if (strpos($file, '.log') === false) {
                    continue;
                }

                $fileDate = str_replace('_audit.log', '', $file);
                
                if ($fileDate < $cutoffDate) {
                    unlink($logDir . '/' . $file);
                }
            }

            return true;
        } catch (\Exception $e) {
            Yii::error('Failed to clear old logs: ' . $e->getMessage(), 'audit');
            return false;
        }
    }
}
