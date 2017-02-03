<?php

namespace Drupal\WeatherStation\Forms;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;


/**
 * Provides the form to set mambo api key.
 */
class WeatherStationSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'weatherstation_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'weatherstation.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['api'] = array(
      '#type' => 'fieldset',
      '#title' => t('Global configuration'),
    );
    $config = $this->config('weatherstation.settings');


    $form['api']['openweather_api_key'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Api Key'),
      '#default_value' => $config->get('openweather_api_key'),
    );

    $form['api']['lat'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('lat'),
      '#default_value' => $config->get('lat'),
    );

    $form['api']['lon'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('lon'),
      '#default_value' => $config->get('lon'),
    );


    $form['api']['weatherstation_id_container'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Selector to replace'),
      '#default_value' => $config->get('weatherstation_id_container'),
    );


    $weather_icons = \Drupal::service('weatherstation_type')->getWeatherTypeIcon();
    foreach ($weather_icons as $icon_code => $weather_icon) {


      $form[$icon_code . '_group'] = array(
        '#type' => 'details',
        '#title' => t($weather_icon),
      );

      $form[$icon_code . '_group'][$icon_code . '_slogan'] = array(
        '#type' => 'textfield',
        '#group' => $icon_code . '_group',
        '#title' => t('Slogan'),
        '#default_value' => $config->get($icon_code . '_slogan'),
      );
      $form[$icon_code . '_group'][$icon_code] = array(
        '#type' => 'managed_file',
        '#group' => $icon_code . '_group',
        '#title' => t('Image'),
        '#upload_location' => 'public://',
        '#default_value' => $config->get($icon_code),
        '#description' => t('Image for') . $weather_icon,
        '#upload_validators' => array(
          'file_validate_extensions' => array('gif png jpg jpeg'),
        ),
        '#states' => array(
          'visible' => array(
            ':input[name="image_type"]' => array('value' => t('Upload New Image(s)')),
          ),
        ),
      );
    }


    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // TODO Create validation to config form.
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {


    $config = \Drupal::service('config.factory')
      ->getEditable('weatherstation.settings');
    $config->set('openweather_api_key', $form_state->getValue('openweather_api_key'))->save();
    $config->set('weatherstation_id_container', $form_state->getValue('weatherstation_id_container'))->save();
    $config->set('lon', $form_state->getValue('lon'))->save();
    $config->set('lat', $form_state->getValue('lat'))->save();
    $weather_icons = \Drupal::service('weatherstation_type')->getWeatherTypeIcon();
    foreach ($weather_icons as $icon_code => $weather_icon) {
      $config->set($icon_code, $form_state->getValue($icon_code))->save();
      $config->set($icon_code . "_slogan", $form_state->getValue($icon_code . '_slogan'))->save();
    }
    parent::submitForm($form, $form_state);
  }
}
