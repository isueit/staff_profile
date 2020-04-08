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
        '#type' => 'textfield',
        '#title' => $this->t('URL of JSON Feed'),
        '#description' => $this->t('URL of JSON feed to populate staff for this site.'),
        '#maxlength' => 255,
        '#size' => 64,
        '#default_value' => !empty($config->get('sync_url')) ? $config->get('sync_url') : '',
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
        ->set('sync_url', $form_state->getValue('sync_url'))
        ->save();

  }
}
