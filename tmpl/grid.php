<?php
# @Author: SPEDI srl
# @Date:   19-01-2018
# @Email:  sviluppo@spedi.it
# @Last modified by:   SPEDI srl
# @Last modified time: 19-01-2018
# @License: GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
# @Copyright: Copyright (C) SPEDI srl

defined('_JEXEC') or die ('Restricted access');
$id = 'gid-'.$module->id;
if(strpos($image_height, 'px') === false){
  // è in %
  echo JLayoutHelper::render('joomla.content.message.message_danger', JText::_("Specifica un'altezza in px per questo layout."));
} else{
  // è in px
  $image_height = substr($image_height, 0, strpos($image_height, 'px'));
  $big   = $image_height.'px';
  $small = ($image_height/2).'px';
}
?>
<?php if($slides >= 5) : ?>
<section class="hero-grid <?= $id ?>">
  <div class="container">
    <div class="row">

      <?php foreach ($slides as $k => $slide) : ?>
        <?php if($k == 0) : //the first slide ?>
          <div class="col-12 col-sm-12 col-md-6 hero-grid-left" style="height: <?php echo $big ?>">
            <div class="cover" style="background-image: url(<?php echo $slide->image ?>)"></div>
            <div class="caption">
              <?php if($slide->title) : ?>
                <h3><?php echo $slide->title ?></h3>
              <?php endif; ?>
              <?php if($slide->description) : ?>
                <?php echo $slide->description ?>
              <?php endif; ?>
            </div>
            <?php if(!is_null($slide->params->get('link_type'))) : ?>
              <a href="<?php echo $slide->link ?>" target="<?php echo $slide->params->get('link_target') ?>" class="link"></a>
            <?php endif; ?>
          </div>

          <div class="col-12 col-sm-12 col-md-6 hero-grid-right"> <!-- open #2 -->
            <div class="row"> <!-- open #1 -->
        <?php endif; ?>

        <?php if($k > 0) : ?>
          <div class="col-6 hero-grid-right-smallbox" style="height: <?php echo $small ?>">
            <div class="cover" style="background-image: url(<?php echo $slide->image ?>)"></div>
            <div class="caption">
              <?php if($slide->title) : ?>
                <h3><?php echo $slide->title ?></h3>
              <?php endif; ?>
              <?php if($slide->description) : ?>
                <?php echo $slide->description ?>
              <?php endif; ?>
            </div>
            <?php if(!is_null($slide->params->get('link_type'))) : ?>
              <a href="<?php echo $slide->link ?>" target="<?php echo $slide->params->get('link_target') ?>" class="link"></a>
            <?php endif; ?>
          </div>
        <?php endif; ?>

      <?php endforeach; ?>

        </div> <!-- close #1 -->
      </div> <!-- close #2 -->


    </div><!-- end .row -->
  </div>
</section>
<?php endif; ?>
