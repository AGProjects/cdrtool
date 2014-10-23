// NOTICE!! DO NOT USE ANY OF THIS JAVASCRIPT
// IT'S ALL JUST JUNK FOR OUR DOCS!
// ++++++++++++++++++++++++++++++++++++++++++

!function ($) {

  $(function(){

    jQuery.fn.exists = function(){
      return this.length>0;
    };

    if ( $('#timepicker1').exists()) {
      $('#timepicker1').timepicker();
    }

    if ( $('#timepicker2').exists()) {
      $('#timepicker2').timepicker();
    }

    if ( $('#begin_date').exists()) {
      $('#begin_date').datepicker();
    }

    if ( $('#thorMap').exists()) {

      var draw = SVG('thorMap');

      draw.viewbox(0, 0, thorData.imgsize, thorData.imgsize);

      // draw.line(0,315,630,315).stroke({ width: 1, color:'grey'});
      // draw.line(315,0,315,630).stroke({ width: 1, color:'grey'});

      draw.circle()
        .radius(315*0.6)
        .fill('none')
        .stroke({
          width: 10,
          color:'#ebebeb'
        })
        .cy(315)
        .cx(315);

      cloud = draw.text("\uf0c2")
        .font({
          family: 'FontAwesome',
          size: '100'
        })
        .y(265)
        .x(265)
        .fill('#bbb');

      var dns_nodes = Object.keys(thorData.dns_managers).length;

      index = 0 ;
      console.log(thorData);

      $.each(thorData.dns_managers, function( key, value ) {

            // draw.text("\ue9a4").font({
            //     family: 'picol',
            //     size: '40'
            // })
            // .cy(20)
            // .cx(610-(40*dns_nodes)+(index*45))
            // .fill('#88a2d2');


            // draw.text("DNS"+(index+1)).font({
            //     family: 'Verdana',
            //     size: '10'
            // })
            // .cy(50)
            // .cx(610-(40*dns_nodes)+(index*45));

        draw.circle()
          .radius(4)
          .fill('green')
          .cx(490)
          .cy(21+(index*12));

        var hostname = key;

        if (thorData.hostnames[key]){
          hostname = thorData.hostnames[key];
        }

        draw.text("DNS"+(index+1)+': '+hostname)
          .font({
            family: 'Verdana',
            size: '10'
          })
          .cy(20+(index*12))
          .x(500);
        index++;
      });

      var sip_proxies = Object.keys(thorData.node_statistics).length + Object.keys(thorData.conference_servers).length + Object.keys(thorData.voicemail_servers).length;

        //var sip_proxies = Object.keys(thorData.node_statistics).length
      var counter= 0;

      $.each(thorData.voicemail_servers, function( key, value ) {
        position = ((2/sip_proxies)*counter);
        counter++;

          // draw.circle()
          //   .radius(10)
          //   .fill('white')
          //   .stroke({color:'green', width:2})
          //   .cx(315+(315*0.6)*Math.sin(position*Math.PI))
          //   .cy(315+(315*0.6)*Math.cos(position*Math.PI));

        draw.rect(32,24)
          .fill('white')
          .stroke({
            color:'green',
            width:2
          })
          .radius(5)
          .x(315).y(315).animate(500, SVG.easing.quartIn)
          .cx(315+(315*0.6)*Math.sin(position*Math.PI))
          .cy(315+(315*0.6)*Math.cos(position*Math.PI));

        draw.text("\uf0e0")
          .font({
            family: 'FontAwesome',
            size: '14',
          })
          .x(315).y(315).animate(500, SVG.easing.quartIn)
          .cx(315+(315*0.6)*Math.sin(position*Math.PI))
          .cy(315+(315*0.6)*Math.cos(position*Math.PI));

        var hostname = key;

        if (thorData.hostnames[key]){
          hostname = thorData.hostnames[key];
        }

        align='start';
        x=0;
        y=0;
        if (position  === 0.5 ) {
          x= -10;
          align='start';
        } else if (position === 1.5) {
          x= 10;
          align='end';
        } else if ((position > 1 && position < 1.5 )|| (position > 1.5 && position < 2)) {
          x = 25;
          align='end';
        } else if ((position > 0 && position < 0.5 )|| (position > 0.5 && position < 1)) {
          x = -25;
        } else if (position === 1 ) {
          align='middle';
          y=20;
        } else if (position === 0 ) {
          align='middle';
          y=-25;
        }

        draw.text(hostname ).font({
            family: 'Verdana-bold',
            size: '10',
            anchor: align,
          })
          .x(315).y(315).animate(500, SVG.easing.quartIn)
          .cy(315+(315*0.6)*Math.cos(position*Math.PI)-y).x(
              315+(315*0.6)*Math.sin(position*Math.PI)-x).fill('#5177bd');

        });

        $.each(thorData.conference_servers, function( key, value ) {
          position = ((2/sip_proxies)*counter);
          counter++;

          // draw.circle()
          //   .radius(10)
          //   .fill('white')
          //   .stroke({color:'green', width:2})
          //   .cx(315+(315*0.6)*Math.sin(position*Math.PI))
          //   .cy(315+(315*0.6)*Math.cos(position*Math.PI));

          draw.rect(32,24).fill('white').stroke({color:'green', width:2}).radius(5)
          .x(315).y(315).animate(500, SVG.easing.quartIn).cx(315+(315*0.6)*Math.sin(position*Math.PI))
            .cy(315+(315*0.6)*Math.cos(position*Math.PI));

          draw.text("\uf0c0").font({
            family: 'FontAwesome',
            size: '15',
          })
          .x(315).y(315).animate(500, SVG.easing.quartIn)
          .cx(315+(315*0.6)*Math.sin(position*Math.PI))
          .cy(315+(315*0.6)*Math.cos(position*Math.PI));

          var hostname = key;

          if (thorData.hostnames[key]){
            hostname = thorData.hostnames[key];
          }

        align='start';
        x=0;
        y=0;
         if (position  === 0.5 ) {
            x= -10;
            align='start';
          } else if (position === 1.5) {
            x= 10;
            align='end';
          } else if ((position > 1 && position < 1.5 )|| (position > 1.5 && position < 2)) {
            x = 25;
            align='end';
          }
          else if ((position > 0 && position < 0.5 )|| (position > 0.5 && position < 1)) {
            x = -25;
          }
          else if (position === 1 ) {
            align='middle';
            y=20;
          } else if (position === 0 ) {
            align='middle';
            y=-20;
          }

          draw.text(hostname ).font({
            family: 'Verdana-bold',
            size: '10',
            anchor: align,
          })
          .x(315).y(315).animate(500, SVG.easing.quartIn)
          .cy(315+(315*0.6)*Math.cos(position*Math.PI)-y)
          .x(315+(315*0.6)*Math.sin(position*Math.PI)-x).fill('#5177bd');

        });

        $.each(thorData.node_statistics, function( key, value ) {
          position = ((2/sip_proxies)*counter);
          counter++;

          // draw.text("\ue9a4").font({
          //   family: 'picol',
          //   size: '40'
          // }).y(290+(315*0.69)*Math.sin(position*Math.PI)).x(
          //     300+(315*0.69)*Math.cos(position*Math.PI)).fill('#5177bd');

          draw.rect(32,24).fill('white').stroke({color:'green', width:2}).radius(4)
          .x(315).y(315).animate(500, SVG.easing.quartIn)
          .cx(315+(315*0.6)*Math.sin(position*Math.PI))
            .cy(315+(315*0.6)*Math.cos(position*Math.PI));

          // draw.circle()
          //   .radius(10)
          //   .fill('white')
          //   .stroke({color:'green', width:2})
          //   .cx(315+(315*0.6)*Math.sin(position*Math.PI))
          //   .cy(315+(315*0.6)*Math.cos(position*Math.PI));

        if (thorData.sip_proxies[key]){
            draw.text("\uf0b2").font({
            family: 'FontAwesome',
            size: '15',
          })
              .x(315).y(315).animate(500, SVG.easing.quartIn)
            .cx(315+(315*0.6)*Math.sin(position*Math.PI))
            .cy(315+(315*0.6)*Math.cos(position*Math.PI));
          }
        x =0 ;
        y = 0;
          // if (position > 0 && position < 0.5) {
          //   x = -45;
          //   y = -20;
          // } else if (position === 0.5) {
          //   x = 0;
          //   y = -55;
          // } else if (position > 0.5 && position < 1) {
          //   x = 120;
          //   y = -15;
          // } else if (position === 1) {
          //   x = 30;
          //   y = 15;
          // } else if (position > 1 && position < 1.5) {
          //   x = 120;
          //   y = -20;
          // } else if (position > 1.5 && position < 2) {
          //   x = -45;
          //   y = -20;
          // } else {
          //   x = 0;
          //   y = 15;
          // }

          var accounts = '';
          if (thorData.node_statistics[key].online_accounts ){
            accounts= thorData.node_statistics[key].online_accounts+' accounts';
          }

          var hostname = key;

          if (thorData.hostnames[key]){
            hostname = thorData.hostnames[key];
          }

        align='start';
         if (position  === 0.5 ) {
            x= -10;
            align='start';
          } else if (position === 1.5) {
            x= 10;
            align='end';
          } else if ((position > 1 && position < 1.5 )|| (position > 1.5 && position < 2)) {
            x = 25;
            align='end';
          }
          else if ((position > 0 && position < 0.5 )|| (position > 0.5 && position < 1)) {
            x = -25;
          }
          else if (position === 1 ) {
            align='middle';
            y=30;
          } else if (position === 0 ) {
            align='middle';
            y=-30;
          }

          draw.text(hostname+"\n"+accounts ).font({
            family: 'Verdana-bold',
            size: '10',
            anchor: align,
          }).x(315).y(315).animate(500, SVG.easing.quartIn).cy(315+(315*0.6)*Math.cos(position*Math.PI)-y).x(
              315+(315*0.6)*Math.sin(position*Math.PI)-x).fill('#5177bd');

align='start';
        x =0 ;
        y = 0;
          if (position === 0.5) {
            align='end';
          }else if (position === 1.5 ) {
            align='start';
            x= -50;
          } else if ((position > 1 && position < 1.5 )|| (position > 1.5 && position < 2)) {
            align='start';
            x= -50;
          }
          else if ((position > 0 && position < 0.5 )|| (position > 0.5 && position < 1)) {
            align='end';
          }
          else if (position === 1 ) {
            //align='middle';
            y=-24;
          } else if (position === 0 ) {
            //align='middle';
            y=25;
          }

          if (thorData.node_statistics[key].sessions) {
            draw.text(thorData.node_statistics[key].sessions +' sessions').font({
            family: 'Verdana',
            size: '10',
            anchor: align,
          }).x(315).y(315).animate(400, SVG.easing.quartIn)
            .cy(315+(315*0.6)*Math.cos(position*Math.PI)-y).cx(
              315+(315*0.6)*Math.sin(position*Math.PI)-x).fill('#5177bd');
          }
        });
    }

    if ( $('#end_date').exists()) {
        $('#end_date').datepicker();
    }
    $('tr[rel=tooltip]').tooltip();

    $('.tooltip-test').tooltip();
    $('.popover-test').popover();

    // popover demo
    $("select[rel=popover]")
      .popover()
      .click(function(e) {
        e.preventDefault();
      });

    $("a[rel=popover]")
      .popover()
      .click(function(e) {
        e.preventDefault();
      });


   $("button[rel=popover]")
      .popover();

    $("input[rel=popover]")
      .popover()
      .click(function(e) {
        e.preventDefault();
      });

    $("textarea[rel=popover]")
      .popover()
      .click(function(e) {
        e.preventDefault();
      });

   if ($('fileupload').exists()){
        $('fileupload').fileupload();
   }

    // if ( $('#download_password').exists()) {
    //     $('#download_password').click(function(){
    //         var password = $('#password').val();
    //         var ha1 = $('#ha1').val();
    //         var ha1b = $('#ha1b').val();
    //         var username = $('#username').val();
    //         var domain = $('#domain').val();
    //         var str = username + ":"+
    //             domain + ":" +
    //             password;

    //         var ha_calc = MD5(str);

    //         if (ha1 === ha_calc) {
    //             $('#java_buttons').removeClass('hide');
    //             $('#password_download').addClass('hide');
    //             var content = decodeURIComponent($('[name=file_content]').val());
    //             var obj= $.parseJSON(content);
    //             obj.password=password;
    //             var new_content= JSON.stringify(obj);
    //             new_content= encodeURIComponent(new_content);
    //             $('[name=file_content]').val(new_content);
    //         } else {
    //             $('#pass_group').addClass('error');
    //             $('#help-text').remove();
    //             $('#controls_password').append('<span id="help-text" class="help-inline">Entered password does not match your account</span>');
    //         }

    //         return false;
    //     });
    // }

    // request built javascript
    $('.download-btn').on('click', function () {

      var css = $("#components.download input:checked")
            .map(function () { return this.value })
            .toArray()
        , js = $("#plugins.download input:checked")
            .map(function () { return this.value })
            .toArray()
        , vars = {}
        , img = ['glyphicons-halflings.png', 'glyphicons-halflings-white.png']

    $("#variables.download input")
      .each(function () {
        $(this).val() && (vars[ $(this).prev().text() ] = $(this).val())
      })

      $.ajax({
        type: 'POST'
      , url: /\?dev/.test(window.location) ? 'http://localhost:3000' : 'http://bootstrap.herokuapp.com'
      , dataType: 'jsonpi'
      , params: {
          js: js
        , css: css
        , vars: vars
        , img: img
      }
      })
    })
  });

// Modified from the original jsonpi https://github.com/benvinegar/jquery-jsonpi
$.ajaxTransport('jsonpi', function(opts, originalOptions, jqXHR) {
  var url = opts.url;

  return {
    send: function(_, completeCallback) {
      var name = 'jQuery_iframe_' + jQuery.now()
        , iframe, form

      iframe = $('<iframe>')
        .attr('name', name)
        .appendTo('head')

      form = $('<form>')
        .attr('method', opts.type) // GET or POST
        .attr('action', url)
        .attr('target', name)

      $.each(opts.params, function(k, v) {

        $('<input>')
          .attr('type', 'hidden')
          .attr('name', k)
          .attr('value', typeof v == 'string' ? v : JSON.stringify(v))
          .appendTo(form)
      })

      form.appendTo('body').submit()
    }
  }
});

Highcharts.theme = {

    chart: {
        backgroundColor: 'transparent',
        plotBackgroundColor: 'transparent',
        plotShadow: false,
        plotBorderWidth: 0
    },
    title: {
        style: {
            color: '#5177bd',
            font: 'bold 16px Verdana, Arial,Telex, sans-serif'
        }
    },
    subtitle: {
        style: {
            color: '#5177bd',
            font: 'bold 12px Verdana, Arial,Telex, sans-serif'
        }
    },
    xAxis: {
        minorTickInterval: 'auto',
        //majorTickInterval: '',
        lineColor: '#000',
        lineWidth: 1,
        tickWidth: 1,
        tickColor: '#000',
        labels: {
            style: {
                color: '#000',
                font: '10px Verdana, Arial,Telex, sans-serif'
            }
        },
    },
    yAxis: {
        minorTickInterval: 'auto',
        lineColor: '#000',
        lineWidth: 1,
        tickWidth: 1,
        tickColor: '#000',
        labels: {
            style: {
                color: '#000',
                font: '10px Verdana, Arial,Telex, sans-serif'
            }
        },
    },
    legend: {
        itemStyle: {
            font: '11px Verdana, Arial,Telex, sans-serif',
            color: '#333'
        },
        itemHoverStyle: {
            color: '#039'
        },
        itemHiddenStyle: {
            color: 'gray'
        }
    },


    navigation: {
        buttonOptions: {
            theme: {
                stroke: '#e6e6e6'
            }
        }
    }
};

// Apply the theme
var highchartsOptions = Highcharts.setOptions(Highcharts.theme);



}(window.jQuery)
