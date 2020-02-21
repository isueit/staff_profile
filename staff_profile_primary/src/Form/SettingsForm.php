<?php

namespace Drupal\staff_profile_primary\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\encrypt\Entity\EncryptionProfile;

/*
 * Class SettingsForm
 */
class SettingsForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'staff_profile_primary.settings',
    ]; }

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
      $config = $this->config('staff_profile_primary.settings');
      $form['minimum_staff'] = array(
        '#type' => 'number',
        '#title' => $this->t('Minimum Number of Staff'),
        '#description' => $this->t('Minimum number of staff we should expect from the database. If we get less than this, then something\'s wrong, don\'t process the records.'),
        '#maxlength' => 4,
        '#size' => 4,
        '#default_value' => !empty($config->get('minimum_staff')) ? $config->get('minimum_staff') : 800,
        '#required' => TRUE,
      );
      $form['db_username'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Database Username'),
        '#description' => $this->t('The username used to connect to the staff database. Field will appear empty, even when a value is already saved in config.'),
        '#maxlength' => 64,
        '#size' => 64,
        '#default_value' => $this->t(''),
      );
      $form['db_password'] = array(
        '#type' => 'password',
        '#title' => $this->t('Database Password'),
        '#description' => $this->t('The password used to connect to the staff database. Note: This is not a secure password storage facility, use an account with the fewest permissions. This field will always show up blank even when a password is saved.'),
        '#size' => 64,
        '#default_value' => $this->t(""),
      );
      $form['db_server_url'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Database URL'),
        '#description' => $this->t('The url used to connect to the staff database. Field will appear empty, even when a value is already saved in config.'),
        '#maxlength' => 64,
        '#size' => 64,
        '#default_value' => $this->t(''),
      );
      $form['database'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Database Name'),
        '#description' => $this->t('The database containing the staff profiles. Field will appear empty, even when a value is already saved in config.'),
        '#maxlength' => 64,
        '#size' => 64,
        '#default_value' => $this->t(''),
      );

      $form['smugmug_pwd'] = array(
        '#type' => 'password',
        '#title' => $this->t('Smugmug Password'),
        '#description' => $this->t('The password to unlock the SmugMug Staff Portrait Album. This is not a secure data storage. This field will appear blank even with a password saved.'),
        '#size' => 64,
        '#default_value' => $this->t(""),
      ); //TODO do we want to use different encryption profile?
      $form['smugmug_api'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Smugmug Api Key'),
        '#description' => $this->t('Smug Mug API key. This will appear blank even when there is a saved api key'),
        '#size' => 64,
        '#default_value' => $this->t(""),
      );

      // List of encryption profiles for selector
      $encrypt_ids = \Drupal::entityQuery('encryption_profile')->execute();
      $encrypt_list = array();

      if (empty($encrypt_ids)) {
        $messenger = \Drupal::messenger();
        $messenger->addMessage('You need to add an Encryption Profile');
      }

      foreach ($encrypt_ids as $eid) {
        $encrypt_prof = \Drupal::entityTypeManager()->getStorage('encryption_profile')->load($eid);
        $encrypt_list[$eid] = $encrypt_prof->label();
      }

      $form['encrypt_profile'] = array(
        '#type' => 'select',
        '#title' => $this->t("Encryption Profile"),
        '#description' => $this->t('Encryption profile used for encryption.'),
        '#options' => $encrypt_list,
        '#default_value' => $config->get('sync_encrypt_profile'),
        '#required' => TRUE,
      );

      return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
      parent::validateForm($form, $form_state);

      $config = $this->config('staff_profile_primary.settings');
      $encrypt_profile = EncryptionProfile::load($form_state->getValue('encrypt_profile'));

      $form_state->setValue('db_password', $this->getHiddenFormFieldValue($config->get('db_password'), $form_state->getValue('db_password'), $encrypt_profile));
      $form_state->setValue('db_username', $this->getHiddenFormFieldValue($config->get('db_username'), $form_state->getValue('db_username'), $encrypt_profile));
      $form_state->setValue('db_server_url', $this->getHiddenFormFieldValue($config->get('db_address'), $form_state->getValue('db_server_url'), $encrypt_profile));
      $form_state->setValue('database', $this->getHiddenFormFieldValue($config->get('db_database'), $form_state->getValue('database'), $encrypt_profile));

      $form_state->setValue('smugmug_pwd', $this->getHiddenFormFieldValue($config->get('smug_mug_password'), $form_state->getValue('smugmug_pwd'), $encrypt_profile));
      $form_state->setValue('smugmug_api', $this->getHiddenFormFieldValue($config->get('smug_mug_api_key'), $form_state->getValue('smugmug_api'), $encrypt_profile));
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
      parent::submitForm($form, $form_state);
      //If checked, run sync
      $this->config('staff_profile_primary.settings')
        ->set('minimum_staff', $form_state->getValue('minimum_staff'))
        ->set('db_username', $form_state->getValue('db_username'))
        ->set('db_password', $form_state->getValue('db_password'))
        ->set('db_address', $form_state->getValue('db_server_url'))
        ->set('db_database', $form_state->getValue('database'))
        ->set('smug_mug_password', $form_state->getValue('smugmug_pwd'))
        ->set('smug_mug_api_key', $form_state->getValue('smugmug_api'))
        ->set('sync_encrypt_profile', $form_state->getValue('encrypt_profile'))
        ->save();

  }

  /*
   * Function to get the hidden value of a form field
   */
  private function getHiddenFormFieldValue($oldvalue, $newvalue, $encrypt_profile) {
    $return_value = $oldvalue;

    if (!empty($newvalue)) {
      $return_value = \Drupal::service('encryption')->encrypt($newvalue, $encrypt_profile);
    }

    return $return_value;
  }
}
