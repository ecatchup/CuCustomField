<?php
declare(strict_types=1);

namespace CuCustomField\Model\Entity;

use Cake\ORM\Entity;

/**
 * CuCustomFieldConfig Entity
 *
 * @property int $id
 * @property int|null content_id
 * @property bool|null $status
 * @property string|null $form_place
 * @property string|null $model
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property \Cake\I18n\FrozenTime|null $created
 *
 * @property \uCustomField\Model\Entity\BlogContent $blog_content
 */
class CuCustomFieldConfig extends Entity
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
        'content_id' => true,
        'status' => true,
        'form_place' => true,
        'model' => true,
        'modified' => true,
        'created' => true,
    ];
}
