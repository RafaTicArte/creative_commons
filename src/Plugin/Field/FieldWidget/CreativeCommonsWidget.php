<?php

namespace Drupal\creative_commons\Plugin\Field\FieldWidget;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\creative_commons\Repository\CreativeCommonsRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'creative_commons' widget.
 *
 * @FieldWidget(
 *   id = "creative_commons",
 *   module = "creative_commons",
 *   label = @Translation("Creative Commons"),
 *   field_types = {
 *     "creative_commons"
 *   }
 * )
 */
class CreativeCommonsWidget extends WidgetBase implements ContainerFactoryPluginInterface {
  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, ConfigFactoryInterface $config_factory) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'size' => 60,
      'max_length' => 255,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $config = $this->configFactory->get('creative_commons.settings');
    $creativeCommonsRepository = CreativeCommonsRepository::getInstance();

    $element = [
      '#type' => 'details',
      '#title' => $items->getFieldDefinition()->getLabel(),
      '#open' => TRUE,
      '#element_validate' => [
        [$this, 'validateForm'],
      ],
    ];

    $element['cc_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Creative Commons license type'),
      '#default_value' => isset($items[$delta]->cc_id) ? $items[$delta]->cc_id : NULL,
      '#required' => FALSE,
      '#options' => $creativeCommonsRepository->getLicenses($config->get('versions')),
    ];

    $element['work_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Work title'),
      '#description' => $this->t('Leave blank to show the title of content. Only available in <em>node</em> entities.'),
      '#default_value' => isset($items[$delta]->work_title) ? $items[$delta]->work_title : NULL,
      '#size' => $this->getSetting('size'),
      '#maxlength' => $this->getSetting('max_length'),
      '#required' => FALSE,
    ];

    $element['work_link'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Work link'),
      '#description' => $this->t('Leave blank to link content. Only available in <em>node</em> entities.'),
      '#default_value' => isset($items[$delta]->work_link) ? $items[$delta]->work_link : NULL,
      '#size' => $this->getSetting('size'),
      '#maxlength' => $this->getSetting('max_length'),
      '#required' => FALSE,
    ];

    $element['author_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Author name'),
      '#description' => $this->t('Leave blank to show the author name of content. Only available in <em>node</em> entities.'),
      '#default_value' => isset($items[$delta]->author_name) ? $items[$delta]->author_name : NULL,
      '#size' => $this->getSetting('size'),
      '#maxlength' => $this->getSetting('max_length'),
      '#required' => FALSE,
    ];

    $element['author_link'] = [
      '#type' => 'url',
      '#title' => $this->t('Author link'),
      '#description' => $this->t('Leave blank to link the author of content. Only available in <em>node</em> entities.'),
      '#default_value' => isset($items[$delta]->author_link) ? $items[$delta]->author_link : NULL,
      '#size' => $this->getSetting('size'),
      '#maxlength' => $this->getSetting('max_length'),
      '#required' => FALSE,
    ];

    $element['source_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Source work title'),
      '#description' => $this->t('Title of work in which it is based on.'),
      '#default_value' => isset($items[$delta]->source_title) ? $items[$delta]->source_title : NULL,
      '#size' => $this->getSetting('size'),
      '#maxlength' => $this->getSetting('max_length'),
      '#required' => FALSE,
    ];

    $element['source_link'] = [
      '#type' => 'url',
      '#title' => $this->t('Source work link'),
      '#description' => $this->t('Link of work in which it is based on.'),
      '#default_value' => isset($items[$delta]->source_link) ? $items[$delta]->source_link : NULL,
      '#size' => $this->getSetting('size'),
      '#maxlength' => $this->getSetting('max_length'),
      '#required' => FALSE,
    ];

    return $element;
  }

  /**
   * Validate form elements and show errors.
   *
   * This function is assigned as an #element_validate
   * callback in formElement().
   */
  public function validateForm($element, FormStateInterface $form_state) {
    if ($element['work_title']['#value'] != strip_tags($element['work_title']['#value'])) {
      $form_state->setError($element['work_title'], $this->t('No HTML tags allowed.'));
    }
    if ($element['author_name']['#value'] != strip_tags($element['author_name']['#value'])) {
      $form_state->setError($element['author_name'], $this->t('No HTML tags allowed.'));
    }
    if ($element['source_title']['#value'] != strip_tags($element['source_title']['#value'])) {
      $form_state->setError($element['source_title'], $this->t('No HTML tags allowed.'));
    }
    if ($element['author_link']['#value'] != '' && $element['author_name']['#value'] == '') {
      $form_state->setError($element['author_name'], $this->t('Author name cannot be blank if you fill in author link.'));
    }
    if ($element['source_link']['#value'] != '' && $element['source_title']['#value'] == '') {
      $form_state->setError($element['source_title'], $this->t('Source work title cannot be blank if you fill in source work link.'));
    }
  }

}
