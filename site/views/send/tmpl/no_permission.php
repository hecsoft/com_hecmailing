<?php 
/**
* @version 3.2.0
* @package hecMailing for Joomla
* @module views.send.tmpl.no_permission.php
* @subpackage : View Send (Sending mail form)
* @author : Herv� CYR
* @copyright Copyright (C) 2008-2015 Hecsoft All rights reserved.
* @license GNU/GPL
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
* 
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*
*******************************************************************************/
 
defined ('_JEXEC') or die ('restricted access'); 
jimport('joomla.html.html');
$app = JFactory::getApplication();
$app->addCustomHeadTag ('<link rel="stylesheet" href="components/com_hecmailing/css/send.css" type="text/css" media="screen" />');

?>
<div class="warning"><?php echo JText::_('NO PERMISSION'); ?></div>


