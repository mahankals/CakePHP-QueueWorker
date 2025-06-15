<?php

namespace QueueWorker\Controller;

use App\Controller\AppController;
use Cake\Http\Exception\ForbiddenException;

/**
 * Queue Jobs Controller
 */
class JobsController extends AppController
{
  protected \QueueWorker\Model\Table\QueueJobsTable $JobsTable;

  public function initialize(): void
  {
    parent::initialize();
    $this->JobsTable = $this->fetchTable('QueueWorker.QueueJobs');
  }


  /**
   * Route access permission can be ovewrite from
   * src\Application.php in bootstrap method
   *
   * \QueueWorker\Controller\QueueJobsController::$accessPolicy = fn($request) =>
   *    $request->getAttribute('identity')?->get('role') === 'admin';
   */
  public static ?\Closure $accessPolicy = null;

  public function beforeFilter(\Cake\Event\EventInterface $event)
  {
    parent::beforeFilter($event);

    $check = static::$accessPolicy ?? fn($r) => true;

    if (!$check || !call_user_func($check, $this->getRequest())) {
      throw new ForbiddenException('Access denied to queue.');
    }
  }

  public function index()
  {
    $query = $this->JobsTable->find()
      ->orderBy(['QueueJobs.created' => 'DESC'])
      ->limit(100);

    $this->set('queueJobs', $this->paginate($query));
  }

  public function retry($id = null)
  {
    $this->request->allowMethod(['post', 'put']);

    $query = $this->JobsTable->find()
      ->where(['status' => 'failed']);

    if (is_numeric($id)) {
      $query->where(['id' => $id]);
    } elseif (is_string($id)) {
      $query->where(['task' => $id]);
    }

    $jobs = $query->all();

    if ($jobs->isEmpty()) {
      $this->Flash->error("No matching failed jobs to retry.");
    } else {
      foreach ($jobs as $job) {
        $job->status = 'queued';
        $job->attempts += 1;
        $this->JobsTable->save($job);
      }

      $this->Flash->success("Retried " . count($jobs) . " job(s).");
    }

    return $this->redirect(['action' => 'index']);
  }

  public function repeat($id = null)
  {
    $this->request->allowMethod(['post', 'put']);

    $original = $this->JobsTable->find()
      ->where(['status' => 'completed'])
      ->first();

    if (!$original) {
      $this->Flash->error("Job not found.");
      return $this->redirect(['action' => 'index']);
    }

    $duplicate = $this->JobsTable->newEntity([
      'title' => $original->title,
      'task' => $original->task,
      'data' => $original->data,
      'status' => 'queued',
      'attempts' => 0,
      'max_attempts' => $original->max_attempts,
    ]);

    if ($this->JobsTable->save($duplicate)) {
      $this->Flash->success("Duplicated job ID {$id} as new job ID {$duplicate->id}.");
    } else {
      $this->Flash->error("Failed to duplicate job.");
    }

    return $this->redirect(['action' => 'index']);
  }
}
