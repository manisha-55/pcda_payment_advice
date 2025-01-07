<?php

namespace Drupal\pcda_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PcdaPaymentController extends ControllerBase {

  protected $formBuilder;

  public function __construct(FormBuilderInterface $form_builder) {
    $this->formBuilder = $form_builder;
  }

  public static function create(ContainerInterface $container) {
    return new static($container->get('form_builder'));
  }

  public function paymentForm() {
    return $this->formBuilder->getForm('Drupal\pcda_module\Form\PcdaPaymentForm');
  }
}
