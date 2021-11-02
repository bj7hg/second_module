<?php

namespace Drupal\review\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\MessageCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

/**
 * Form for adding reviews.
 */
class ReviewForm extends FormBase {

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    return 'review_form';
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your name:'),
      '#required' => TRUE,
    ];
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Your email:'),
      '#required' => TRUE,
      '#placeholder' => t('Allowed: numbers,"@" and "-"'),
    ];
    $form['number'] = [
      '#type' => 'tel',
      '#title' => $this->t('Your phone-number:'),
      '#required' => TRUE,
      '#placeholder' => $this->t('Format: "380xxxxxxxxx"'),
    ];
    $form['text'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Your review:'),
      '#required' => TRUE,
    ];
    $form['user'] = [
      '#title' => 'User image:',
      '#type' => 'managed_file',
      '#multiple' => FALSE,
      '#description' => t('Allowed extensions: jpeg, jpg, png'),
      '#required' => FALSE,
      '#upload_location' => 'public://images/',
      '#upload_validators'    => [
        'file_validate_is_image'      => [],
        'file_validate_extensions'    => ['png jpg jpeg'],
        'file_validate_size'          => [2097152],
      ],
    ];
    $form['image'] = [
      '#title' => 'Image for review:',
      '#type' => 'managed_file',
      '#multiple' => FALSE,
      '#description' => t('Allowed extensions: jpeg, jpg, png'),
      '#required' => FALSE,
      '#upload_location' => 'public://images/',
      '#upload_validators'    => [
        'file_validate_is_image'      => [],
        'file_validate_extensions'    => ['png jpg jpeg'],
        'file_validate_size'          => [5242880],
      ],
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add review'),
      '#button_type' => 'primary',
      '#ajax' => [
        'callback' => '::setMessage',
      ],
    ];
    return $form;
  }

  /**
   * Validation data's from form.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $email = $form_state->getValue('email');
    if (strlen($form_state->getValue('name')) < 2) {
      $form_state->setErrorByName('name', $this->t('Name is too short.'));
    }
    elseif (strlen($form_state->getValue('name')) > 100) {
      $form_state->setErrorByName('name', $this->t('Name is too long.'));
    }
    if (strlen($form_state->getValue('number')) != 12) {
      $form_state->setErrorByName('name', $this->t('Write phone number in allowed format.'));
    }
    if ((!filter_var($email, FILTER_VALIDATE_EMAIL))
      || (strpbrk($email, '+*/!#$^&*()='))) {
      $form_state->setErrorByName('email', $this->t('Invalid Email'));
    }
  }

  /**
   * {@inheritDoc}
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   * @throws \Exception
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $avatar = $form_state->getValue('user');
    $picture = $form_state->getValue('image');
    if ($picture != NULL) {
      $file = File::load($picture[0]);
      if ($file != NULL) {
        $file->setPermanent();
        $file->save();
      }
    }
    if ($avatar != NULL) {
      $avatar_file = File::load($avatar[0]);
      if ($avatar_file != NULL) {
        $avatar_file->setPermanent();
        $avatar_file->save();
      }
    }
    $data = [
      'name' => $form_state->getValue('name'),
      'email' => $form_state->getValue('email'),
      'number' => $form_state->getValue('number'),
      'text' => $form_state->getValue('text'),
      'user' => $form_state->getValue('user')[0],
      'image' => $form_state->getValue('image')[0],
      'date' => date('d-m-Y H:i:s', strtotime('+3 hour')),
    ];
    \Drupal::database()->insert('reviews')->fields($data)->execute();
  }

  /**
   * Set messages of errors or success.
   */
  public function setMessage(array $form, FormStateInterface $form_state): AjaxResponse {
    $response = new AjaxResponse();
    if ($form_state->hasAnyErrors()) {
      foreach ($form_state->getErrors() as $errors_array) {
        $response->addCommand(new MessageCommand($errors_array, '#for-message', [], FALSE));
      }
    }
    else {
      $response->addCommand(new MessageCommand('You adedd a review!'));
    }
    \Drupal::messenger()->deleteAll();
    return $response;
  }

}
