<?php
$this->title = _e('Not found');
$this->breadcrumbs[] = ['label' => _e('Employees'), 'url' => $main_url]; ?>

<div class="card">
    <div class="card-body page-item-not-found">
        <i class="ri-error-warning-line"></i>
        <h3><?= _e('Employee not found!'); ?></h3>
        <p><?= _e('The employee you were looking for does not exist, unavailable for you or deleted.'); ?></p>

        <a href="<?= $main_url; ?>" class="btn btn-secondary waves-effect btn-with-icon">
            <i class="ri-arrow-left-line mr-1"></i>
            <?= _e('Back to employees'); ?>
        </a>
    </div>
</div>