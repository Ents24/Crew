<?php

class HipchatNotifier extends SimpleNotifier
{

  /**
   * @param int $statusId
   *
   * @return string
   */
  protected function getLabelStatus($statusId)
  {
    return BranchPeer::getLabelStatus($statusId);
  }

  /**
   * @param $message
   *
   * @return $this
   */
  protected function send($message)
  {
    $configCurrentProject = $this->getCurrentProjectConfig();

    $token = $configCurrentProject['token'];
    $room  = $configCurrentProject['room'];
    $user  = $configCurrentProject['user'];

    list(,$eventName) = explode('.', $this->name);
    $eventConfig = $this->getEventConfig($eventName);

    $notify = isset($eventConfig['notify']) && $eventConfig['notify'] == '1';

    $branch = $this->arguments['object'];
    $newBranchStatus = $this->getLabelStatus($branch->getStatus());

    $color = Hipchat::COLOR_YELLOW;
    if ($newBranchStatus == "ok") {
		$color = Hipchat::COLOR_GREEN;
    } else if($newBranchStatus == "ko") {
		$color = Hipchat::COLOR_RED;
    } else if (isset($eventConfig['color'])) {
        $color = $eventConfig['color'];
    }

    $hipChat = new HipChat($token);
    $hipChat->message_room($room, $user, $message, $notify, $color, Hipchat::FORMAT_TEXT);

    return $this;
  }

}
