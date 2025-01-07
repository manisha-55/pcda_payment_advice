<?php

namespace Drupal\pcda_module\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

class PcdaPaymentForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'pcda_payment_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['pan_number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('PAN Number'),
      '#required' => TRUE,
    ];

    $form['claimed_amount'] = [
      '#type' => 'number',
      '#title' => $this->t('Claimed Amount'),
      '#required' => TRUE,
    ];

    $form['captcha'] = [
      '#type' => 'captcha',
      '#captcha_type' => 'default',
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $pan_number = $form_state->getValue('pan_number');
    $claimed_amount = $form_state->getValue('claimed_amount');

    if (!empty($pan_number)) {
      // PAN number should be in the format of 5 letters, 4 digits, and 1 letter
      if (!preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', $pan_number)) {
        $form_state->setErrorByName('pan_number', $this->t('Please Enter Valid PAN number.'));
      }
    }

    if ($claimed_amount <= 0) {
      $form_state->setErrorByName('claimed_amount', $this->t('Enter valid amount.'));
    }

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $pan_number = $form_state->getValue('pan_number');
    $claimed_amount = $form_state->getValue('claimed_amount');

    $connection = Database::getConnection();
    $query = $connection->select('pcda_payments', 'p')
      ->fields('p', ['bill_id', 'passed_amount', 'rejection_reason'])
      ->condition('p.pan_number', $pan_number)
      ->condition('p.claimed_amount', $claimed_amount)
      ->execute()
      ->fetchAssoc();

    if ($query) {
      $bill_id = $query['bill_id'];
      $file_path = 'sites/default/files/payment_advices/' . $bill_id . '.pdf';

      if (file_exists($file_path)) {
        \Drupal::messenger()->addMessage($this->t('Payment advice file downloaded successfully.'));
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        //header('Content-Disposition: inline; filename="' . basename($file_path) . '"');
        readfile($file_path);
        exit;
      } else {
        \Drupal::messenger()->addError($this->t('Payment advice file not found.'));

      }
    } else {
      \Drupal::messenger()->addError($this->t('No matching records found. Please check your details.'));

    }
  }

}
