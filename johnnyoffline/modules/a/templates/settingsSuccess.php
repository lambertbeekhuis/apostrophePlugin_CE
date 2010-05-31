<div class="a-chad"></div>

<?php use_helper('Url', 'jQuery') ?>

	<?php echo jq_form_remote_tag(
	  array(
	    'update' => "a-page-settings",
	    "url" => "a/settings",
			'complete' => '$(".a-page-overlay").hide();', 
	    "script" => true),
	  array(
	    "name" => "a-page-settings-form", 
	    "id" => "a-page-settings-form")) ?>

	<h3 id="a-page-settings-heading">Page Settings</h3>

	<?php // We need this to distinguish the original AJAX POST from an ?>
	<?php // actual form submit; we can't use a name attribute on the ?>
	<?php // submit tag because that doesn't work in jq_form_remote_tag ?>
  <input type="hidden" name="submit" value="1" />

	<?php echo $form['id'] ?>

		<div id="a-page-settings-left">
			<?php if (isset($form['slug'])): ?>
			  <div class="a-form-row slug">
			    <label>Page Slug</label>
			    <?php echo $form['slug'] ?>
			    <?php echo $form['slug']->renderError() ?>
			  </div>
			<?php endif ?>
			<div class="a-form-row engine">
			  <label>Page Engine</label>
			  <?php echo $form['engine']->render(array('onClick' => 'aUpdateEngineAndTemplate()')) ?>
			  <?php echo $form['engine']->renderError() ?>
			</div>
			<div class="a-form-row template" id="a-page-template">
			  <label>Page Template</label>
			  <?php echo $form['template'] ?>
			  <?php echo $form['template']->renderError() ?>
			</div>
			<div class="a-form-row status">
			  <label>Page Status</label>
			  	<div class="a-page-settings-status">
				    <?php echo $form['archived'] ?>
            <?php if(isset($form['cascade_archived'])): ?>
              <?php echo $form['cascade_archived'] ?> Cascade <em>status</em> changes to children
            <?php endif ?> 
					</div>
			</div>			
			<div class="a-form-row privacy">
			  <label>Page Privacy</label>
			  	<div class="a-page-settings-status">
						<?php echo $form['view_is_secure'] ?>
						<?php if(isset($form['cascade_view_is_secure'])): ?>
                <?php echo $form['cascade_view_is_secure'] ?> Cascade <em>privacy</em> changes to children
            <?php endif ?> 
					</div>
			</div>
		</div>
	
  <div id="a-page-settings-right">
	
		<h4>Page Permissions</h4>
	
		<div class="a-page-permissions">
		  <?php include_partial('a/privileges', 
		    array('form' => $form, 'widget' => 'editors',
		      'label' => 'Editors', 'inherited' => $inherited['edit'],
		      'admin' => $admin['edit'])) ?>
		  <?php include_partial('a/privileges', 
		    array('form' => $form, 'widget' => 'managers',
		      'label' => 'Managers', 'inherited' => $inherited['manage'],
		      'admin' => $admin['manage'])) ?>
			</div>
  </div>
	
	<ul id="a-page-settings-footer" class="a-controls a-page-settings-form-controls">
		<li>
		  <input type="submit" name="submit" value="Save Changes" class="a-submit" id="a-page-settings-submit" />
		</li>
		<li>
			<?php echo jq_link_to_function('Cancel', '
				$("#a-page-settings").slideUp(); 
				$("#a-page-settings-button-open").show(); 
				$("#a-page-settings-button-close").addClass("loading").hide()
				$(".a-page-overlay").hide();', 
				array(
					'class' => 'a-btn icon a-cancel', 
					'title' => 'cancel', 
				)) ?>
		</li>
		<?php if ($page->userHasPrivilege('manage')): ?>
		<li>
			<?php $childMessage = ''; ?>
			<?php if($page->hasChildren()): ?>
			<?php $childMessage = "This page has children that will also be deleted. "; ?>
			<?php endif; ?>
      <?php echo link_to("Delete This Page", "a/delete?id=" . $page->getId(), array("confirm" => $childMessage."Are you sure? This operation can not be undone. Consider archiving the page instead.", 'class' => 'a-btn icon a-delete')) ?>
    </li>
		<?php endif ?>
	</ul>

</form>
<script type="text/javascript" charset="utf-8">
	function aUpdateEngineAndTemplate()
	{
	  if (!$('#a_settings_settings_engine').val().length)
	  {
	    $('#a_settings_settings_template').show();
	  }
	  else
	  {
	    $('#a_settings_settings_template').hide();
	  }
	}
	aUpdateEngineAndTemplate();
	<?php // you can do this: { remove: 'custom html for remove button' } ?>
	aMultipleSelect('#a-page-settings', { });

	<?php // you can do this: { linkTemplate: "<a href='#'>_LABEL_</a>",  ?>
	<?php //                    spanTemplate: "<span>_LINKS_</span>",     ?>
	<?php //                    betweenLinks: " " }                       ?>
	aRadioSelect('.a-radio-select', { });
	$('#a-page-settings').show();
</script>