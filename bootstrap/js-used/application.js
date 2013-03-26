// NOTICE!! DO NOT USE ANY OF THIS JAVASCRIPT
// IT'S ALL JUST JUNK FOR OUR DOCS!
// ++++++++++++++++++++++++++++++++++++++++++

!function ($) {

  $(function(){

    $('#timepicker1').timepicker();
    $('#timepicker2').timepicker();

    $('#begin_date').datepicker();    

    $('#end_date').datepicker();

    $('tr[rel=tooltip]').tooltip()

    $('.tooltip-test').tooltip()
    $('.popover-test').popover()

    // popover demo
    $("select[rel=popover]")
      .popover()
      .click(function(e) {
        e.preventDefault()
      })

    $("a[rel=popover]")
      .popover()
      .click(function(e) {
        e.preventDefault()
      })


   $("button[rel=popover]")
      .popover();

    $("input[rel=popover]")
      .popover()
      .click(function(e) {
        e.preventDefault()
      })

    $('fileupload').fileupload()

    if ( $('#download_password').exists()) {
        $('#download_password').click(function(){
            var password = $('#password').val();
            var ha1 = $('#ha1').val();
            var ha1b = $('#ha1b').val();
            var username = $('#username').val();
            var domain = $('#domain').val();
            var str = username + ":"+
                domain + ":" +
                password;

            var ha_calc = MD5(str);
            //console.log(username+" "+ha1+" "+ha_calc);

            if (ha1 === ha_calc) {
                $('#java_buttons').removeClass('hide');
                $('#password_download').addClass('hide');
                console.log($('[name=file_content]').val());
                var content = decodeURIComponent($('[name=file_content]').val());
                //content = decodeURIComponent(content);
                console.log(content);
                var obj= $.parseJSON(content);
                obj.password=password;
                console.log(obj);
                var new_content= JSON.stringify(obj);
                console.log(new_content);
                new_content= encodeURIComponent(new_content);
                $('[name=file_content]').val(new_content);
                console.log(new_content);
            } else {
                $('#pass_group').addClass('error');
                $('#help-text').remove();
                $('#controls_password').append('<span id="help-text" class="help-inline">Entered password does not match your account</span>');
            }

            return false;
        });
    }

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
