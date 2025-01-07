<?php

namespace Drupal\pcda_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

class HomePageController extends ControllerBase {

  public function homePageContent() {
    $payment_form_url = Url::fromRoute('pcda_module.payment_form')->toString();

    $button_html = '<a href="' . $payment_form_url . '" class="button" style="text-decoration:none; padding: 10px 20px; background-color: #007bff; color: white; border-radius: 5px;">Go to Payment Form</a>';
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Click here for payment advice bill') . '<br><br>' . $button_html,
    ];
  }
}

