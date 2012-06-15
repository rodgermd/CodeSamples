<?php
class DiaryPeriodSelectorValidator extends PeriodSelectorValidator
{
  const TODAY     = 'today';
  const YESTERDAY = 'yesterday';

  /**
   * clean procedure
   * @param mixed $value
   * @return array|mixed
   */
  public function doClean($value)
  {
    switch ($value) {
      case self::TODAY:
        $date = new DateTime('now');
        break;
      case self::YESTERDAY:
        $date = new DateTime('now');
        $date->modify('-1 day');
        break;
      default:
        preg_match('/\d+-\d+-\d+/', $value, $matches);
        $date = new DateTime(@$matches[0]);
        break;
    }
    return parent::doClean(implode(':', array(self::DAY, $date->format('Y-m-d'))));
  }
}