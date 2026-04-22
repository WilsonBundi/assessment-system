<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <h4 class="alert-heading">Something went wrong.</h4>
        <p>
            We could not complete your request at this time. Please try one of the options below.
        </p>
        <?php if (!empty($message)): ?>
            <hr>
            <p class="mb-0">
                <strong>Error detail:</strong> <?= nl2br(Html::encode($message)) ?>
            </p>
        <?php endif; ?>
    </div>

    <div class="alert alert-secondary">
        <p><strong>What you can do next:</strong></p>
        <ul>
            <li>Refresh the page and try again.</li>
            <li>Use the navigation menu to return to a different section.</li>
            <li>If you were entering data, please check that all required fields are complete.</li>
        </ul>
    </div>

    <p>
        If this problem persists, contact support and tell them what you were doing when the error happened.
    </p>

    <p>
        <?= Html::a('Return to the Home Page', Yii::$app->homeUrl, ['class' => 'btn btn-primary']) ?>
    </p>

</div>
