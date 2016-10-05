<!-- Content to render -->

<?= $this->render($content) ?>

<!-- Scripts import and slick configuration -->

<script src="https://code.jquery.com/jquery-2.2.0.min.js"></script>
<script src="../../vendor/slick/slick.js" charset="utf-8"></script>
<script>
    
    $(".slick").slick({
        centerMode: true,
        centerPadding: '60px',
        dots: true,
        arrows: false,
        infinite: true,
        slidesToShow: 2,
        slidesToScroll: 2,
        responsive: [
        {
            breakpoint: 980,
            settings: {
              centerPadding: '60px',
              slidesToShow: 1,
              slidesToScroll: 1
            }
        }
        ]
    });
    
</script>
