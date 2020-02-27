<?php

namespace Drupal\creative_commons\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\creative_commons\Repository\CreativeCommonsRepository;

/**
 * Provides a form for setting module.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'creative_commons.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'creative_commons_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $config = $this->config('creative_commons.settings');
    $creativeCommonsRepository = CreativeCommonsRepository::getInstance();

    $form['versions'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('License versions available'),
      '#description' => $this->t('Creative Commons license versions available to be choosen by users.'),
      '#options' => $creativeCommonsRepository->getVersions(),
      '#default_value' => $config->get('versions'),
    ];

    $form['image_repository'] = [
      '#type' => 'select',
      '#title' => $this->t('Image repository'),
      '#description' => $this->t('Repository used to load image licenses.'),
      '#options' => [
        'local' => $this->t('Module folder'),
        'licensebuttons' => $this->t('Licensebuttons.net'),
      ],
      '#size' => 1,
      '#default_value' => $config->get('image_repository'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('creative_commons.settings')
      ->set('versions', $form_state->getValue('versions'))
      ->set('image_repository', $form_state->getValue('image_repository'))
      ->save();
  }

}
