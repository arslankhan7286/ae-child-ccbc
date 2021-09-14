<?php 
    $meta = new \Ardent\Wp\Meta;
    $title = get_the_title();
    $permalink = get_the_permalink();
    $first_name = $meta->first_name;
    $last_name = $meta->last_name;
    $church_title = $meta->title;
    $email = $meta->email;
    $phone = $meta->phone;
    $feat_image = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID(), 'large'));
    $htime = md5((string)microtime() . (string)rand() . $email);
    
?>

<div class="elementor-element elementor-element-2254ffc1 elementor-column elementor-col-33 elementor-inner-column" data-id="2254ffc1" data-element_type="column"><div class="elementor-column-wrap  elementor-element-populated">
<div class="elementor-widget-wrap">

<div class="elementor-element elementor-element-7295ab22 elementor-widget elementor-widget-image staff-head-widget" data-id="7295ab22" data-element_type="widget" data-widget_type="image.default">
<div class="elementor-widget-container">
<div class="elementor-image staff-headshot-image"> 
<a href="#lc<?php echo $htime; ?>" data-rel="lightcase"> 
<img width="768" height="960" src="<?php echo $feat_image ?>" class="attachment-medium_large size-medium_large" alt="" loading="lazy" srcset="" sizes="(max-width: 768px) 100vw, 768px"> 
</a>
</div>
</div>
</div>

<div class="elementor-element elementor-element-6ad21890 elementor-widget elementor-widget-heading staff-title-widget" data-id="6ad21890" data-element_type="widget" data-widget_type="heading.default">
<div class="elementor-widget-container">
<h3 class="elementor-heading-title elementor-size-default staff-heading-title"><?php echo $title ?></h3>
</div>
</div>

<div class="elementor-element elementor-element-89aa58d elementor-widget elementor-widget-spacer staff-spacer" data-id="89aa58d" data-element_type="widget" data-widget_type="spacer.default">
<div class="elementor-widget-container">
<div class="elementor-spacer">
<div class="elementor-spacer-inner">
</div>
</div>
</div>
</div>

<div class="elementor-element elementor-element-59a1483 elementor-widget elementor-widget-spacer staff-spacer" data-id="59a1483" data-element_type="widget" data-widget_type="spacer.default">
<div class="elementor-widget-container">
<div class="elementor-spacer">
<div class="elementor-spacer-inner">
</div>
</div>
</div>
</div>
<?php if($church_title){ ?>
<div class="elementor-element elementor-element-745e5ac6 elementor-widget elementor-widget-heading staff-ct-widget" data-id="745e5ac6" data-element_type="widget" data-widget_type="heading.default">
<div class="elementor-widget-container">
<h3 class="elementor-heading-title elementor-size-default staff-church-title"><?php echo $church_title ?></h3>
</div>
</div>
<?php } ?>
<?php if($phone){ ?>
<div class="elementor-element elementor-element-416ffba elementor-align-center elementor-widget elementor-widget-button staff-phone-widget" data-id="416ffba" data-element_type="widget" data-widget_type="button.default">
<div class="elementor-widget-container">
<div class="elementor-button-wrapper"> 
<a href="tel:<?php echo $phone ?>" class="elementor-button-link elementor-button elementor-size-sm staff-phone-number" role="button"> 
<span class="elementor-button-content-wrapper"> 
<span class="elementor-button-text"><?php echo $phone ?></span> 
</span> 
</a>
</div>
</div>
</div>
<?php } ?>
<div class="elementor-element elementor-element-7b1e5fa animated-fast elementor-widget elementor-widget-heading animated fadeIn" data-id="7b1e5fa" data-element_type="widget" data-settings="{&quot;_animation&quot;:&quot;fadeIn&quot;}" data-widget_type="heading.default">
<div class="elementor-widget-container">
<h3 class="elementor-heading-title elementor-size-default staff-read-more">
<a href="#lc<?php echo $htime; ?>" data-rel="lightcase">READ MORE &gt;</a>
<div id="lc<?php echo $htime; ?>" style="display:none;">
    <div class="content">
        <div class="name"><?php echo $first_name.' '.$last_name; ?></div>
        <div class="church_title"><?php echo $church_title; ?></div>
        <div class="content"><?php the_content(); ?></div>
    </div>
    <div class="image">
        <img width="768" height="960" src="<?php echo $feat_image ?>" class="attachment-medium_large size-medium_large" alt="" srcset="" sizes="(max-width: 768px) 100vw, 768px"> 
    </div>
</div>
</h3>
</div>
</div>

</div>
</div>
</div>