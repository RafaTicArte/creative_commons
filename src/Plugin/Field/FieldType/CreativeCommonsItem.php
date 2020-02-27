<?php

namespace Drupal\creative_commons\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'creative_commons' field type.
 *
 * @FieldType(
 *   id = "creative_commons",
 *   label = @Translation("Creative Commons"),
 *   description = @Translation("This field stores a Creative Commons license and its data in the database."),
 *   default_widget = "creative_commons",
 *   default_formatter = "creative_commons"
 * )
 */
class CreativeCommonsItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
      'max_length' => 255,
      'max_length_id' => 20,
      'is_ascii' => FALSE,
      'case_sensitive' => FALSE,
    ] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = [
      'columns' => [
        'cc_id' => [
          'type' => 'varchar',
          'length' => (int) $field_definition->getSetting('max_length_id'),
          'not null' => FALSE,
        ],
        'work_title' => [
          'type' => 'varchar',
          'length' => (int) $field_definition->getSetting('max_length'),
          'not null' => FALSE,
        ],
        'work_link' => [
          'type' => 'varchar',
          'length' => (int) $field_definition->getSetting('max_length'),
          'not null' => FALSE,
        ],
        'author_name' => [
          'type' => 'varchar',
          'length' => (int) $field_definition->getSetting('max_length'),
          'not null' => FALSE,
        ],
        'author_link' => [
          'type' => 'varchar',
          'length' => (int) $field_definition->getSetting('max_length'),
          'not null' => FALSE,
        ],
        'source_title' => [
          'type' => 'varchar',
          'length' => (int) $field_definition->getSetting('max_length'),
          'not null' => FALSE,
        ],
        'source_link' => [
          'type' => 'varchar',
          'length' => (int) $field_definition->getSetting('max_length'),
          'not null' => FALSE,
        ],
      ],
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('cc_id')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['cc_id'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Creative Commons license ID'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'))
      ->setRequired(FALSE);

    $properties['work_title'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Work title'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'))
      ->setRequired(FALSE);

    $properties['work_link'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Work link'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'))
      ->setRequired(FALSE);

    $properties['author_name'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Author name'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'))
      ->setRequired(FALSE);

    $properties['author_link'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Author link'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'))
      ->setRequired(FALSE);

    $properties['source_title'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Source work title'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'))
      ->setRequired(FALSE);

    $properties['source_link'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Source work link'))
      ->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'))
      ->setRequired(FALSE);

    return $properties;
  }

}
