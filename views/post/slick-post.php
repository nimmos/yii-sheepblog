<!-- CSS for this particular element -->

<style>

    .slider {
        width: 100%;
        height: 5%;
        margin-top: 5%;
        margin-right: auto;
        margin-left: auto;
    }

    .slick-slide {
        margin: 0px 20px 20px;
        width: 100%;
    }

    .slick-slide img {
        alignment-adjust: middle;
        margin: auto;
    }
    
    .slick-prev,
    .slick-next
    {
        width: 0px;
    }

    .slick-prev:before,
    .slick-next:before {
        color: black;
    }
    
    .slick-dots li.slick-active button:before,
    .slick-dots li button:before {
        color: black;
    }
    
    .slider img {
        border-radius: 6px;
    }
    
    .slider a {
        width: 100%;
    }
</style>

<!-- Content -->

<?php if(count($images)!=0): ?>

<div>
    <h4><hr><strong>Gallery</strong></h4>
</div>

<section class="center-mode slider">
    
    <?php foreach($images as $imagelink): ?>
        <div>
            <a href="<?= $imagelink?>">
                <img src="<?= $imagelink?>" width="150" height="150">
            </a>
        </div>
    <?php endforeach; ?>
</section>

<?php endif; ?>

<!-- Scripts import and slick configuration -->

<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script src="//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.min.js"></script>
<script>
    
    $(".center-mode").slick({
        
        centerMode: false,
        adaptiveHeight: true,
        centerPadding: '60px',
        dots: true,
        arrows: false,
        infinite: true,
        slidesToShow: 5,
        slidesToScroll: 3,
        responsive: [
        {
            breakpoint: 980,
            settings: {
              centerPadding: '60px',
              slidesToShow: 3,
              slidesToScroll: 3
            }
        }
        ]
    });
    
</script>
