<?php

class Zizio_Powershare_Helper_Cron extends Mage_Core_Helper_Abstract
{

	private $_helper = null;
	
	public function __construct()
	{
		$this->_helper = new Zizio_Powershare_Helper_Data();
	}
	
	public function CheckCronJobs ()
	{
		try
		{
			$run_hourly = false;
			$hourly_last_run = $this->_helper->GetConfigDate(Zizio_Powershare_Helper_Data::CRON_HOURLY_LAST_RUN_PATH);
			if ($hourly_last_run == null)
				$run_hourly = true;
			else
			{
				$last_hour = $this->_helper->DateTimeToUTC();
				$last_hour->addHour(-1);
				if ($hourly_last_run < $last_hour)
					$run_hourly = true;
			}

			if ($run_hourly)
			{
				$schedule = new Mage_Cron_Model_Schedule();
				$psuedo_schedule = $hourly_last_run ? $hourly_last_run->addHour(1) : new DateTime();
				$schedule->setScheduledAt($psuedo_schedule);
				$this->HourlyCronJobs($schedule);
			}

			$run_daily = false;
			$daily_last_run = $this->_helper->GetConfigDate(Zizio_Powershare_Helper_Data::CRON_DAILY_LAST_RUN_PATH);
			if ($daily_last_run == null)
				$run_daily = true;
			else
			{
				$last_day = $this->_helper->DateTimeToUTC();
				$last_day->addHour(-24);
				if ($daily_last_run < $last_day)
					$run_daily = true;
			}

			if ($run_daily)
			{
				$schedule = new Mage_Cron_Model_Schedule();
				$psuedo_schedule = $daily_last_run ? $daily_last_run->addDay(1) : new DateTime();
				$schedule->setScheduledAt($psuedo_schedule);
				$this->DailyCronJobs($schedule);
			}
		}
		catch (Exception $ex)
		{
			$this->_helper->LogError($ex);
			return false;
		}

	}

	public function HourlyCronJobs($schedule)
	{
		$this->_helper->SaveConfigDate(Zizio_Powershare_Helper_Data::CRON_HOURLY_LAST_RUN_PATH);
		return $this;
	}

	public function DailyCronJobs($schedule)
	{
		$this->ProcessRemoteMessages($schedule); // Handler to fetch and process messages from the remote Zizio server
		$this->_helper->SaveConfigDate(Zizio_Powershare_Helper_Data::CRON_DAILY_LAST_RUN_PATH);
		return $this;
	}

	/**
	 * This method is intended to run as a cron job . It's intended to fetch messages
	 * from the remote Zizio server and process them into Inbox notifications to be
	 * displayed to the admin user.
	 */
	public function ProcessRemoteMessages($schedule)
	{
		try
		{
			// Array of messages fetched from the Zizio server:
			$remote_messages = $this->_helper->GetRemoteMessages();
			// String that contains messages to be logged in the cron_schedule table, when done:
			$schedule_messages = "[ProcessRemoteMessages: ";

			if ($remote_messages != null)
			{
				// Array of new notifications to be inserted into the Magento Inbox:
				$notifications = array();

				foreach ($remote_messages as $remote_message)
				{
					if (!isset($remote_message['url']) ||
						!isset($remote_message['title']))
						continue;
					$notification = array();
					if (isset($remote_message['url']))					
						$notification['url'] = $remote_message['url'];
					else 
					{
						// Internal (admin) url
						$block = new Mage_Core_Block_Text();
						$notification['url'] = $block->getUrl($remote_message['admin_url']);
					}
					
					$notification['title'] 		 = $remote_message['title'];
					$notification['description'] = isset($remote_message['description']) ? $remote_message['description'] : "";
					$notification['severity'] 	 = isset($remote_message['severity']) ? $remote_message['severity'] : 4;
					$notification['is_read'] 	 = 0;
					$notification['date_added']  = date('Y-m-d H:i:s');
					$notifications[] = $notification;
					$schedule_messages .= "_id=" . $remote_message['_id'] . ",";
				}

				// Save new notifications to DB:
				$inbox = Mage::getModel('adminNotification/inbox');
				$inbox->parse($notifications);
				$this->_helper->SaveLastRemoteMessagesDate();
			}

			// Put logging messages into cron schedule object to be returned upwards:
			$schedule_messages = substr($schedule_messages, 0, -1);
			$schedule_messages .= "]";
			$schedule->setMessages($schedule->getMessages() . $schedule_messages);
		}
		catch (Exception $ex)
		{
			$this->_helper->LogError($ex);
			return false;
		}
		return true;

	}

}