<?php

namespace Drupal\review\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\file\Entity\File;

/**
 * Provides route responses for the review module.
 */
class ReviewController extends ControllerBase {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function content() {
    $form = \Drupal::formBuilder()->getForm('\Drupal\review\Form\reviewForm');
    return [
      '#theme' => 'reviews-page',
      '#form' => $form,
      '#list' => $this->getReview(),
    ];
  }

  /**
   * Select reviews table from database.
   */
  public function getReview() {
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    $admin = "administrator";
    $query = \Drupal::database();
    $result = $query->select('reviews', 'r')
      ->fields('r', [
        'id',
        'user',
        'name',
        'date',
        'text',
        'image',
        'number',
        'email',
      ])
      ->orderBy('date', 'DESC')
      ->execute()
      ->fetchAll();
    $review = [];
    foreach ($result as $value) {
      if ($value->user != NULL) {
        $value->user = [
          '#theme' => 'image',
          '#uri' => File::load($value->user)->getFileUri(),
          '#attributes' => [
            'class' => 'avatar',
            'alt' => 'avatar',
            'width' => 100,
            'height' => 100,
          ],
        ];
      }
      else {
        $value->user = [
          '#theme' => 'image',
          '#uri' => '/modules/custom/review/img/img.png',
          '#attributes' => [
            'class' => 'avatar',
            'alt' => 'avatar',
            'width' => 100,
            'height' => 100,
          ],
        ];
      }
      if ($value->image != NULL) {
        $value->image = [
          '#theme' => 'image',
          '#uri' => File::load($value->image)->getFileUri(),
          '#attributes' => [
            'class' => 'review-image',
            'alt' => 'Image',
            'width' => 150,
            'height' => 150,
          ],
        ];
      }
      if (in_array($admin, $roles)) {
        $url = Url::fromRoute('delete_review', ['id' => $value->id]);
        $url_edit = Url::fromRoute('edit_review', ['id' => $value->id]);
        $value->delete = [
          '#title' => 'Delete',
          '#type' => 'link',
          '#url' => $url,
          '#attributes' => [
            'class' => ['use-ajax'],
            'data-dialog-type' => 'modal',
          ],
          '#attached' => [
            'library' => ['core/drupal.dialog.ajax'],
          ],
        ];
        $value->edit = [
          '#title' => 'Edit',
          '#type' => 'link',
          '#url' => $url_edit,
          '#attributes' => [
            'class' => ['use-ajax'],
            'data-dialog-type' => 'modal',
          ],
          '#attached' => [
            'library' => ['core/drupal.dialog.ajax'],
          ],
        ];
      }
      else {
        $value->edit = NULL;
        $value->delete = NULL;
      }
      $review[$value->id] = [
        '#theme' => 'list',
        '#id' => $value->id,
        '#name' => $value->name,
        '#email' => $value->email,
        '#number' => $value->number,
        '#user' => $value->user,
        '#image' => $value->image,
        '#text' => $value->text,
        '#date' => $value->date,
        '#edit' => $value->edit,
        '#delete' => $value->delete,
      ];
    }
    return $review;
  }

}
