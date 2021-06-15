<?php

namespace Drupal\creative_commons\Plugin\Field\FieldFormatter;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\creative_commons\Repository\CreativeCommonsRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'creative_commons' formatter.
 *
 * @FieldFormatter(
 *   id = "creative_commons",
 *   label = @Translation("Creative Commons"),
 *   field_types = {
 *     "creative_commons"
 *   }
 * )
 */
class CreativeCommonsFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $routeMatch;

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, ConfigFactoryInterface $config_factory, CurrentRouteMatch $route_match) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->configFactory = $config_factory;
    $this->routeMatch = $route_match;
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
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('config.factory'),
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'license_name' => 'long',
      'image_position' => 'above',
      'image_size' => 'medium',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements['license_name'] = [
      '#title' => $this->t('License name'),
      '#type' => 'select',
      '#options' => [
        'long' => $this->t('Long'),
        'short' => $this->t('Short'),
      ],
      '#default_value' => $this->getSetting('license_name'),
    ];

    $elements['image_position'] = [
      '#title' => $this->t('Image position'),
      '#type' => 'select',
      '#options' => [
        'above' => $this->t('Above'),
        'inline' => $this->t('Inline'),
        'hidden' => $this->t('Hidden'),
      ],
      '#default_value' => $this->getSetting('image_position'),
    ];

    $elements['image_size'] = [
      '#title' => $this->t('Image size'),
      '#type' => 'select',
      '#options' => [
        'medium' => $this->t('Medium'),
        'small' => $this->t('Small'),
      ],
      '#default_value' => $this->getSetting('image_size'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $summary[] = $this->t('License name: @license_name', ['@license_name' => ucfirst($this->getSetting('license_name'))]);
    $summary[] = $this->t('License image position: @image_position', ['@image_position' => ucfirst($this->getSetting('image_position'))]);
    $summary[] = $this->t('License image size: @image_size', ['@image_size' => ucfirst($this->getSetting('image_size'))]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $config = $this->configFactory->get('creative_commons.settings');
    $creativeCommonsRepository = CreativeCommonsRepository::getInstance();
    $entity = $items->getEntity();

    $elements = [];

    foreach ($items as $delta => $item) {
      if ($item->work_title == '' and $entity->getEntityTypeId() == 'node') {
        $work_title = $entity->getTitle();
        $work_link = $entity->toURL()->toString();
      }
      else {
        $work_title = $item->work_title;
        $work_link = $item->work_link;
      }

      if ($item->author_name == '' and $entity->getEntityTypeId() == 'node') {
        $author_name = $entity->getOwner()->getDisplayName();
        $author_link = $entity->getOwner()->toURL()->toString();
      }
      elseif ($creativeCommonsRepository->isZero($item->cc_id)) {
        $author_name = '';
        $author_link = '';
      }
      else {
        $author_name = $item->author_name;
        $author_link = $item->author_link;
      }

      $elements[$delta] = [
        '#theme' => 'creative_commons',
        '#image_position' => $this->getSetting('image_position'),
        '#cc_image' => $creativeCommonsRepository->getImage($item->cc_id, $this->getSetting('image_size'), $config->get('image_repository')),
        '#cc_name' => $creativeCommonsRepository->getName($item->cc_id, $this->getSetting('license_name')),
        '#cc_legal' => $creativeCommonsRepository->getLegal($item->cc_id),
        '#work_title' => $work_title,
        '#work_link' => $work_link,
        '#author_name' => $author_name,
        '#author_link' => $author_link,
        '#source_title' => $item->source_title,
        '#source_link' => $item->source_link,
      ];
    }

    return $elements;
  }

}
