<?php

class Zizio_Powershare_Block_Adminhtml_Incentives_Edit_Conf_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		try
		{
			$form = new Varien_Data_Form();
			$this->setForm($form);
			$incentive = Mage::registry('powershare_incentive');
			$defaults = array();
			
			$fieldset = $form->addFieldset('powershare_form', 
						array('legend'=>Mage::helper('powershare')->__('Incentive Configuration')));
						
			$note = "Select incentive title for admin and reporting reference (e.g. <b>Share 15% Off</b>)";
			$fieldset->addField('name', 'text', array(
				'name'		 => 'name',
				'label'		 => Mage::helper('powershare')->__('Name'),
				'title'		 => Mage::helper('powershare')->__('Name'),
				'required'	 => true,
				'after_element_html' => Mage::helper('powershare')->GetFieldNote($note)
			));
			
			/* not yet implemented */
			
			$note = "Select shoppers' task";
			$alert = "Select shoppers' task that will entitle them to receive your special offer

- Post on Facebook wall (Active)
- 'X' friends connect / register (Coming Soon)
- 'X' friends purchase (Coming Soon)

Suggest us other tasks via support@zizio.com";
			$fieldset->addField('shopper_task', 'select', array(
				'name'		 => 'shopper_task',
				'label'		 => Mage::helper('powershare')->__('Shoppers\' task'),
				'values'	 => array(
					array('value' => "fb_post_to_wall", 'label' => "Post to Facebook wall"),
					array('value' => "x_friends_connect", 'label' => "'X' friends connect / register (Coming Soon)"),
					array('value' => "x_friends_purchase", 'label' => "'X' friends purchase (Coming Soon)")
				),
				'after_element_html' => Mage::helper('powershare')->GetFieldNote($note, $alert) . '<script type="text/javascript">
					$$("#shopper_task option").each(function(element) {
						if (element.value != "fb_post_to_wall")
							element.setAttribute("disabled", true);
					});	
				</script>'
			));
			
			/* sender experience */
			
			$note = "A coupon can be configured in <b>Promotions -&gt; Shopping Cart Price Rules</b>";
			$fieldset->addField('coupon_code', 'text', array(
				'name'		 => 'coupon_code',
				'label'		 => Mage::helper('powershare')->__('Coupon code'),
				'title'		 => Mage::helper('powershare')->__('Coupon code'),
				'required'	 => true,
				'after_element_html' => Mage::helper('powershare')->GetFieldNote($note)
			));
			
			$note = "Short Incentive description (e.g. <b>15% Off!</b>). If a URL is used (starting with 'http://'
or 'https://') the image at this location will be used. This value overrides any default caption defined in <b>System -&gt; Configuration -&gt; Zizio Power Share</b>";
			$alert = "This field enables you to configure the blue Share Button caption 
Use your special offer to get your shoppers' attention! 
Describe your reward in a short, attractive, one or two words. examples:

- 15% Off
- Get $25
- Free Shipping";
			$fieldset->addField('btn_caption', 'text', array(
				'name'		 => 'btn_caption',
				'label'		 => Mage::helper('powershare')->__('Button caption / URL'),
				'title'		 => Mage::helper('powershare')->__('Button caption / URL'),
				'required'	 => true,
				'onchange'   => "Zizio_Powershare_OnBtnCaptionChanged();",
				'after_element_html' => Mage::helper('powershare')->GetFieldNote($note, $alert)
			));
			
			$note = "Dimensions of the incentive button";
			$alert = "If the image is bigger than those dimensions the top center slice ".
"will be used as basic image and the bottom center slice will be used in hover state.";
			$fieldset->addField('btn_dim', 'hidden', array(
				'name'		 => 'btn_dim',
				'after_element_html' => '</td></tr><tr class="z-btn-dim">
					<td class="label">
						<label for="btn_dim_w">Button dimensions <span class="required">*</span></label>
					</td>
					<td class="value">
						<label for="btn_dim_w">Width</label> <input type="text" name="btn_dim_w" id="btn_dim_w" class="input-text validate-zizio-dim" style="width: 25px;" />
						<label for="btn_dim_h">Height</label> <input type="text" name="btn_dim_h" id="btn_dim_h" class="input-text validate-zizio-dim" style="width: 25px;" />
						<script type="text/javascript">
							(function() {
								var dim = $F("btn_dim");
								dim = dim ? dim.split(",") : [];
								if ((dim.length != 2) || !dim[0] || !dim[1] || (dim[0] % 1 !== 0) || (dim[1] % 1 !== 0))
									dim = [60, 25];
								$("btn_dim_w").value = dim[0];
								$("btn_dim_h").value = dim[1];
								
								var is_url = function() {
									return /^https?:\/\//i.test($F("btn_caption"));
								}
								
								window.Zizio_Powershare_OnBtnCaptionChanged = function() {
									$$(".z-btn-dim")[0][is_url() ? "show" : "hide"]();
								};
								window.Zizio_Powershare_OnBtnCaptionChanged();
							
								Validation.add("validate-zizio-dim", "Please use an integer", function(v) {
									if (!$("btn_dim") || !is_url())
										return true;
									v = Number(v);
									if (isNaN(v) || !v)
										return false;
									return (v % 1) == 0;
								});
							})();
						</script>
						' . Mage::helper('powershare')->GetFieldNote($note, $alert)
			));
			
			$note = "Share dialog <i>title</i> call-for-action (e.g. <b>Share 15% Off with your friends</b>)";
			$alert = "The dialog title describes the task and the reward to your shoppers.
The dialog is prompted whenever the Blue Share Button is clicked in your store.
The title should be short and clear, it's recommended to include less than 8 words, examples: 

- Share 15% Off with your friends
- 10% Off for you and your friends
- Send Coupon to your friends and get yours";
			$fieldset->addField('dialog_title', 'text', array(
				'name'		 => 'dialog_title',
				'label'		 => Mage::helper('powershare')->__('Dialog title'),
				'title'		 => Mage::helper('powershare')->__('Dialog title'),
				'required'	 => true,
				'after_element_html' => Mage::helper('powershare')->GetFieldNote($note, $alert)
			));
			
			$note = "Describe the sharing process. (e.g. <b>Share this product with your friends</b>)";
			$fieldset->addField('flow_share', 'textarea', array(
				'name'		 => 'flow_share',
				'label'		 => Mage::helper('powershare')->__('Share step caption'),
				'title'		 => Mage::helper('powershare')->__('Share step caption'),
				'style'		 => "height: 30px;",
				'required'	 => true,
				'after_element_html' => Mage::helper('powershare')->GetFieldNote($note)
			));
			
			$note = "Describe the specific incentive the shopper gets (e.g. <b>You get 15% off</b>)";
			$fieldset->addField('flow_incentive', 'textarea', array(
				'name'		 => 'flow_incentive',
				'label'		 => Mage::helper('powershare')->__('Incentive step caption'),
				'title'		 => Mage::helper('powershare')->__('Incentive step caption'),
				'style'		 => "height: 30px;",
				'required'	 => true,
				'after_element_html' => Mage::helper('powershare')->GetFieldNote($note)
			));
								
			/* $note = "Describe what the shoppers' friends can get. (e.g. <b>Your friends can share too and get theirs</b>)";
			$fieldset->addField('flow_friends', 'textarea', array(
				'name'		 => 'flow_friends',
				'label'		 => Mage::helper('powershare')->__('Friends step caption'),
				'title'		 => Mage::helper('powershare')->__('Friends step caption'),
				'style'		 => "height: 30px;",
				'after_element_html' => Mage::helper('powershare')->GetFieldNote($note)
			)); */
			
			$note = "Share dialog <i>success</i> call-for-action description";
			$alert = "Success message appears, in the dialog, once the task was carried out successfully by the shopper.
This message should compliment shoppers on the completed task and drive them to redeem the reward and make the purchase example: 

- Success, your coupon is accepted! Proceed to checkout and get your discount. 
- Thanks for sharing, your discount will be waiting for you at check out.";
			$fieldset->addField('thankyou_message', 'textarea', array(
				'name'		 => 'thankyou_message',
				'label'		 => Mage::helper('powershare')->__('Success message'),
				'title'		 => Mage::helper('powershare')->__('Success message'),
				'style'		 => "height: 50px;",
				'required'	 => true,
				'after_element_html' => Mage::helper('powershare')->GetFieldNote($note, $alert)
			));
			
			/* receiver experience */
			
			$note = "Incentive description as appears in shared content";
			$fieldset->addField('share_teaser', 'text', array(
				'name'		 => 'share_teaser',
				'label'		 => Mage::helper('powershare')->__('Shared item teasing line'),
				'title'		 => Mage::helper('powershare')->__('Shared item teasing line'),
				'required'	 => true,
				'after_element_html' => Mage::helper('powershare')->GetFieldNote($note)
			));
			
			$note = "Product image watermark for shared content";
			$fieldset->addField('share_watermark', 'select', array(
				'name'		 => 'share_watermark',
				'label'		 => Mage::helper('powershare')->__('Shared item watermark'),
				'values'	 => Mage::helper('powershare')->GetWatermarksOptionsArray(),
				'after_element_html' => Mage::helper('powershare')->GetFieldNote($note)
			));
			$defaults['share_watermark'] = Mage::helper('powershare')->GetDefaultWatermark();
			
			/* dates */
			
			$fieldset->addField('start_date', 'date', array(
				'name'				 => 'start_date',
				'title'				 => Mage::helper('powershare')->__('Start Date'),
				'label'				 => Mage::helper('powershare')->__('Start Date'),
				'image'				 => $this->getSkinUrl('images/grid-cal.gif'),
				'time'				 => false,
				'format'			 => Varien_Date::DATE_INTERNAL_FORMAT,
				'input_format'		 => Varien_Date::DATE_INTERNAL_FORMAT,
				'readonly'			 => true,
				'after_element_html' => '
					<p class="note"><span><input type="checkbox" id="start_date_limit" /> <label>Uncheck to remove limit</label></span></p>
					<script type=""text/javascript">(function() {
						$("start_date").onclick = $("start_date_trig").onclick;
						var onchange = function() {
							var checked = Boolean($F("start_date"));
							$("start_date_limit").checked = checked;
							$("start_date_limit").up(".note")[checked ? "show" : "hide"]();
						};
						onchange();
						$("start_date").observe("change", onchange);
						$("start_date_limit").observe("click", function() {
							if(!this.checked) {
								$("start_date").value = "";
								$("start_date_limit").up(".note").hide();
							}
						});
					})();</script>'
			));
			
			$fieldset->addField('end_date', 'date', array(
				'name'				 => 'end_date',
				'title'				 => Mage::helper('powershare')->__('End Date'),
				'label'				 => Mage::helper('powershare')->__('End Date'),
				'image'				 => $this->getSkinUrl('images/grid-cal.gif'),
				'time'				 => false,
				'format'			 => Varien_Date::DATE_INTERNAL_FORMAT,
				'input_format'		 => Varien_Date::DATE_INTERNAL_FORMAT,
				'readonly'			 => true,
				'after_element_html' => '
					<p class="note"><span><input type="checkbox" id="end_date_limit" /> <label>Uncheck to remove limit</label></span></p>
					<script type=""text/javascript">(function() {
						$("end_date").onclick = $("end_date_trig").onclick;
						var onchange = function() {
							var checked = Boolean($F("end_date"));
							$("end_date_limit").checked = checked;
							$("end_date_limit").up(".note")[checked ? "show" : "hide"]();
						};
						onchange();
						$("end_date").observe("change", onchange);
						$("end_date_limit").observe("click", function() {
							if(!this.checked) {
								$("end_date").value = "";
								$("end_date_limit").up(".note").hide();
							}
						});
					})();</script>'
				)
			);
			
			/* status selection */
						
	        $fieldset->addField('status', 'select', array(
				'label'  => Mage::helper('powershare')->__('Status'),
				'name'   => 'status',
				'values' => array(
			        array(
			                  'value' => 1,
			                  'label' => Mage::helper('powershare')->__('Enabled')
			        ),
			        array(
			                  'value' => 2,
			                  'label' => Mage::helper('powershare')->__('Disabled')
			        )
			    )
	        ));
				
			// set form values
			$form->setValues($incentive->getData());
			if ($incentive->isObjectNew())
				$form->addValues($defaults);
		}
		catch (Exception $ex)
		{
			Zizio_Powershare_Helper_Data::LogError($ex);
		}
		return parent::_prepareForm();
	}
}