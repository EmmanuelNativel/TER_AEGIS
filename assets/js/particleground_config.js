$(window).load(function() {

    $('#particles').particleground({
        minSpeedX: 0.1,
        maxSpeedX: 0.7,
        minSpeedY: 0.1,
        maxSpeedY: 0.7,
        directionX: 'center', // 'center', 'left' or 'right'. 'center' = dots bounce off edges
        directionY: 'center', // 'center', 'up' or 'down'. 'center' = dots bounce off edges
        density: 10000, // How many particles will be generated: one particle every n pixels
        dotColor: 'rgba(255,255,255,0.5)',
        lineColor: 'rgba(255,255,255,0.5)',
        particleRadius: 7, // Dot size
        lineWidth: 1,
        curvedLines: false,
        proximity: 100, // How close two dots need to be before they join
        // parallax: false
    });

    $('.intro').css({
      'margin-top': -($('#particles').height() / 2),
      'position': 'relative'
    });
    $('.slides .intro').css({
      'margin-top': -($('#particles').height()),
      'position': 'relative'
    });

});

$(window).resize(function() {
  $('.intro').css({
    'margin-top': -($('#particles').height() / 2),
    'position': 'relative'
  });

  $('.slides .intro').css({
    'margin-top': -($('#particles').height()),
    'position': 'relative'
  });
});
