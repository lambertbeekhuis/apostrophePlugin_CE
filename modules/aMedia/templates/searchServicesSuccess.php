<?php use_helper('a') ?>
<?php slot('body_class') ?>a-media<?php end_slot() ?>

<?php slot('a-page-header') ?>
	<?php include_partial('aMedia/mediaHeader', array('uploadAllowed' => $uploadAllowed, 'embedAllowed' => $embedAllowed)) ?>
<?php end_slot() ?>
<?php include_component('aMedia', 'browser') ?>

<div class="a-media-library">
  <h3><?php echo a_('Search Services') ?></h3>
  <form method="POST" action="<?php echo url_for('aMedia/searchServices') ?>">
    <?php echo $form ?>
    <ul class="a-ui a-controls" id="a-media-video-add-by-embed-form-submit">
      <li><input type="submit" value="<?php echo a_('Go') ?>" class="a-btn a-submit" /></li>
      <li>
  			<?php echo link_to('<span class="icon"></span>'.a_("Cancel"), 'aMedia/resume', array("class" => "a-btn icon a-cancel")) ?>
  		</li>
    </ul>
  </form>
  <?php if (isset($pager)): ?>
    <?php include_partial('aMedia/videoSearch', array('url' => $url, 'pager' => $pager, 'service' => $service)) ?>
  <?php endif ?>
</div>