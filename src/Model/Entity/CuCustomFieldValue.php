<?php
declare(strict_types=1);

namespace CuCustomField\Model\Entity;

use Cake\ORM\Entity;

/**
 * CuCustomField Entity
 *
 * @property int $id
 * @property int|null $relate_id
 * @property string|null $key
 * @property string|null $value
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property \Cake\I18n\FrozenTime|null $created
 */
class CuCustomFieldValue extends Entity
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
        'id' => true,
        'relate_id' => true,
        'key' => true,
        'value' => true,
        'modified' => true,
        'created' => true,
    ];
}
