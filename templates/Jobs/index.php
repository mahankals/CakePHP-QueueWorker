<?php

/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\QueueJob> $QueueJobs
 */
?>
<div class="index content">
  <?= $this->Form->postLink('Retry All Failed Jobs', ['action' => 'retry'], ['class' => 'button float-right']) ?>
  <h3><?= __('Queued Jobs') ?></h3>
  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th><?= $this->Paginator->sort('id', 'ID') ?></th>
          <th><?= $this->Paginator->sort('title', 'Title') ?></th>
          <th><?= $this->Paginator->sort('job', 'Job') ?></th>
          <th><?= $this->Paginator->sort('status', 'Status') ?></th>
          <th><?= $this->Paginator->sort('attempts', 'Attempts') ?></th>
          <th><?= $this->Paginator->sort('created', 'Created') ?></th>
          <th><?= $this->Paginator->sort('updated', 'Last Attempted at') ?></th>
          <th class="actions"><?= __('Actions') ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($queueJobs as $index => $job): ?>
          <?php
          $jobTitle = $job->title ?? $job->job;
          ?>
          <tr>
            <td><?= $this->Number->format($index + 1) ?></td>
            <td><?= h($job->id) ?></td>
            <td><?= h($job->title) ?></td>
            <td><?= h($job->job) ?></td>
            <td title="<?= $job->error ?>">
              <?= h($job->status) ?>
              <?php if ($job->error): ?>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" height="18">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>

              <?php endif; ?>
            </td>
            <td><?= h($job->attempts) ?> / <?= h($job->max_attempts) ?></td>
            <td><?= h($job->created->format('d M Y, H:i:s')) ?></td>
            <td><?= h($job->modified->format('d M Y, H:i:s')) ?></td>
            <td class="actions">

              <!-- <?= $this->Html->link(__('View'), ['action' => 'view', 0]) ?>
              <?= $this->Html->link(__('Edit'), ['action' => 'edit', 0]) ?>
              <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', 0], ['confirm' => __('Are you sure you want to delete # {0}?', 0)]) ?> -->

              <?php if ($job->status === 'failed'): ?>
                <?= $this->Form->postLink(
                  'Retry',
                  ['action' => 'retry', $job->id],
                  ['confirm' => "Retry {$jobTitle} job?", 'title' => 'Retry', 'class' => 'button', 'style' => 'padding-left: 12px; padding-right: 12px; background-color: #dc3545; color: #fff', 'escape' => false]
                ) ?>
              <?php endif; ?>
              <?php if ($job->status === 'completed'): ?>
                <?= $this->Form->postLink(
                  'Clone',
                  ['action' => 'repeat', $job->id],
                  ['confirm' => "Repeat {$jobTitle} job?", 'title' => 'Clone', 'class' => 'button', 'style' => 'padding-left: 12px; padding-right: 12px; background-color: #198754; color: #fff', 'escape' => false]
                ) ?>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="paginator">
    <ul class="pagination">
      <?= $this->Paginator->first('<< ' . __('first')) ?>
      <?= $this->Paginator->prev('< ' . __('previous')) ?>
      <?= $this->Paginator->numbers() ?>
      <?= $this->Paginator->next(__('next') . ' >') ?>
      <?= $this->Paginator->last(__('last') . ' >>') ?>
    </ul>
    <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
  </div>
</div>
