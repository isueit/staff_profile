<?php

namespace Drupal\staff_profile_secondary\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/*
 * Class SettingsForm
 */
class SettingsForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'staff_profile_secondary.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
   public function getFormID() {
     return 'settings_form';
   }

   /**
    * {@inheritdoc}
    */
    public function buildForm(array $form, FormStateInterface $form_state) {
      $config = $this->config('staff_profile_secondary.settings');
      $form['sync_url'] = array(
        '#type' => 'textarea',
        '#title' => $this->t('URLs of JSON Feeds'),
        '#description' => $this->t('URLs of JSON feeds to populate staff for this site. Use %20 instead of spaces, ie black%20hawk & pottawattamie%20-%20west <br>Separate each url with a new line'),
        '#size' => 64,
        '#default_value' => !empty($config->get('sync_url')) ? preg_replace('/,/',"\r\n",$config->get('sync_url')) : '',
        '#required' => TRUE,
      );

      return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
      parent::submitForm($form, $form_state);
      //If checked, run sync
      $this->config('staff_profile_secondary.settings')
        ->set('sync_url', preg_replace('/\s+(?=[hw])/',',',trim($form_state->getValue('sync_url'))))
        ->save();
  }
}
