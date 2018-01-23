<?php
# @Author: SPEDI srl
# @Date:   19-01-2018
# @Email:  sviluppo@spedi.it
# @Last modified by:   SPEDI srl
# @Last modified time: 2018-01-22T18:11:45+01:00
# @License: GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
# @Copyright: Copyright (C) SPEDI srl

// no direct access
defined('_JEXEC') or die ('Restricted access');
$id = 'hero-fade-carousel-'.$module->id;
?>
<!-- Swiper -->
<?php if($slides): ?>
<div class="swiper-container hero-fade-carousel" id="<?php echo $id ?>" style="height: <?php echo $image_height ?>">
  <div class="swiper-wrapper">
    <?php foreach ($slides as $slide) : ?>
    <div class="swiper-slide" style="background-image:url(<?php echo $slide->image ?>)">
      <div class="caption-slider">
        <h1 class="animated slideInLeft"><?php echo $slide->title ?></h1>
        <?php echo $slide->description ?>
        <?php if(!is_null($slide->params->get('link_type'))) : ?>
        <p class="mt-3">
          <a href="<?php echo $slide->link ?>" target="<?php echo $slide->params->get('link_target') ?>" class="btn btn-outline-light">Scopri di più <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
        </p>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <!-- Add Pagination -->
  <div class="swiper-pagination swiper-pagination-white"></div>
  <!-- Add Arrows -->
  <div class="swiper-button-next swiper-button-white"></div>
  <div class="swiper-button-prev swiper-button-white"></div>
</div>

<?php
$document->addScriptDeclaration("
	jQuery(document).ready(function($){

    var swiper = new Swiper('#".$id."', {
      spaceBetween: 30,
      effect: 'fade',
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
      autoplay: {
        delay: 2500,
        disableOnInteraction: false,
      },

    });


    swiper.on('slideChange', function () {
      $('#".$id." .swiper-slide').each(function () {
          if ($(this).index() === swiper.activeIndex) {
              $(this).find('.caption-slider > h1').fadeIn(300).addClass('slideInLeft');
          }
          else {
              $(this).find('.caption-slider > h1').removeClass('slideInLeft');
          }
      });
    });


	})
");
?>
<?php endif; ?>
