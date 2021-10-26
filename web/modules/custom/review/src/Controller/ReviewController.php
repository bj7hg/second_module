<?php
/**
 * @return
 * Contains \Drupal\rgb\Controller\CatsController.
 */

namespace Drupal\review\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses for the rgb module.
 */
class ReviewController extends ControllerBase
{

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
    public function content()
    {
        $form = \Drupal::formBuilder()->getForm('\Drupal\review\Form\reviewForm');
        return [
        '#theme' => 'reviews-page',
        '#form' => $form,
        ];
    }
}
