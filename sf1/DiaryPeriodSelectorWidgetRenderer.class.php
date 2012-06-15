<?php
class DiaryPeriodSelectorWidgetRenderer extends PeriodSelectorWidgetRenderer
{
  protected $widget_template = '
<span class="selector-container single-date-selector">
  %date-input%<div class="current-selection"><span class="text">%current-selection%</span></div>
</span>';

  /**
   * Renders widget
   * @param string $name
   * @param null $value
   * @param array $attributes
   * @param array $errors
   * @return string
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $range = $value['range'];
    return strtr($this->widget_template, array(
      '%date-input%'        => $this->renderDateInput($name, $range),
      '%current-selection%' => $this->getCurrentSelection($name, $range)
    ));
  }

  /**
   * Renders date input
   * @param $name
   * @param DateRange|null $range
   * @return string
   */
  public function renderDateInput($name, DateRange $range = null)
  {
    return $this->renderTag('input', array('type'  => 'text',
                                           'name'  => $name,
                                           'id'    => $this->generateId($name),
                                           'class' => 'datepicker-field',
                                           'value' => $range ? $this->formatDateValue($range->getMinDate()) : null));
  }
}