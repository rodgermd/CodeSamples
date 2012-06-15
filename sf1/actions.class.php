<?php

/**
 * notification actions.
 *
 * @package    timetracker
 * @subpackage notification
 * @author     rodger
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 *
 * @property NotificationsListFormFilter $filters
 */
class notificationActions extends myActions
{
 /**
  * Executes index action
  * Renders Notifications list
  *
  * @param sfWebRequest $request
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->filters = new NotificationsListFormFilter();
    $this->filters->bindRequest($request);

    $url = urldecode($this->generateUrl('notifications', $this->filters->generate_url_params()));
    $this->preprocess_selectors_url($request, $url);

    $this->pager = new sfDoctrinePager('WorktimeActions');
    $this->pager->setQuery($this->filters->buildQuery($this->filters->getValues()));
    $this->pager->setMaxPerPage(sfConfig::get('app_notifications_list_limit'));
    $this->pager->setPage($request->getParameter('page', 1));
    $this->pager->init();

    if ($this->pager->getPage() > $this->pager->getLastPage()) {
      $this->pager->setPage($this->pager->getLastPage());
      $this->pager->init();
    }

    $this->user = $this->getUser()->get();
    $this->order = $this->filters->getSortOrder();
  }

  /**
   * Single notification
   * @param sfWebRequest $request
   */
  public function executeSingle(sfWebRequest $request) {
    $this->notification = $this->getRoute()->getObject();
    $this->forward404Unless($this->notification->belongs_to($this->user));
    $this->notification->read();
    $this->ajax_mode = $request->isXmlHttpRequest();
  }
}
