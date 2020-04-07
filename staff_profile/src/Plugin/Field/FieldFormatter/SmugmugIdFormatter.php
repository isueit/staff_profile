<?php

/**
 * @file
 * Contains Drupal\drupal8_field_formatters\Plugin\Field\FieldFormatter\SmugmugIdFormatter.
 */

namespace Drupal\staff_profile\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'smugmug_id_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "smugmug_id_formatter",
 *   label = @Translation("Smugmug Image"),
 *   weight = "10",
 *   field_types = {
 *     "string",
 *     "text",
 *   },
 *   quickedit = {
 *     "editor" = "plain_text"
 *   }
 * )
 */
class SmugmugIdFormatter extends FormatterBase
{
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode)
  {
    $element = [];
    foreach ($items as $delta => $item) {
      // Render each element as markup.
      $htmloutput = '';

      if (!empty($item->value)) {
        $htmloutput = '<img class="staff_profile_smugmug" src="https://photos.smugmug.com/photos/' . $item->value . '/0/XL/' . $item->value . '-XL.jpg" alt="Staff photo" />' ;
      }

      $element[$delta] = [
        '#type' => 'markup',
        '#markup' => $htmloutput,
      ];
    }
    return $element;
  }
}
