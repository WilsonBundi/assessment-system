<?php

namespace app\components;

use Yii;
use yii\base\Behavior;
use yii\web\UploadedFile;
use app\models\Assessment;

/**
 * AssessmentImageBehavior - Handles image uploads for assessments
 * Stores images in web/uploads/assessments/{assessment_id}/
 * Max 5 images per assessment
 */
class AssessmentImageBehavior extends Behavior
{
    const MAX_IMAGES = 5;
    const UPLOAD_PATH = '@webroot/uploads/assessments';

    /**
     * Get upload directory for an assessment
     * @param int $assessmentId
     * @return string
     */
    public static function getUploadDir($assessmentId)
    {
        return \Yii::getAlias(self::UPLOAD_PATH . '/' . $assessmentId);
    }

    /**
     * Get upload URL for an assessment
     * @param int $assessmentId
     * @return string
     */
    public static function getUploadUrl($assessmentId)
    {
        return Yii::$app->urlManager->baseUrl . '/uploads/assessments/' . $assessmentId;
    }

    /**
     * Upload and save images for assessment
     * @param Assessment $assessment
     * @param array $uploadedFiles Array of UploadedFile instances
     * @return array Array of saved file names
     * @throws \Exception
     */
    public static function uploadImages(Assessment $assessment, $uploadedFiles = [])
    {
        if (empty($uploadedFiles)) {
            return [];
        }

        $uploadDir = self::getUploadDir($assessment->assessment_id);
        
        // Create upload directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Get existing images
        $existingImages = self::getImages($assessment->assessment_id);
        $newImages = [];

        foreach ($uploadedFiles as $file) {
            if (!$file || !($file instanceof UploadedFile)) {
                continue;
            }

            // Check total images limit
            if (count($existingImages) + count($newImages) >= self::MAX_IMAGES) {
                break;
            }

            // Validate file
            $ext = strtolower($file->extension);
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (!in_array($ext, $allowedExts)) {
                throw new \Exception("Invalid file type. Allowed: " . implode(', ', $allowedExts));
            }

            if ($file->size > 5 * 1024 * 1024) { // 5MB max
                throw new \Exception("File size too large. Maximum 5MB allowed.");
            }

            // Generate unique filename
            $timestamp = time();
            $random = random_int(1000, 9999);
            $filename = "image_{$timestamp}_{$random}.{$ext}";
            
            // Save file
            if ($file->saveAs($uploadDir . '/' . $filename)) {
                $newImages[] = $filename;
            }
        }

        return $newImages;
    }

    /**
     * Get all images for an assessment
     * @param int $assessmentId
     * @return array Array of image file names
     */
    public static function getImages($assessmentId)
    {
        $uploadDir = self::getUploadDir($assessmentId);
        
        if (!is_dir($uploadDir)) {
            return [];
        }

        $images = [];
        $files = scandir($uploadDir);
        
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $images[] = $file;
            }
        }

        return $images;
    }

    /**
     * Get image URL
     * @param int $assessmentId
     * @param string $filename
     * @return string
     */
    public static function getImageUrl($assessmentId, $filename)
    {
        return self::getUploadUrl($assessmentId) . '/' . urlencode($filename);
    }

    /**
     * Delete an image
     * @param int $assessmentId
     * @param string $filename
     * @return bool
     */
    public static function deleteImage($assessmentId, $filename)
    {
        $uploadDir = self::getUploadDir($assessmentId);
        $filePath = $uploadDir . '/' . $filename;

        if (file_exists($filePath)) {
            return unlink($filePath);
        }

        return false;
    }

    /**
     * Delete all images for an assessment
     * @param int $assessmentId
     * @return bool
     */
    public static function deleteAllImages($assessmentId)
    {
        $uploadDir = self::getUploadDir($assessmentId);

        if (!is_dir($uploadDir)) {
            return true;
        }

        $files = scandir($uploadDir);
        
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                unlink($uploadDir . '/' . $file);
            }
        }

        return rmdir($uploadDir);
    }

    /**
     * Get image thumbnails HTML
     * @param int $assessmentId
     * @return string HTML with image gallery
     */
    public static function getImageGallery($assessmentId)
    {
        $images = self::getImages($assessmentId);

        if (empty($images)) {
            return '<div class="alert alert-info">No images uploaded yet.</div>';
        }

        $html = '<div class="image-gallery">';
        
        foreach ($images as $image) {
            $imageUrl = self::getImageUrl($assessmentId, $image);
            $html .= '<div class="image-thumbnail">';
            $html .= '<img src="' . $imageUrl . '" alt="Assessment image" style="max-width: 150px; max-height: 150px; margin: 5px;">';
            $html .= '<br><small>' . $image . '</small>';
            $html .= '</div>';
        }

        $html .= '</div>';
        return $html;
    }
}
