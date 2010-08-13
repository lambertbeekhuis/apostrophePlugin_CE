<?php use_helper('I18N') ?>

<?php $buttons = aTools::getGlobalButtons() ?>
<?php $page = aTools::getCurrentPage() ?>
<?php $pageEdit = ($page && $page->userHasPrivilege('edit')) || empty($page) ?>
<?php $cmsAdmin = $sf_user->hasCredential('cms_admin') ?>

<div class="a-ui a-global-toolbar">

	<ul class="a-ui a-controls">

	  <?php if ($cmsAdmin || count($buttons) || $pageEdit): ?>

			<li><?php echo link_to(__('Apostrophe Now', null, 'apostrophe'),'@homepage', array('class' => 'the-apostrophe')) ?></li>

			<?php foreach ($buttons as $button): ?>
				<?php if ($button->getTargetEnginePage()): ?><?php aRouteTools::pushTargetEnginePage($button->getTargetEnginePage()) ?><?php endif ?>
				<li><?php echo link_to(__($button->getLabel(), null, 'apostrophe'), $button->getLink(), array('class' => 'a-btn icon ' . $button->getCssClass())) ?></li>
			<?php endforeach ?>

			<?php if ($page && (!$page->admin) && $cmsAdmin && $pageEdit): ?>			
				<li>
					<a href="#" onclick="return false;" class="a-btn icon a-page-settings" id="a-page-settings-button">Page Settings</a>			
					<div id="a-page-settings" class="a-page-settings-menu dropshadow"></div>
				</li>				

				<?php // Remove the Add Page Button if we have reached our max depth, max peers, or if it is an engine page ?>
				<?php $maxPageLevels = (sfConfig::get('app_a_max_page_levels'))? sfConfig::get('app_a_max_page_levels') : 0; ?><?php // Your Site Tree can only get so deep ?>
				<?php $maxChildPages = (sfConfig::get('app_a_max_children_per_page'))? sfConfig::get('app_a_max_children_per_page') : 0; ?><?php // Your Site Tree can only get so wide ?>
				<?php if (!(($maxPageLevels && ($page->getLevel() == $maxPageLevels)) || ($maxChildPages && (count($page->getChildren()) == $maxChildPages)) || strlen($page->getEngine()))): ?>
					<li><?php include_component('a', 'createPage', array('page' => $page, 'edit' => $page->userHasPrivilege('edit'))); ?></li>
				<?php endif ?>

			<?php endif ?>
		<?php endif ?>

		<li class="a-login">
			<?php include_partial("a/login") ?>
		</li>		

	</ul>
 
</div>

<?php if (aTools::isPotentialEditor() && $page): ?>

<?php include_partial('a/historyBrowser', array('page' => $page)) ?>

<div class="a-page-overlay"></div>

<script type="text/javascript">
	$(document).ready(function() {
				
		aMenuToggle('#a-page-settings-button', $('#a-page-settings-button').parent(), '', true);
		
		var aPageSettingsButton = $('#a-page-settings-button');		
		aPageSettingsButton.click(function() {
		 $.ajax({
				type:'POST',
				dataType:'html',
				success:function(data, textStatus){
					$('#a-page-settings').html(data);
				},
				complete:function(XMLHttpRequest, textStatus){
					aUI('#a-page-settings');
				},
				url:'/admin/a/settings/id/<?php echo $page->id; ?>'
			});	
		});
	});
</script>
<?php endif ?>