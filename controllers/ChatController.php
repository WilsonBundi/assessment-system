<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use app\components\ChatService;

/**
 * ChatController handles AI chatbot interactions
 */
class ChatController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // Authenticated users only
                    ],
                ],
            ],
        ];
    }

    /**
     * Send message and get AI response
     */
    public function actionSend()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        try {
            $request = Yii::$app->request;
            if (!$request->isPost) {
                return ['success' => false, 'error' => 'POST request required'];
            }

            $message = $request->post('message', '');
            $assessmentId = $request->post('assessment_id');
            $context = $request->post('context', []);

            if (empty($message)) {
                return ['success' => false, 'error' => 'Message cannot be empty'];
            }

            // Get current user
            $user = Yii::$app->user->identity;
            $userId = $user->user_id;

            // Store message in session history
            if (!isset(Yii::$app->session['chatHistory'])) {
                Yii::$app->session['chatHistory'] = [];
            }
            
            $chatHistory = Yii::$app->session['chatHistory'];
            $chatHistory[] = [
                'role' => 'user',
                'message' => $message,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            // Get AI response with error handling
            $chatService = new ChatService();
            $response = $chatService->generateResponse($message, $context, $user, $assessmentId);
            
            // Ensure response is not empty
            if (empty($response)) {
                $response = "I'm here to help! Feel free to ask me about:\n• Assessment and grading\n• System statistics\n• General knowledge\n• Teaching tips\n\nWhat can I help you with?";
            }

            $chatHistory[] = [
                'role' => 'assistant',
                'message' => $response,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            // Keep only last 20 messages
            if (count($chatHistory) > 20) {
                $chatHistory = array_slice($chatHistory, -20);
            }

            Yii::$app->session['chatHistory'] = $chatHistory;

            return [
                'success' => true,
                'message' => $response,
                'history' => $chatHistory
            ];
        } catch (\Exception $e) {
            Yii::error('ChatController Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Unable to process your request. I\'m here to help! Try asking about assessments, grades, or general topics.',
                'message' => 'I\'m here to help! Feel free to ask about:\n• Assessment and grading\n• System statistics\n• General knowledge\n• Teaching tips\n\nWhat can I help you with?'
            ];
        }
    }

    /**
     * Get chat history
     */
    public function actionHistory()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $history = Yii::$app->session['chatHistory'] ?? [];
        
        return [
            'history' => $history
        ];
    }

    /**
     * Clear chat history
     */
    public function actionClear()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->session->remove('chatHistory');
        
        return [
            'success' => true,
            'message' => 'Chat history cleared'
        ];
    }
}
