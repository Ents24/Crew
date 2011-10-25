<?php

/**
 * repository actions.
 *
 * @package    crew
 * @subpackage repository
 * @author     Your name here
 */
class fileToggleUnvalidateAction extends sfAction
{
  /**
   * @param sfWebRequest $request
   * @return void
   */
  public function execute($request)
  {
    $file = FilePeer::retrieveByPK($request->getParameter('file'));
    $this->forward404Unless($file, 'File Not Found');

    if ($file->getStatusId() === StatusPeer::KO)
    {
      $file->setStatusId(StatusPeer::A_TRAITER);
      $render = array('toggleState' => 'disabled');
    }
    else
    {
      $file->setStatusId(StatusPeer::KO);
      $render = array('toggleState' => 'enabled');
    }

    $file
      ->save()
    ;
    
    $this->getResponse()->setContentType('application/json');
    return $this->renderText(json_encode($render));
  }
}
