<?php

/** @var yii\web\View $this */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'TP Assessment System';
?>

<style>
    * {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
    }

    /* Hero Section */
    .hero-section {
        background: linear-gradient(135deg, #5B9BD5 0%, #2E75B6 100%);
        color: white;
        padding: 150px 0;
        margin-top: -16px;
        position: relative;
        overflow: hidden;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 500px;
        height: 500px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        transform: translate(150px, -150px);
    }

    .hero-section::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 400px;
        height: 400px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        transform: translate(-100px, 100px);
    }

    .hero-content {
        position: relative;
        z-index: 1;
        text-align: center;
        max-width: 700px;
    }

    .hero-title {
        font-size: clamp(1.6rem, 5vw, 3.5rem);
        font-weight: 800;
        margin-bottom: 20px;
        text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
        letter-spacing: -0.5px;
        line-height: 1.2;
        white-space: normal;
        overflow: visible;
        max-width: 100%;
        opacity: 0;
        transform: translateY(-20px);
        animation: headingFadeIn 1s ease 0.2s forwards;
    }

    @keyframes headingFadeIn {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .hero-section {
        background: linear-gradient(135deg, #5B9BD5 0%, #2E75B6 100%);
        color: white;
        padding: 40px 20px;
        margin-top: -16px;
        position: relative;
        overflow: hidden;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .hero-grid {
        display: flex;
        flex-wrap: nowrap;
        gap: 40px;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        max-width: 1300px;
        height: auto;
        padding: 0;
    }

    .hero-left {
        flex: 1;
        min-width: 0;
        text-align: left;
    }

    .hero-right {
        flex: 0 0 420px;
        text-align: left;
    }

    .hero-title {
        font-size: clamp(2rem, 4vw, 3.5rem);
        width: 100%;
        margin-bottom: 15px;
    }

    .hero-subtitle {
        font-size: clamp(1rem, 2vw, 1.2rem);
        margin-bottom: 15px;
    }

    .hero-left p {
        font-size: clamp(0.95rem, 1.5vw, 1.1rem);
        line-height: 1.5;
        margin-bottom: 10px;
    }

    .login-card {
        width: 100%;
    }

    @media (max-width: 992px) {
        .hero-section {
            padding: 30px 15px;
        }

        .hero-grid {
            gap: 30px;
            max-width: 100%;
        }

        .hero-left {
            min-width: 0;
        }

        .hero-right {
            flex: 0 0 380px;
        }

        .hero-title {
            font-size: clamp(1.6rem, 4vw, 2.8rem);
        }

        .hero-right .login-card {
            padding: 20px;
        }
    }

    @media (max-width: 768px) {
        .hero-section {
            padding: 30px 10px;
            min-height: auto;
        }

        .hero-grid {
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .hero-left,
        .hero-right {
            max-width: 100%;
            min-width: auto;
            flex: 0 0 100%;
        }

        .hero-left {
            text-align: center;
        }

        .hero-right {
            text-align: center;
        }

        .hero-title {
            font-size: clamp(1.4rem, 5vw, 2.4rem);
        }

        .login-card {
            margin: 0 auto;
            max-width: 100%;
        }
    }

    /* Button Styles */
    .btn-login {
        padding: 16px 60px;
        font-size: 1.15rem;
        font-weight: 700;
        border-radius: 10px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        display: inline-block;
        text-decoration: none;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .btn-login-primary {
        background-color: white;
        color: #2E75B6;
    }

    .btn-login-primary:hover {
        background-color: #f5f5f5;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        transform: translateY(-3px);
    }

    .hero-right .login-card {
        background: rgba(255, 255, 255, 0.97);
        color: #1a1a1a;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.35);
    }

    .hero-right .login-card h2 {
        margin-top: 0;
        margin-bottom: 8px;
        font-size: 1.4rem;
        color: #2E75B6;
    }

    .hero-right .login-card p {
        margin: 5px 0 12px;
        color: #555;
        font-size: 0.9rem;
    }

    .hero-right .form-field {
        margin-bottom: 10px;
    }

    .hero-right .form-field label {
        display: block;
        font-weight: 700;
        margin-bottom: 4px;
        color: #333;
        font-size: 0.9rem;
    }

    .input-placeholder {
        background: #f8f9fb;
        border: 1px solid #d2d8e0;
        border-radius: 8px;
        padding: 10px 12px;
        color: #666;
    }

    .form-control {
        padding: 8px 10px !important;
        font-size: 0.95rem !important;
        border: 1px solid #d2d8e0 !important;
        border-radius: 6px !important;
        height: auto !important;
    }

    .form-control:focus {
        border-color: #2E75B6 !important;
        box-shadow: 0 0 0 3px rgba(46, 117, 182, 0.1) !important;
    }

    .form-check {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 8px;
        margin-bottom: 12px;
        font-size: 0.9rem;
    }

    .login-hint {
        background: #e8f1ff;
        border-left: 4px solid #2E75B6;
        padding: 10px 10px 10px 12px;
        border-radius: 6px;
        color: #2b4d7a;
        font-size: 0.85rem;
        line-height: 1.4;
        margin-bottom: 12px;
    }

    .btn-block {
        width: 100%;
        text-align: center;
        margin-top: 3px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #5B9BD5, #2E75B6);
        border-color: #2E75B6;
        color: white;
        font-weight: 700;
        border-radius: 8px;
        padding: 10px 0;
        font-size: 0.95rem;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #2E75B6, #1f5194);
        color: white;
    }

    /* Footer */
    .footer-simple {
        background: #2E75B6;
        color: white;
        padding: 30px 0;
        text-align: center;
        margin-top: 0;
        font-size: 0.95rem;
    }

    .footer-simple p {
        margin: 0;
        opacity: 0.9;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.2rem;
        }

        .hero-subtitle {
            font-size: 1.1rem;
        }

        .hero-description {
            font-size: 1rem;
        }

        .btn-login {
            padding: 14px 40px;
            font-size: 1rem;
            display: block;
            margin: 10px auto;
            width: 100%;
            max-width: 300px;
        }

        .hero-section {
            padding: 100px 20px;
            min-height: auto;
        }
    }
</style>

<div class="site-index">
    <!-- Top Header -->
    <div style="text-align: center; padding: 30px 0; background: linear-gradient(135deg, #5B9BD5 0%, #2E75B6 100%); border-bottom: 1px solid #1f5194;">
        <h2 style="margin: 0; color: black; font-weight: 700; font-size: 3rem;">TP Assessment Portal</h2>
    </div>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-grid">
            <div class="hero-left">
                <h1 class="hero-title">Welcome to TP Assessment Portal</h1>
                <p class="hero-subtitle">Your centralized teaching practice assessment platform.</p>
                <?php if (Yii::$app->user->isGuest): ?>
                <p style="margin-top: 20px; font-size: 1.05rem; color: rgba(255,255,255,0.95);">To continue, sign in using your payroll number and password.</p>
                <?php else: ?>
                <p style="margin-top: 20px; font-size: 1.05rem; color: rgba(255,255,255,0.95);">You are logged in. Go to the dashboard to access your modules.</p>
                <?php endif; ?>
            </div>

            <div class="hero-right">
                <div class="login-card">
                    <h2>Login</h2>
                    <p>Sign in to your account</p>

                    <?php if (Yii::$app->session->hasFlash('success')): ?>
                        <div class="alert alert-success">
                            <?= Yii::$app->session->getFlash('success') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (Yii::$app->session->hasFlash('error')): ?>
                        <div class="alert alert-danger">
                            <?= Yii::$app->session->getFlash('error') ?>
                        </div>
                    <?php endif; ?>

                    <?php $form = ActiveForm::begin([ 'id' => 'landing-login-form', 'action' => ['site/index'], 'options' => ['autocomplete' => 'off'] ]); ?>

                    <?php if ($model->hasErrors()): ?>
                        <div class="alert alert-danger">
                            <strong>Login Failed:</strong>
                            <?php foreach ($model->getErrors() as $attribute => $errors): ?>
                                <?php foreach ($errors as $error): ?>
                                    <div><?= $error ?></div>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?= $form->field($model, 'payroll_no')->textInput(['autofocus' => true, 'placeholder' => 'Payroll number or username', 'class' => 'form-control'])->label('Payroll Number or Username'); ?>

                    <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Password', 'class' => 'form-control'])->label('Password'); ?>

                    <?= $form->field($model, 'rememberMe')->checkbox([ 'template' => "<div class=\"form-check\">{input} {label}</div>\n{error}", 'labelOptions' => ['class' => 'form-check-label'], 'inputOptions' => ['class' => 'form-check-input'], ]); ?>

                    <div class="login-hint">
                        <strong>Demo Credentials:</strong><br>
                        Username: admin / Password: admin<br>
                        Username: demo / Password: demo<br>
                        Username: supervisor1 / Password: password123<br>
                        Username: coordinator1 / Password: password123<br>
                        Username: tpoffice1 / Password: password123<br>
                        Username: chair1 / Password: password123
                    </div>

                    <div class="form-group">
                        <?= Html::submitButton('Login', ['class' => 'btn btn-primary btn-block', 'name' => 'login-button']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                    <?php if (!Yii::$app->user->isGuest): ?>
                        <?= Html::a('Go to Dashboard', ['/site/dashboard'], ['class' => 'btn btn-secondary btn-block', 'style' => 'margin-top: 10px;']) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>
