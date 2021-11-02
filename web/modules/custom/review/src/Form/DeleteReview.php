<?php

namespace Drupal\review\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Url;

/**
 * Form for possibility to delete review.
 *
 * @package Drupal\mymodule\Form
 */
class DeleteReview extends ConfirmFormBase {
  public $id;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'delete_review';
  }

  /**
   * {@inheritDoc}
   */
  public function getQuestion() {
    return t('Are you sure?');
  }

  /**
   * {@inheritDoc}
   */
  public function getCancelUrl() {
    return new Url('reviews');
  }

  /**
   * {@inheritDoc}
   */
  public function getDescription() {
    return t('Do you want to delete review?');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return t('Delete');
  }

  /**
   * {@inheritDoc}
   */
  public function getCancelText() {
    return t('Cancel');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {

    $this->id = $id;
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $query = \Drupal::database();
    $query->delete('reviews')
      ->condition('id', $this->id)
      ->execute();
    \Drupal::messenger()->addStatus('Succesfully deleted.');
    $form_state->setRedirect('reviews');
  }

}
