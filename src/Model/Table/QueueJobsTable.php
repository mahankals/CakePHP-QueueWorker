<?php
declare(strict_types=1);

namespace QueueWorker\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * QueueJobs Model
 *
 * @method \QueueWorker\Model\Entity\QueueJob newEmptyEntity()
 * @method \QueueWorker\Model\Entity\QueueJob newEntity(array $data, array $options = [])
 * @method array<\QueueWorker\Model\Entity\QueueJob> newEntities(array $data, array $options = [])
 * @method \QueueWorker\Model\Entity\QueueJob get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \QueueWorker\Model\Entity\QueueJob findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \QueueWorker\Model\Entity\QueueJob patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\QueueWorker\Model\Entity\QueueJob> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \QueueWorker\Model\Entity\QueueJob|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \QueueWorker\Model\Entity\QueueJob saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\QueueWorker\Model\Entity\QueueJob>|\Cake\Datasource\ResultSetInterface<\QueueWorker\Model\Entity\QueueJob>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\QueueWorker\Model\Entity\QueueJob>|\Cake\Datasource\ResultSetInterface<\QueueWorker\Model\Entity\QueueJob> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\QueueWorker\Model\Entity\QueueJob>|\Cake\Datasource\ResultSetInterface<\QueueWorker\Model\Entity\QueueJob>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\QueueWorker\Model\Entity\QueueJob>|\Cake\Datasource\ResultSetInterface<\QueueWorker\Model\Entity\QueueJob> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class QueueJobsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('queue_jobs');
        $this->setDisplayField('job');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('title')
            ->maxLength('title', 255)
            ->allowEmptyString('title');

        $validator
            ->scalar('job')
            ->maxLength('job', 255)
            ->requirePresence('job', 'create')
            ->notEmptyString('job');

        $validator
            ->scalar('data')
            ->allowEmptyString('data');

        $validator
            ->scalar('status')
            ->maxLength('status', 50)
            ->allowEmptyString('status');

        $validator
            ->integer('progress')
            ->allowEmptyString('progress');

        $validator
            ->integer('attempts')
            ->allowEmptyString('attempts');

        $validator
            ->integer('max_attempts')
            ->allowEmptyString('max_attempts');

        $validator
            ->scalar('error')
            ->allowEmptyString('error');

        $validator
            ->scalar('trace')
            ->allowEmptyString('trace');

        return $validator;
    }

    public function createJob(string $job, array $data = [], ?string $title = null): bool
    {
        $job = $this->newEntity([
            'title' => $title,
            'job' => $job,
            'data' => json_encode($data),
            'status' => 'queued',
            'created' => new \DateTime()
        ]);
        return (bool)$this->save($job);
    }
}
