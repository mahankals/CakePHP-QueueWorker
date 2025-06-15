<?php
declare(strict_types=1);

namespace QueueWorker\Model\Entity;

use Cake\ORM\Entity;

/**
 * QueueJob Entity
 *
 * @property int $id
 * @property string|null $title
 * @property string $job
 * @property string|null $data
 * @property string|null $status
 * @property int|null $progress
 * @property int|null $attempts
 * @property int|null $max_attempts
 * @property string|null $error
 * @property string|null $trace
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 */
class QueueJob extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'title' => true,
        'job' => true,
        'data' => true,
        'status' => true,
        'progress' => true,
        'attempts' => true,
        'max_attempts' => true,
        'error' => true,
        'trace' => true,
        'created' => true,
        'modified' => true,
    ];
}
