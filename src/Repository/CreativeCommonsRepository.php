<?php

namespace Drupal\creative_commons\Repository;

use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Defines a repository for storage Creative Commons licenses.
 */
class CreativeCommonsRepository {
  use StringTranslationTrait;

  /**
   * Object for Singleton instance.
   *
   * @var instance
   */
  private static $instance;

  /**
   * Array with the content of CreativeCommonsLicense.json file.
   *
   * @var repository
   */
  private $repository;

  /**
   * Construct for loading CreativeCommonsLicense.json file.
   */
  private function __construct() {
    $file = file_get_contents(drupal_get_path('module', 'creative_commons') . '/includes/CreativeCommonsLicenses.json');
    if ($file == FALSE) {
      \Drupal::logger('creative_commons')->error('<em>CreativeCommonsLicenses.json</em> file is not found');
      $this->repository = [];
    }
    else {
      $this->repository = json_decode($file, TRUE);
      if ($this->repository == NULL) {
        \Drupal::logger('creative_commons')->error('<em>CreativeCommonsLicenses.json</em> file is not JSON valid');
        $this->repository = [];
      }
    }
  }

  /**
   * Singleton pattern.
   *
   * @return object
   *   Instance of CreativeCommonsRepository.
   */
  public static function getInstance() {
    if (!self::$instance instanceof self) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  /**
   * Get translation name of Creative Commons license.
   *
   * @param string $cc_id
   *   Identifier of license.
   * @param string $name_length
   *   Length of license name.
   *
   * @return string
   *   Translation name of license.
   */
  public function getName($cc_id, $name_length = 'long') {
    if (empty($this->repository)) {
      return '';
    }

    if ($name_length == 'short') {
      $name = 'CC ';
      $name .= strtoupper($this->repository['types'][$cc_id]['terms']) . ' ';
      $name .= $this->repository['types'][$cc_id]['version'];
    }
    else {
      $name = $this->t('Creative Commons') . ' ';

      switch ($this->repository['types'][$cc_id]['terms']) {
        case 'by':
          $name .= $this->t('Attribution') . ' ';
          break;

        case 'by-nc':
          $name .= $this->t('Attribution') . '-' . $this->t('NonCommercial') . ' ';
          break;

        case 'by-nc-nd':
          $name .= $this->t('Attribution') . '-' . $this->t('NonCommercial') . '-' . $this->t('NoDerivatives') . ' ';
          break;

        case 'by-nc-sa':
          $name .= $this->t('Attribution') . '-' . $this->t('NonCommercial') . '-' . $this->t('ShareAlike') . ' ';
          break;

        case 'by-nd':
          $name .= $this->t('Attribution') . '-' . $this->t('NoDerivatives') . ' ';
          break;

        case 'by-sa':
          $name .= $this->t('Attribution') . '-' . $this->t('ShareAlike') . ' ';
          break;

        case 'zero':
          $name .= $this->t('Zero') . ' ';
          break;
      }

      switch ($this->repository['types'][$cc_id]['version']) {
        case '4.0':
          $name .= $this->repository['types'][$cc_id]['version'] . ' ' . $this->t('International');
          break;

        case '3.0':
          $name .= $this->repository['types'][$cc_id]['version'] . ' ' . $this->t('Unported');
          break;

        case '2.5':
        case '2.0':
        case '1.0':
          $name .= $this->repository['types'][$cc_id]['version'] . ' ' . $this->t('Generic');
          break;

        case 'zero':
          $name .= $this->repository['types'][$cc_id]['version'] . ' ' . $this->t('Universal');
          break;
      }
    }

    return $name;
  }

  /**
   * Get link to image license.
   *
   * @param string $cc_id
   *   Identifier of license.
   * @param string $size
   *   Size of image license.
   * @param string $source
   *   Source of image license.
   *
   * @return string
   *   Link to image license.
   */
  public function getImage($cc_id, $size = 'medium', $source = 'local') {
    if (empty($this->repository)) {
      return '';
    }

    global $base_path;
    $terms = $this->repository['types'][$cc_id]['terms'];
    $version = $this->repository['types'][$cc_id]['version'];

    $image = '';

    switch ($source) {
      case 'local':
        $image .= $base_path . drupal_get_path('module', 'creative_commons') . '/images/';
        break;

      case 'licensebuttons':
        $image .= 'https://licensebuttons.net/l/';
        break;
    }

    $image .= $terms . '/' . $version . '/';

    switch ($size) {
      case 'small':
        $image .= '80x15.png';
        break;

      case 'medium':
        $image .= '88x31.png';
        break;
    }

    return $image;
  }

  /**
   * Get link to legal information license.
   *
   * @param string $cc_id
   *   Identifier of license.
   *
   * @return string
   *   Link to legal information license.
   */
  public function getLegal($cc_id) {
    if (empty($this->repository)) {
      return '';
    }

    $terms = $this->repository['types'][$cc_id]['terms'];
    $version = $this->repository['types'][$cc_id]['version'];

    return 'https://creativecommons.org/licenses/' . $terms . '/' . $version . '/';
  }

  /**
   * Check if a license is Zero type.
   *
   * @param string $cc_id
   *   Identifier of license.
   *
   * @return bool
   *   TRUE if license is Zero type.
   */
  public function isZero($cc_id) {
    if ($this->repository['types'][$cc_id]['terms'] == 'zero') {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Defines ....
   *
   * $config->get('versions') viene con valores a 0
   */
  public function getLicenses($versions = []) {
    $licenses = ['' => 'None'];

    if (empty($this->repository)) {
      return $licenses;
    }

    foreach ($versions as $cc_version) {
      if ($cc_version != '0') {
        $licenses[$this->repository['versions'][$cc_version]['title']] = [];
        foreach ($this->repository['versions'][$cc_version]['types'] as $item) {
          $licenses[$this->repository['versions'][$cc_version]['title']] += [$item => $this->getName($item)];
        }
      }
    }

    return $licenses;
  }

  /**
   * Get Creative Commons versions available.
   *
   * @return array
   *   Array with Creative Commons versions id and name.
   */
  public function getVersions() {
    $versions = [];

    foreach ($this->repository['versions'] as $cc_version_id => $cc_version_data) {
      $versions[$cc_version_id] = $cc_version_data['title'];
    }

    return $versions;
  }

}
