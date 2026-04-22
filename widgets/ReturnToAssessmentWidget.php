<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;
use yii\bootstrap5\Html;

/**
 * Widget to display "Return to Assessment" button
 * Appears when user is actively working on an assessment and navigates away
 */
class ReturnToAssessmentWidget extends Widget
{
    public function run()
    {
        // Check if there's an active assessment in session
        $activeAssessmentId = Yii::$app->session->get('active_assessment_id');
        $studentReg = Yii::$app->session->get('active_assessment_student');
        
        if (!$activeAssessmentId) {
            return '';
        }
        
        // Don't show button if user is already on the assessment update or grade-grid page
        if ($this->isOnAssessmentPage($activeAssessmentId)) {
            return '';
        }
        
        // Generate the return button
        return $this->renderButton($activeAssessmentId, $studentReg);
    }
    
    /**
     * Check if the current page is the assessment page the user is working on
     */
    private function isOnAssessmentPage($assessmentId)
    {
        $route = Yii::$app->controller->route;
        $assessmentIdParam = Yii::$app->request->get('assessment_id') ?? Yii::$app->request->get('id');
        
        // Check if on assessment/update or assessment/grade-grid with the same assessment ID
        if (($route === 'assessment/update' || $route === 'assessment/grade-grid') && 
            $assessmentIdParam == $assessmentId) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Render the return button
     */
    private function renderButton($assessmentId, $studentReg)
    {
        $returnUrl = ['assessment/update', 'assessment_id' => $assessmentId];
        
        $button = Html::a(
            '<i class="fas fa-arrow-left"></i> Back to Assessing ' . Html::encode($studentReg ?? 'Assessment'),
            $returnUrl,
            [
                'class' => 'btn btn-warning btn-sm',
                'title' => 'Return to the assessment you were working on'
            ]
        );
        
        // Return with CSS styling for sticky positioning
        return Html::tag('div', $button, [
            'class' => 'return-assessment-widget',
            'style' => 'position: sticky; top: 90px; z-index: 99; margin: 10px 0; padding: 10px 0;'
        ]);
    }
}
