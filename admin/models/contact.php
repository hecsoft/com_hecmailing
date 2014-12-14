<?php
/**
 * @package     HECMailing
 * @subpackage  com_hecmailing
 *
 * @copyright   Copyright (C) 2005 - 2014 Hecsoft. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
jimport('joomla.error.log');
jimport('joomla.log.log');
/**
 * User group model.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_users
 * @since       1.6
 */
class HecMailingModelContact extends JModelAdmin
{
	var $log=false;
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	 * @since   1.6
	*/
	public function getTable($type = 'Contact', $prefix = 'JTable', $config = array())
	{
		$return = JTable::getInstance($type, $prefix, $config);
		return $return;
	}

	
	
	public function getItem ($pk=null)
	{
		$row = JTable::getInstance('contact', 'Table');
		// load the row from the db table
		if($pk!=0)
		{
			if ($pk>0)
				$row->load( $pk );
			else 
			{
				$row->load( -$pk );
				$row->id=0;
			}
		}
		else
		{
			$row = new StdClass;
			$row->ct_id_contact=0;
    		$row->grp_id_groupe=0;
    		$row->ct_nm_contact='';
    		$row->ct_cm_contact='';
    		$row->ct_vl_info='';
    		$row->ct_vl_template='';
    		$row->ct_vl_prefixsujet='';
    		$row->published=1;
			$row->checked_out=0;
			return $row;
		}
		
		$user 	= JFactory::getUser();
		$row->checkout($user->get('id'));
		
		
		return $row;
	}
	
	
	public function getGroups($current_groupe)
	{
		$db = JFactory::getDbo();
		$query = "SELECT grp_id_groupe, grp_nm_groupe FROM  #__hecmailing_groups WHERE grp_id_groupe!=".$current_groupe." AND published=1 ORDER BY grp_nm_groupe"; 
		$db->setQuery($query);
		$grp = $db->loadRowList();
		return $grp;
	}
	
	/**
	 * Method to get the record form.
	 *
	 * @param   array  $data		An optional array of data for the form to interogate.
	 * @param   boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return  JForm	A JForm object on success, false on failure
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_hecmailing.group', 'group', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_hecmailing.edit.group.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		$this->preprocessData('com_hecmailing.group', $data);

		return $data;
	}

	
	
	/**
	 * Override preprocessForm to load the user plugin group instead of content.
	 *
	 * @param   object	A form object.
	 * @param   mixed	The data expected for the form.
	 * @throws	Exception if there is an error in the form event.
	 * @since   1.6
	 */
	protected function preprocessForm(JForm $form, $data, $groups = '')
	{
		parent::preprocessForm($form, $data, 'hecmailing');
	}

	
	protected function AddLog($type,$info)
	{
		$version = new JVersion();
		if ( (real)$version->RELEASE < 3.0 )
		{
			if (!$this->log) $this->log = &JLog::getInstance('com_hecmailing.log.php');
			$log->addEntry(array($type => $text));
		}
		else
		{
			if (!$this->log) JLog::addLogger(array('text_file' => 'com_hecmailing.log.php', 'text_entry_format' => '{DATETIME} {PRIORITY} {MESSAGE}'));
			$this->log=true;
			if ($type=="error") $type='JLog::ERROR';
			else if ($type=="error") $type='JLog::WARNING';
			JLog::add($info, $type, "com_hecmailing"); 
		}
		
	
	}
	
	/**
	 * Method to save the form data.
	 *
	 * @param   array  The form data.
	 * @return  boolean  True on success.
	 * @since   1.6
	 */
	public function save($data)
	{
		// Modif Joomla 1.6/1.7+
		$error=false;
		$this->addLog('comment' ,'======= saveObject Contact =========');

		// Initialize variables
		$db		=JFactory::getDBO();
		$row	=JTable::getInstance('contact', 'Table');
		
		if (!$row->bind( $data )) {	JError::raiseError(500, $row->getError() );	}
			
		// pre-save checks
		if (!$row->check()) { JError::raiseError(500, $row->getError() ); }

		// save the changes
		if (!$row->store()) {	JError::raiseError(500, $row->getError() );	}
			
		$row->checkin();
		
		return true;
		
	}

	/**
	 * Method to delete rows.
	 *
	 * @param   array  An array of item ids.
	 * @return  boolean  Returns true on success, false on failure.
	 * @since   1.6
	 */
	public function delete(&$cid)
	{
		$app = JFactory::getApplication();
		$db		= JFactory::getDBO();
		JArrayHelper::toInteger($cid);

		if (count( $cid )) {
			$cids = implode( ',', $cid );
			$query = 'DELETE FROM #__hecmailing_contact'
				. ' WHERE ct_id_contact IN ( '. $cids .' )';
			$db->setQuery( $query );
			if (!$db->query()) {
				$this->error=$db->getErrorMsg(true);
				return false;
			}
		}
		else
		{
			$error = JText::_("COM_HECMAILING_CONTACT_ERROR_NOGROUP");
			return false;
		}
  		return true;
	}
	
	
}
