<?php

class aMediaCMSSlotsTools
{
  // You too can do this in a plugin dependent on a, see the provided stylesheet 
  // for how to correctly specify an icon to go with your button. See the 
  // apostrophePluginConfiguration class for the registration of the event listener.
  static public function getGlobalButtons()
  {
    // Only if we have suitable credentials
    $user = sfContext::getInstance()->getUser();
    if ($user->hasCredential('media_admin') || $user->hasCredential('media_upload'))
    {
      aTools::addGlobalButtons(array(
        new aGlobalButton('Media', 'aMedia/index', 'a-media')));
    }
  }
}
