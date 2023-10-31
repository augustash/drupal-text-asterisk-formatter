<?php

namespace Drupal\text_asterisk_formatter\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'asterisk' formatter.
 *
 * @FieldFormatter(
 *   id = "asterisk",
 *   label = @Translation("Asterisk"),
 *   field_types = {
 *     "string",
 *     "string_long"
 *   }
 * )
 */
class TextAsteriskFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'strip' => FALSE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $elements['strip'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Strip'),
      '#description' => $this->t('Strip asterisk formatting from output.'),
      '#default_value' => $this->getSetting('strip'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    if ($this->getSetting('strip')) {
      $summary[] = $this->t('Remove asterisk formatting.');
    }
    else {
      $summary[] = $this->t('*<em>Italic</em>* | **<strong>Bold</strong>** | ***<strong><em>Bold Italic</em></strong>***');;
    }
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = ['#markup' => $this->viewValue($item)];
    }

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    // The text value has no text format assigned to it, so the user input
    // should equal the output, including newlines.
    if ($this->getSetting('strip')) {
      $value = preg_replace('#\*{3}(.*?)\*{3}#', '$1', $item->value);
      $value = preg_replace('#\*{2}(.*?)\*{2}#', '$1', $value);
      return preg_replace('#\*{1}(.*?)\*{1}#', '$1', $value);
    }
    else {
      $value = preg_replace('#\*{3}(.*?)\*{3}#', '<strong><em>$1</em></strong>', $item->value);
      $value = preg_replace('#\*{2}(.*?)\*{2}#', '<strong>$1</strong>', $value);
      return preg_replace('#\*{1}(.*?)\*{1}#', '<em>$1</em>', $value);
    }
  }

}
