<?php
/**
 * @file
 * Contains \Drupal\rgb\Form\catForm.
 *
 */

namespace Drupal\review\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\MessageCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\HtmlCommand;

class ReviewForm extends FormBase
{

    public function getFormId()
    {
        return 'review_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {

        $form['name'] = [
        '#type' => 'textfield',
        '#placeholder' => $this->t(''),
        '#title' => $this->t('Your name:'),
        '#required' => true,
        ];
        $form['email'] = [
          '#type' => 'email',
          '#title' => $this->t('Your email:'),
          '#required' => true,
          '#placeholder' => t('Allowed: numbers,"@" and "-"'),
        ];
        $form['number'] = [
          '#type' => 'tel',
          '#title' => $this->t('Your phone-number:'),
          '#required' => true,
          '#placeholder' => $this->t('Format: "380xxxxxxxxx"'),
        ];
        $form['text'] = [
          '#type' => 'textarea',
          '#title' => $this->t('Your review:'),
          '#required' => true,
        ];
        $form['user'] = [
          '#title' => 'User image:',
          '#type' => 'managed_file',
          '#multiple' => false,
          '#description' => t('Allowed extensions: jpeg, jpg, png'),
          '#required' => false,
          '#upload_validators'    => [
            'file_validate_is_image'      => [],
            'file_validate_extensions'    => ['png jpg jpeg'],
            'file_validate_size'          => [2097152],
            '#upload_location' => 'public://images/',
            '#default_value' => '../../img/img.png',
          ],
        ];
        $form['image'] = [
          '#title' => 'Image for review:',
          '#type' => 'managed_file',
          '#multiple' => false,
          '#description' => t('Allowed extensions: jpeg, jpg, png'),
          '#required' => false,
          '#upload_validators'    => [
            'file_validate_is_image'      => [],
            'file_validate_extensions'    => ['png jpg jpeg'],
            'file_validate_size'          => [5242880],
            '#upload_location' => 'public://images/',
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
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        $email=$form_state->getValue('email');
        if (strlen($form_state->getValue('name')) < 2) {
            $form_state->setErrorByName('name', $this->t('Name is too short.'));
        } elseif (strlen($form_state->getValue('name')) > 100) {
            $form_state->setErrorByName('name', $this->t('Name is too long.'));
        }
        if (strlen($form_state->getValue('number')) != 12) {
            $form_state->setErrorByName('name', $this->t('Write phone number in allowed format.'));
        }
        if ((!filter_var($email, FILTER_VALIDATE_EMAIL))
        || ( strpbrk($email, '+*/!#$^&*()='))) {
            $form_state->setErrorByName('email', $this->t('Invalid Email'));
        }
    }
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
    }
    public function setMessage(array $form, FormStateInterface $form_state): AjaxResponse
    {
        $response = new AjaxResponse();
        if ($form_state->hasAnyErrors()) {
            foreach ($form_state->getErrors() as $errors_array) {
                $response->addCommand(new MessageCommand($errors_array, null, [], false));
            }
        } else {
            $response->addCommand(new MessageCommand('You adedd a review!'));
        }
        \Drupal::messenger()->deleteAll();
        return $response;
    }
}
